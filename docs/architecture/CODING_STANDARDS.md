# 📝 Padrões de Código (Coding Standards)

> **Parte:** ARCHITECTURE  
> **Propósito:** Documentar padrões de escrita de código  
> **Última atualização:** Junho 2026

---

## 🎯 Regra de Ouro

**"Código é lido 10x mais que é escrito"**

Escreva para quem vai ler, não para máquina.

---

## 🐘 Backend (PHP / Laravel)

### Padrão PSR-12 + Laravel Conventions

#### Naming Conventions

```php
// ✅ Classes: PascalCase
class TransactionService { }
class AIAnalysisService { }
class CreateTransactionRequest { }

// ✅ Métodos/Funções: camelCase
public function analyzeTransaction() { }
public function detectAnomalies() { }

// ✅ Constantes: UPPER_SNAKE_CASE
const MAX_TRANSACTION_AMOUNT = 100000;
const CACHE_TTL = 3600;

// ✅ Variáveis: snake_case (em arrays) ou camelCase
$transaction_id = 123; // ← Melhor para arrays
$transactionId = 123;   // ← Para objetos

// ✅ Database: snake_case para tudo
// Migrations: 2024_01_01_create_transactions_table.php
// Tables: transactions, ai_analyses, audit_logs
// Columns: created_at, updated_at, user_id
```

#### Estrutura de Classe

```php
<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service para operações de transações
 * 
 * Responsabilidades:
 * - Criar e validar transações
 * - Categorizar automaticamente
 * - Detectar anomalias
 * - Calcular estatísticas
 */
class TransactionService
{
    /**
     * Construtor com dependency injection
     */
    public function __construct(
        private TransactionRepository $repository,
        private AIAnalysisService $aiService,
    ) {}
    
    /**
     * Criar nova transação
     * 
     * @param array $data Dados da transação
     * @return Transaction Transação criada
     * @throws InvalidTransactionException
     */
    public function create(array $data): Transaction
    {
        // 1. Validar dados
        $this->validateBusinessRules($data);
        
        // 2. Categorizar
        $data['category'] = $this->categorize($data['description']);
        
        // 3. Salvar
        $transaction = $this->repository->create($data);
        
        // 4. Análise assíncrona
        AnalyzeTransactionJob::dispatch($transaction);
        
        // 5. Log
        Log::info("Transaction created", ['id' => $transaction->id]);
        
        return $transaction;
    }
    
    /**
     * Validações de regra de negócio
     */
    private function validateBusinessRules(array $data): void
    {
        // Lógica de validação aqui
    }
    
    /**
     * Categorizar transação (exemplo interno)
     */
    private function categorize(string $description): string
    {
        return cache()->remember(
            "categorize_{$description}",
            3600,
            fn() => $this->aiService->categorize($description)
        );
    }
}
```

#### Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $service,
    ) {}
    
    /**
     * Criar transação
     * 
     * POST /api/transactions
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->service->create($request->validated());
        
        return response()->json($transaction, 201);
    }
    
    /**
     * Listar transações
     * 
     * GET /api/transactions
     */
    public function index()
    {
        $transactions = $this->service->list(
            auth()->user()->id,
            request()->get('page', 1)
        );
        
        return response()->json($transactions);
    }
}
```

#### Requests (Validação)

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    /**
     * Regras de validação
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:100000',
            'type' => 'required|in:debit,credit,pix',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date|before_or_equal:today',
        ];
    }
    
    /**
     * Mensagens customizadas (opcional)
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Valor mínimo é 0.01',
            'type.in' => 'Tipo inválido',
        ];
    }
}
```

#### Models

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'type',
        'category_id',
        'date',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];
    
    // Relações
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function analyses(): HasMany
    {
        return $this->hasMany(AIAnalysis::class);
    }
    
    // Scopes (queries reutilizáveis)
    public function scopeLastMonth($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }
    
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
```

#### Testes

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TransactionService;
use App\Models\Transaction;

class TransactionServiceTest extends TestCase
{
    private TransactionService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TransactionService::class);
    }
    
    /**
     * @test
     * Teste se cria transação corretamente
     */
    public function it_creates_transaction_correctly()
    {
        // Arrange
        $data = [
            'description' => 'UBER BRASIL',
            'amount' => 45.50,
            'type' => 'debit',
            'date' => now(),
        ];
        
        // Act
        $transaction = $this->service->create($data);
        
        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'UBER BRASIL',
        ]);
    }
}
```

#### Comentários

```php
✅ BOM:
/**
 * Categorizar transação usando IA
 * 
 * Estratégia:
 * 1. Verificar cache
 * 2. Se não encontrar, chamar Claude API
 * 3. Cachear resultado por 24h
 * 
 * @param string $description Descrição da transação
 * @return string Categoria detectada
 */
public function categorize(string $description): string { }

❌ RUIM:
// categorizar
public function cat($d) { } // Nada claro!

❌ EXCESSIVO:
// Loop através de array
foreach ($items as $item) { // Óbvio demais
    // ...
}
```

---

## 💻 Frontend (TypeScript / Vue 3)

### Prettier + ESLint + Vue Conventions

#### Naming Conventions

```ts
// ✅ Componentes: PascalCase
<TransactionTable />
<AIInsights />
<SummaryCard />

// ✅ Stores: camelCase
useTransactionStore()
useAnalyticsStore()
useAuthStore()

// ✅ Funções/Métodos: camelCase
const fetchTransactions = async () => { }
const calculateTotal = (items) => { }

// ✅ Variáveis: camelCase
const loading = ref(false)
const selectedTransaction = ref(null)
const monthlySpending = computed(() => { })

// ✅ Constantes: UPPER_SNAKE_CASE
const MAX_FILE_SIZE = 5 * 1024 * 1024
const API_TIMEOUT = 30000
```

#### Estrutura de Componente

```vue
<template>
  <div class="transaction-table">
    <!-- Template limpo e simples -->
    <table>
      <thead>
        <tr>
          <th>Description</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <TransactionRow
          v-for="transaction in transactions"
          :key="transaction.id"
          :transaction="transaction"
          @delete="handleDelete"
          @edit="handleEdit"
        />
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Transaction } from '@/types'
import TransactionRow from './TransactionRow.vue'
import { useTransactionStore } from '@/stores/transactions'

// Props com tipos explícitos
interface Props {
  initialLoad?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  initialLoad: true,
})

// Emits com tipos
const emit = defineEmits<{
  (e: 'delete', id: number): void
  (e: 'edit', transaction: Transaction): void
}>()

// Store
const store = useTransactionStore()

// State
const loading = ref(false)

// Computed
const transactions = computed(() => store.transactions)

// Métodos
const handleDelete = async (id: number) => {
  loading.value = true
  try {
    await store.deleteTransaction(id)
  } finally {
    loading.value = false
  }
}

const handleEdit = (transaction: Transaction) => {
  emit('edit', transaction)
}

// Lifecycle
onMounted(() => {
  if (props.initialLoad) {
    store.fetch()
  }
})
</script>

<style scoped>
.transaction-table {
  @apply rounded-lg border border-gray-200;
}

.transaction-table table {
  @apply w-full;
}

.transaction-table thead {
  @apply bg-gray-50;
}

.transaction-table th {
  @apply px-4 py-3 text-left text-sm font-semibold text-gray-900;
}

.transaction-table td {
  @apply px-4 py-3 border-t border-gray-200;
}
</style>
```

#### Composables

```ts
// composables/useTransactions.ts
import { ref, computed, onMounted } from 'vue'
import type { Transaction } from '@/types'
import { useTransactionStore } from '@/stores/transactions'
import { transactionService } from '@/services/transactions'

/**
 * Hook para gerenciar transações
 * 
 * @returns Objeto com estado e métodos
 */
export const useTransactions = () => {
  const store = useTransactionStore()
  
  // State
  const loading = ref(false)
  const error = ref<string | null>(null)
  
  // Computed
  const transactions = computed(() => store.transactions)
  const total = computed(() => 
    transactions.value.reduce((sum, t) => sum + t.amount, 0)
  )
  
  // Métodos
  const fetch = async () => {
    loading.value = true
    error.value = null
    
    try {
      await store.fetch()
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Unknown error'
    } finally {
      loading.value = false
    }
  }
  
  const create = async (data: Partial<Transaction>) => {
    loading.value = true
    try {
      return await store.create(data)
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Unknown error'
      throw e
    } finally {
      loading.value = false
    }
  }
  
  // Lifecycle
  onMounted(() => fetch())
  
  return { loading, error, transactions, total, fetch, create }
}
```

#### Types

```ts
// types/index.ts
export interface Transaction {
  id: number
  userId: number
  description: string
  amount: number
  type: 'debit' | 'credit' | 'pix'
  categoryId?: number
  category?: Category
  date: string // ISO date
  createdAt: string
  updatedAt: string
}

export interface Category {
  id: number
  name: string
  icon?: string
  color?: string
}

export interface AIAnalysis {
  transactionId: number
  category: string
  confidence: number
  isAnomaly: boolean
  suggestions?: string[]
}

export interface User {
  id: number
  name: string
  email: string
  avatarUrl?: string
}
```

#### Services

```ts
// services/transactions.ts
import { api } from './api'
import type { Transaction } from '@/types'

export const transactionService = {
  async list(page = 1): Promise<Transaction[]> {
    const { data } = await api.get('/transactions', {
      params: { page },
    })
    return data
  },
  
  async create(data: Partial<Transaction>): Promise<Transaction> {
    const response = await api.post('/transactions', data)
    return response.data
  },
  
  async update(id: number, data: Partial<Transaction>): Promise<Transaction> {
    const { data: result } = await api.put(`/transactions/${id}`, data)
    return result
  },
  
  async delete(id: number): Promise<void> {
    await api.delete(`/transactions/${id}`)
  },
  
  async analyze(id: number): Promise<AIAnalysis> {
    const { data } = await api.post(`/transactions/${id}/analyze`)
    return data
  },
}
```

#### Testes (Vitest)

```ts
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import TransactionTable from '@/components/TransactionTable.vue'
import type { Transaction } from '@/types'

describe('TransactionTable.vue', () => {
  let wrapper: any
  
  const mockTransactions: Transaction[] = [
    {
      id: 1,
      userId: 1,
      description: 'UBER BRASIL',
      amount: 45.50,
      type: 'debit',
      date: '2024-01-15',
      createdAt: '2024-01-15T10:00:00Z',
      updatedAt: '2024-01-15T10:00:00Z',
    },
  ]
  
  beforeEach(() => {
    wrapper = mount(TransactionTable, {
      props: {
        transactions: mockTransactions,
      },
    })
  })
  
  it('renders transactions correctly', () => {
    expect(wrapper.find('table').exists()).toBe(true)
    expect(wrapper.findAll('tbody tr')).toHaveLength(1)
  })
  
  it('emits delete event', async () => {
    await wrapper.vm.handleDelete(1)
    expect(wrapper.emitted('delete')).toBeTruthy()
  })
})
```

---

## 📋 Checklist de Qualidade

### Antes de Commitar

- [ ] Código passa em linter (`eslint`, `phpstan`)
- [ ] Código passa em formatter (`prettier`, `php-cs-fixer`)
- [ ] Testes passam (`php artisan test`, `npm run test`)
- [ ] Coverage > 80%
- [ ] Sem `console.log()` ou `dd()`
- [ ] Sem hardcoded values (use env/config)
- [ ] Documentação atualizada
- [ ] Commit message descritiva

### Antes de Fazer PR

- [ ] Código segue padrões deste arquivo
- [ ] Testes inclusos para nova lógica
- [ ] Documentação em docstrings/comentários
- [ ] Sem warnings em console
- [ ] Performance verificada
- [ ] Sem segredos expostos

---

## 🛠️ Tools & Configurações

### Backend

```bash
# Lint
./vendor/bin/phpstan analyse app

# Format
./vendor/bin/php-cs-fixer fix app

# Testes
php artisan test
php artisan test --coverage
```

### Frontend

```bash
# Lint
npm run lint

# Format
npm run format

# Testes
npm run test
npm run test:coverage
```

---

## 🔗 Links Relacionados

- [DESIGN_PATTERNS.md](DESIGN_PATTERNS.md) - Padrões arquiteturais
- [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Onde colocar código
- [API_ARCHITECTURE.md](API_ARCHITECTURE.md) - Como estruturar APIs

---

<div align="center">

**Próximo passo:** [API_ARCHITECTURE.md](API_ARCHITECTURE.md) ↗️

</div>
