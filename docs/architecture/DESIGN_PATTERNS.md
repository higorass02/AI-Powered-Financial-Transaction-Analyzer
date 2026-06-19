# 🎨 Design Patterns & Architectural Patterns

> **Parte:** ARCHITECTURE  
> **Propósito:** Documentar padrões de design implementados  
> **Última atualização:** Junho 2026

---

## 🏛️ Padrões Arquiteturais

### 1. **Service Layer Pattern** (Principal)

A lógica de negócio fica em **Services**, não em Controllers ou Models.

```
Controller → Service → Repository → Database
                    ↘ IA Service ↙
                    ↘ Other Services ↙
```

**Por quê?**
- ✅ Testável (moque o Service)
- ✅ Reutilizável (use em múltiplos Controllers)
- ✅ Mantível (lógica centralizada)

**Exemplo:**

```php
// ❌ ERRADO: Lógica em Controller
class TransactionController {
    public function store(Request $request) {
        $transaction = Transaction::create($request->all());
        // análise IA aqui... muita lógica!
        return $transaction;
    }
}

// ✅ CORRETO: Lógica em Service
class TransactionController {
    public function store(StoreTransactionRequest $request, TransactionService $service) {
        $transaction = $service->create($request->validated());
        return response()->json($transaction, 201);
    }
}

class TransactionService {
    public function create(array $data): Transaction {
        // 1. Validar
        // 2. Categorizar com IA
        // 3. Salvar
        // 4. Disparar eventos
        // 5. Cache invalidate
        return $transaction;
    }
}
```

### 2. **Repository Pattern** (Data Access)

Abstrai acesso ao banco de dados.

```
Service → Repository → Eloquent → Database
                    ↘ Cache ↙
```

**Por quê?**
- ✅ Trocar banco sem mudar Service
- ✅ Queries complexas isoladas
- ✅ Cache centralizado

**Exemplo:**

```php
// Service (não sabe como dados são obtidos)
class AnalyticsService {
    public function __construct(TransactionRepository $repository) {
        $this->repository = $repository;
    }
    
    public function getMonthlyStats($userId) {
        $transactions = $this->repository->findLastMonth($userId);
        // analisa dados...
    }
}

// Repository (sabe como obter dados)
class TransactionRepository {
    public function findLastMonth($userId) {
        return Transaction::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonth())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

### 3. **Command/Action Pattern** (Complex Operations)

Para operações multi-step com transações.

```
Controller → Action → Service → Repository
                   ↘ Transação DB ↙
```

**Por quê?**
- ✅ Rollback automático
- ✅ Multi-step coordination
- ✅ Reutilizável

**Exemplo:**

```php
// ImportTransactionsAction.php
class ImportTransactionsAction {
    public function execute(UploadedFile $file): int {
        return DB::transaction(function() use ($file) {
            $rows = $this->parseCSV($file);
            
            foreach ($rows as $row) {
                $transaction = $this->createTransaction($row);
                $this->analyzeWithAI($transaction);
                $this->validateBusiness($transaction);
            }
            
            // Se tudo OK, tudo é commitado
            // Se erro em qualquer passo, TUDO é rolled back
            return count($rows);
        });
    }
}

// Usar:
class ImportController {
    public function store(Request $request, ImportTransactionsAction $action) {
        $count = $action->execute($request->file('csv'));
        return response()->json(['imported' => $count]);
    }
}
```

### 4. **Event-Driven Architecture**

Desacoplamento entre componentes.

```
Service dispara:    TransactionCreated
                           ↓
Listeners escutam:  LogTransactionCreated
                    NotifyUserAboutTransaction
                    UpdateCacheService
```

**Por quê?**
- ✅ Desacoplado
- ✅ Fácil adicionar listeners sem alterar Service
- ✅ Assíncrono com queues

**Exemplo:**

```php
// Disparar evento
class TransactionService {
    public function create(array $data) {
        $transaction = Transaction::create($data);
        
        // Dispara evento
        TransactionCreated::dispatch($transaction);
        
        return $transaction;
    }
}

// Listener 1: Log
class LogTransactionCreated {
    public function handle(TransactionCreated $event) {
        Log::info("Transaction created: {$event->transaction->id}");
    }
}

// Listener 2: Notificar
class NotifyUserAboutTransaction {
    public function handle(TransactionCreated $event) {
        // Enviar email, push notification, etc
    }
}

// Listener 3: Analytics
class UpdateAnalyticsCache {
    public function handle(TransactionCreated $event) {
        Cache::forget("user_{$event->transaction->user_id}_stats");
    }
}
```

---

## 🎯 Padrões de Design Específicos

### 1. **Dependency Injection (DI)**

Injetar dependências, não instanciar.

```php
// ❌ ERRADO: Acoplado
class TransactionService {
    public function analyze($transaction) {
        $aiService = new AIAnalysisService(); // Acoplado!
        return $aiService->analyze($transaction);
    }
}

// ✅ CORRETO: Injetado
class TransactionService {
    public function __construct(AIAnalysisService $aiService) {
        $this->aiService = $aiService; // Laravel injeta
    }
    
    public function analyze($transaction) {
        return $this->aiService->analyze($transaction);
    }
}

// Laravel container resolve automaticamente
```

### 2. **Factory Pattern**

Criar objetos complexos.

```php
class TransactionFactory {
    public static function fromCSV(array $row): Transaction {
        return Transaction::create([
            'description' => trim($row['description']),
            'amount' => (float) $row['amount'],
            'type' => self::mapType($row['type']),
            'category' => self::guessCategory($row['description']),
            'date' => Carbon::parse($row['date']),
        ]);
    }
    
    private static function mapType($type) {
        return match($type) {
            'D' => 'debit',
            'C' => 'credit',
            'P' => 'pix',
            default => 'unknown'
        };
    }
}

// Usar:
$transaction = TransactionFactory::fromCSV($csvRow);
```

### 3. **Strategy Pattern**

Múltiplas estratégias intercambiáveis.

```php
// Interface
interface CategorizeStrategy {
    public function categorize(Transaction $transaction): string;
}

// Estratégia 1: Regex
class RuleBasedStrategy implements CategorizeStrategy {
    public function categorize(Transaction $transaction): string {
        // Match contra regras
    }
}

// Estratégia 2: IA
class AIStrategy implements CategorizeStrategy {
    public function categorize(Transaction $transaction): string {
        // Chamar Claude API
    }
}

// Usar a certa
class TransactionService {
    public function __construct(CategorizeStrategy $strategy) {
        $this->strategy = $strategy;
    }
    
    public function create(array $data) {
        $category = $this->strategy->categorize($data);
        // ...
    }
}

// Em production: CategorizeStrategy = AIStrategy
// Em testes: CategorizeStrategy = RuleBasedStrategy (mais rápido)
```

### 4. **Decorator Pattern**

Adicionar responsabilidades dinamicamente.

```php
// Service base
class AIAnalysisService {
    public function analyze(Transaction $transaction): array {
        // Análise básica
    }
}

// Decorador 1: Adiciona cache
class CachedAIAnalysisService implements AIAnalysisContract {
    public function __construct(AIAnalysisService $service) {
        $this->service = $service;
    }
    
    public function analyze(Transaction $transaction): array {
        return Cache::remember("ai_analysis_{$transaction->id}", 3600, fn() =>
            $this->service->analyze($transaction)
        );
    }
}

// Decorador 2: Adiciona logging
class LoggingAIAnalysisService implements AIAnalysisContract {
    public function __construct(AIAnalysisService $service) {
        $this->service = $service;
    }
    
    public function analyze(Transaction $transaction): array {
        Log::info("Starting AI analysis for transaction {$transaction->id}");
        $result = $this->service->analyze($transaction);
        Log::info("Finished AI analysis");
        return $result;
    }
}

// Stack decoradores:
// CachedAIAnalysisService → LoggingAIAnalysisService → AIAnalysisService
```

### 5. **Observer Pattern** (Events)

Visto acima no "Event-Driven Architecture"

---

## 💻 Padrões Frontend (Vue 3)

### 1. **Composition API Pattern**

Reutilizar lógica com composables.

```ts
// ❌ ERRADO: Lógica em componente
<script setup>
import { ref } from 'vue'
import { useTransactionStore } from '@/stores/transactions'

const store = useTransactionStore()
const loading = ref(false)
const error = ref(null)

const fetchTransactions = async () => {
    loading.value = true
    try {
        await store.fetch()
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
}

onMounted(() => fetchTransactions())
</script>

// ✅ CORRETO: Extrair para composable
// composables/useTransactions.ts
export const useTransactions = () => {
    const store = useTransactionStore()
    const loading = ref(false)
    const error = ref(null)
    
    const fetch = async () => {
        loading.value = true
        try {
            await store.fetch()
        } catch (e) {
            error.value = e.message
        } finally {
            loading.value = false
        }
    }
    
    return { loading, error, fetch, transactions: store.transactions }
}

// Usar em múltiplos componentes
<script setup>
const { loading, fetch, transactions } = useTransactions()
onMounted(() => fetch())
</script>
```

### 2. **Store Pattern (Pinia)**

Estado global gerenciado.

```ts
// stores/transactions.ts
import { defineStore } from 'pinia'

export const useTransactionStore = defineStore('transactions', {
    // Estado
    state: () => ({
        transactions: [],
        selectedId: null,
    }),
    
    // Getters (computed)
    getters: {
        total: (state) => state.transactions.reduce((sum, t) => sum + t.amount, 0),
        selected: (state) => state.transactions.find(t => t.id === state.selectedId),
    },
    
    // Actions (métodos)
    actions: {
        async fetch() {
            const response = await api.get('/transactions')
            this.transactions = response.data
        },
        
        select(id) {
            this.selectedId = id
        },
        
        async create(data) {
            const response = await api.post('/transactions', data)
            this.transactions.push(response.data)
        },
    }
})

// Usar em componente
<script setup>
const store = useTransactionStore()

onMounted(() => store.fetch())
</script>

<template>
  <div>
    <div v-for="t in store.transactions" :key="t.id">{{ t.description }}</div>
    <p>Total: {{ store.total }}</p>
  </div>
</template>
```

### 3. **Component Composition Pattern**

Componentes pequenos, compostos.

```
Dashboard.vue
├─ SummaryCards.vue
│  ├─ SummaryCard.vue (reutilizável)
│  └─ SummaryCard.vue
├─ TransactionTable.vue
│  └─ TransactionRow.vue (reutilizável)
└─ ChartsSection.vue
   ├─ SpendingChart.vue
   └─ TrendChart.vue

Cada componente tem UMA responsabilidade
```

### 4. **Slot Pattern**

Componentes flexíveis.

```vue
<!-- Modal.vue - genérico -->
<template>
  <div class="modal">
    <div class="modal-header">
      <slot name="header">Padrão header</slot>
    </div>
    <div class="modal-body">
      <slot>Conteúdo padrão</slot>
    </div>
    <div class="modal-footer">
      <slot name="footer">Padrão footer</slot>
    </div>
  </div>
</template>

<!-- Usar em TransactionForm.vue -->
<Modal>
  <template #header>
    <h2>Nova Transação</h2>
  </template>
  
  <form @submit="save">
    <!-- formulário -->
  </form>
  
  <template #footer>
    <button @click="close">Cancelar</button>
    <button @click="save" type="submit">Salvar</button>
  </template>
</Modal>
```

---

## 🔄 Fluxo de Dados (Data Flow)

### Request-Response Flow

```
1. FRONTEND
   └─ User action → Component event
   
2. SERVICE CALL
   └─ composable/store chama: api.post('/transactions', data)
   
3. BACKEND
   ├─ Route recebe: /api/transactions
   ├─ Controller valida: StoreTransactionRequest
   ├─ Service executa: TransactionService->create()
   └─ Repository salva: Transaction::create()
   
4. DATABASE
   └─ PostgreSQL: INSERT INTO transactions
   
5. RESPONSE
   └─ Controller retorna: response()->json($transaction, 201)
   
6. FRONTEND
   ├─ Store recebe resposta
   ├─ Store atualiza state
   └─ Componente re-renderiza (reatividade Vue)
```

### State Management Flow

```
User Action
    ↓
Componente (components/)
    ↓
Service/Composable (src/services/ ou src/composables/)
    ↓
Store Actions (stores/)
    ↓
API Call (src/services/api.ts)
    ↓
Backend API
    ↓
Store State atualizado
    ↓
Componente re-renderiza (reatividade automática)
```

---

## 🧪 Padrões de Teste

### 1. **AAA Pattern** (Arrange-Act-Assert)

```php
public function test_it_analyzes_transaction_correctly() {
    // Arrange: Preparar dados
    $transaction = Transaction::factory()->create([
        'description' => 'UBER BRASIL',
        'amount' => 45.50
    ]);
    
    // Act: Executar ação
    $analysis = $this->service->analyze($transaction);
    
    // Assert: Verificar resultado
    $this->assertEquals('Transportation', $analysis['category']);
    $this->assertGreaterThan(0.8, $analysis['confidence']);
}
```

### 2. **Mocking Pattern**

```php
public function test_service_uses_ai_correctly() {
    // Mock do AIService
    $aiServiceMock = $this->mock(AIAnalysisService::class)
        ->shouldReceive('analyze')
        ->once()
        ->andReturn(['category' => 'Food']);
    
    // Injetar mock
    $service = new TransactionService($aiServiceMock);
    
    // Testar
    $result = $service->create(['description' => 'Pizza']);
    
    // Verificar
    $this->assertEquals('Food', $result->category);
}
```

---

## 📊 Resumo de Padrões

| Padrão | Usado em | Propósito |
|--------|----------|-----------|
| **Service Layer** | Backend | Lógica centralizada |
| **Repository** | Backend | Acesso a dados |
| **Action** | Backend | Operações complex |
| **Event-Driven** | Backend | Desacoplamento |
| **Dependency Injection** | Backend | Testabilidade |
| **Factory** | Backend | Criar objetos |
| **Strategy** | Backend | Múltiplas estratégias |
| **Decorator** | Backend | Adicionar responsabilidades |
| **Composition API** | Frontend | Reutilizar lógica |
| **Store (Pinia)** | Frontend | Estado global |
| **Component Composition** | Frontend | Componentes pequenos |
| **Slots** | Frontend | Flexibilidade |

---

## 🎓 Por Que Esses Padrões?

### Vantagens

✅ **Testabilidade:** Fácil mockar e testar  
✅ **Manutenibilidade:** Código claro e organizado  
✅ **Escalabilidade:** Fácil adicionar features  
✅ **Reutilização:** Código DRY  
✅ **Desacoplamento:** Baixa dependência  

### Trade-offs

⚠️ Mais código initially  
⚠️ Curva de aprendizado  
⚠️ Possível over-engineering em projects pequenos  

---

## 🔗 Links Relacionados

- [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Onde cada padrão vive
- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Como implementar
- [API_ARCHITECTURE.md](API_ARCHITECTURE.md) - Como padrões se comunicam
- [TECHNICAL_NOTES.md](../Business/TECHNICAL_NOTES.md) - Decisões arquiteturais

---

<div align="center">

**Próximo passo:** [CODING_STANDARDS.md](CODING_STANDARDS.md) ↗️

</div>
