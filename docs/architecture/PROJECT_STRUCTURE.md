# 📁 Estrutura do Projeto

> **Parte:** ARCHITECTURE  
> **Propósito:** Documentar organização do projeto e responsabilidades de cada pasta  
> **Última atualização:** Junho 2026

---

## 🏢 Visão Geral

O projeto segue uma **arquitetura em camadas** dividida em:
- **Frontend:** Vue 3 (50% do desenvolvimento)
- **Backend:** Laravel 12 (50% do desenvolvimento)  
- **Infra:** Docker + GitHub Actions

```
Cada camada é independente mas se comunica via REST API
Frontend ←→ API REST ←→ Backend ←→ Database
```

---

## 📂 Estrutura Completa

```
ai-financial-analyzer/
│
├── 📁 Docs/
│   └── 📄 GETTING_STARTED.md          ⭐ Comece por aqui!
│
├── 📄 README.md                       Quick reference (a implementar)
├── 📄 .gitignore                      Git config (a implementar)
│
│   ├── 📁 Architecture/               ← Você está aqui!
│   │   ├── PROJECT_STRUCTURE.md       (este arquivo)
│   │   ├── DESIGN_PATTERNS.md
│   │   ├── CODING_STANDARDS.md
│   │   ├── API_ARCHITECTURE.md
│   │   └── TECHNOLOGY_STACK.md
│   │
│   └── 📁 Business/
│       ├── BUSINESS_RULES.md
│       ├── DEVELOPMENT_ROADMAP.md
│       ├── FEATURE_SPECIFICATIONS.md
│       ├── TECHNICAL_NOTES.md
│       └── CONSTRAINTS.md
│
├── 📁 backend/                        Laravel 12 (50% código)
│   │
│   ├── 📁 app/
│   │   ├── 📁 Http/
│   │   │   ├── 📁 Controllers/        Lógica de requisição
│   │   │   │   ├── TransactionController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   ├── AIController.php
│   │   │   │   └── AuthController.php
│   │   │   │
│   │   │   └── 📁 Requests/           Validação de input
│   │   │       ├── StoreTransactionRequest.php
│   │   │       ├── UpdateTransactionRequest.php
│   │   │       └── AnalyzeRequest.php
│   │   │
│   │   ├── 📁 Models/                 Eloquent Models (database)
│   │   │   ├── User.php               Usuário do sistema
│   │   │   ├── Transaction.php        Transação financeira
│   │   │   ├── Category.php           Categoria de gasto
│   │   │   ├── AIAnalysis.php         Resultado análise IA
│   │   │   └── Audit.php              Log de ações
│   │   │
│   │   ├── 📁 Services/               Business Logic (camada chave!)
│   │   │   ├── TransactionService.php
│   │   │   │   ├── create()
│   │   │   │   ├── categorize()
│   │   │   │   └── validate()
│   │   │   │
│   │   │   ├── AIAnalysisService.php
│   │   │   │   ├── analyzeTransaction()
│   │   │   │   ├── detectAnomalies()
│   │   │   │   └── optimizeBudget()
│   │   │   │
│   │   │   ├── PixIntegrationService.php
│   │   │   │   ├── importPixTransactions()
│   │   │   │   └── validatePixData()
│   │   │   │
│   │   │   ├── CardProcessingService.php
│   │   │   │   ├── processCardImport()
│   │   │   │   └── detectDuplicates()
│   │   │   │
│   │   │   ├── AnalyticsService.php
│   │   │   │   ├── getMonthlyStats()
│   │   │   │   ├── getCategoryBreakdown()
│   │   │   │   └── forecastBudget()
│   │   │   │
│   │   │   └── AuthService.php
│   │   │       ├── generateToken()
│   │   │       └── refreshToken()
│   │   │
│   │   ├── 📁 Repositories/           Data Access Layer
│   │   │   ├── TransactionRepository.php
│   │   │   ├── UserRepository.php
│   │   │   └── CategoryRepository.php
│   │   │
│   │   ├── 📁 Actions/                Complex Operations
│   │   │   ├── ImportTransactionsAction.php
│   │   │   │   └── Executar importação de arquivo CSV
│   │   │   │
│   │   │   ├── AnalyzeTransactionAction.php
│   │   │   │   └── Chamar IA e salvar análise
│   │   │   │
│   │   │   └── GenerateReportAction.php
│   │   │       └── Gerar relatório mensal
│   │   │
│   │   ├── 📁 Events/                 Event System
│   │   │   ├── TransactionCreated.php
│   │   │   └── AnomalyDetected.php
│   │   │
│   │   ├── 📁 Listeners/              Event Handlers
│   │   │   ├── LogTransactionCreated.php
│   │   │   └── NotifyAnomalyDetected.php
│   │   │
│   │   ├── 📁 Exceptions/             Custom Exceptions
│   │   │   ├── InvalidTransactionException.php
│   │   │   ├── AIAnalysisFailedException.php
│   │   │   └── InsufficientBalanceException.php
│   │   │
│   │   ├── 📁 Traits/                 Shared Functionality
│   │   │   ├── Auditable.php          Auto-logging
│   │   │   ├── HasTimestamps.php
│   │   │   └── HasRelations.php
│   │   │
│   │   ├── 📁 Enums/                  Type-safe Constants
│   │   │   ├── TransactionType.php    (debit, credit, pix)
│   │   │   ├── TransactionStatus.php  (pending, approved, failed)
│   │   │   └── AnomalyLevel.php       (low, medium, high)
│   │   │
│   │   └── 📁 Mail/                   Email Templates
│   │       ├── TransactionAlert.php
│   │       └── AnomalyDetectionMail.php
│   │
│   ├── 📁 database/
│   │   ├── 📁 migrations/             Database schema
│   │   │   ├── 2024_01_01_create_users_table.php
│   │   │   ├── 2024_01_02_create_transactions_table.php
│   │   │   ├── 2024_01_03_create_categories_table.php
│   │   │   ├── 2024_01_04_create_ai_analyses_table.php
│   │   │   └── 2024_01_05_create_audit_logs_table.php
│   │   │
│   │   └── 📁 seeders/                Dados iniciais
│   │       ├── DatabaseSeeder.php     Executa todos os seeders
│   │       ├── UserSeeder.php         Cria usuários de teste
│   │       ├── TransactionSeeder.php  Cria transações de exemplo
│   │       └── CategorySeeder.php     Cria categorias padrão
│   │
│   ├── 📁 routes/
│   │   ├── web.php                    Web routes (páginas)
│   │   ├── api.php                    API routes (endpoints)
│   │   └── auth.php                   Auth routes
│   │
│   ├── 📁 tests/                      Testes (>80% coverage)
│   │   ├── 📁 Unit/
│   │   │   ├── Services/              Service layer tests
│   │   │   │   ├── AIAnalysisServiceTest.php
│   │   │   │   ├── TransactionServiceTest.php
│   │   │   │   └── AnalyticsServiceTest.php
│   │   │   │
│   │   │   └── Models/                Model tests
│   │   │       ├── UserTest.php
│   │   │       └── TransactionTest.php
│   │   │
│   │   ├── 📁 Feature/
│   │   │   ├── TransactionApiTest.php API endpoint tests
│   │   │   ├── AIAnalysisApiTest.php
│   │   │   ├── AuthApiTest.php
│   │   │   └── AnalyticsApiTest.php
│   │   │
│   │   └── TestCase.php               Base test class
│   │
│   ├── 📁 config/
│   │   ├── app.php                    App configuration
│   │   ├── database.php               Database config
│   │   ├── services.php               External services
│   │   └── claude.php                 Claude API config
│   │
│   ├── 📁 storage/                    File uploads & caches
│   │   ├── 📁 app/
│   │   ├── 📁 logs/
│   │   └── 📁 cache/
│   │
│   ├── .env.example                   Environment template
│   ├── composer.json                  PHP dependencies
│   ├── phpunit.xml                    Test configuration
│   ├── artisan                        CLI command
│   └── bootstrap/                     Application bootstrap
│
├── 📁 frontend/                       Vue 3 (50% código)
│   │
│   ├── 📁 src/
│   │   ├── 📁 components/             Componentes reutilizáveis
│   │   │   ├── 📁 common/
│   │   │   │   ├── Header.vue         Cabeçalho global
│   │   │   │   ├── Sidebar.vue        Sidebar navegação
│   │   │   │   ├── Loading.vue        Spinner/loading
│   │   │   │   └── ErrorAlert.vue     Mensagem de erro
│   │   │   │
│   │   │   ├── 📁 dashboard/
│   │   │   │   ├── SummaryCards.vue   Cards com métricas
│   │   │   │   ├── MonthlySummary.vue Resumo mensal
│   │   │   │   └── QuickStats.vue     Stats rápidas
│   │   │   │
│   │   │   ├── 📁 transactions/
│   │   │   │   ├── TransactionForm.vue    Form importação
│   │   │   │   ├── TransactionTable.vue   Tabela transações
│   │   │   │   ├── TransactionRow.vue     Uma transação
│   │   │   │   └── TransactionFilters.vue Filtros
│   │   │   │
│   │   │   ├── 📁 analytics/
│   │   │   │   ├── SpendingChart.vue      Gráfico gastos
│   │   │   │   ├── TrendChart.vue         Tendência
│   │   │   │   └── CategoryBreakdown.vue  Categorias
│   │   │   │
│   │   │   └── 📁 ai/
│   │   │       ├── AIInsights.vue         Insights IA
│   │   │       ├── AnomalyAlert.vue       Alerta anomalia
│   │   │       └── RecommendationCard.vue Recomendação
│   │   │
│   │   ├── 📁 pages/                  Page-level components
│   │   │   ├── Dashboard.vue          /dashboard
│   │   │   ├── Transactions.vue       /transactions
│   │   │   ├── Analytics.vue          /analytics
│   │   │   ├── AIRecommendations.vue  /ai/recommendations
│   │   │   ├── Settings.vue           /settings
│   │   │   ├── Login.vue              /login
│   │   │   └── NotFound.vue           404
│   │   │
│   │   ├── 📁 stores/                 Pinia state management
│   │   │   ├── auth.ts                Autenticação
│   │   │   ├── transactions.ts        Estado transações
│   │   │   ├── analytics.ts           Estado analytics
│   │   │   └── ui.ts                  Estado UI (modais, etc)
│   │   │
│   │   ├── 📁 services/               API & utilities
│   │   │   ├── api.ts                 HTTP client (axios)
│   │   │   ├── auth.ts                Autenticação/tokens
│   │   │   ├── transactions.ts        Transaction API calls
│   │   │   ├── analytics.ts           Analytics API calls
│   │   │   └── ai.ts                  AI API calls
│   │   │
│   │   ├── 📁 composables/            Vue 3 Composition API
│   │   │   ├── useAuth.ts             Auth logic
│   │   │   ├── useTransactions.ts     Transaction logic
│   │   │   ├── useAnalytics.ts        Analytics logic
│   │   │   └── useFetch.ts            Generic fetch
│   │   │
│   │   ├── 📁 types/                  TypeScript types
│   │   │   ├── index.ts               Tipos globais
│   │   │   ├── transaction.ts         Transaction type
│   │   │   ├── user.ts                User type
│   │   │   └── ai.ts                  AI types
│   │   │
│   │   ├── 📁 utils/                  Utilities
│   │   │   ├── formatters.ts          Format helpers
│   │   │   ├── validators.ts          Input validators
│   │   │   ├── dates.ts               Date helpers
│   │   │   └── currency.ts            Currency helpers
│   │   │
│   │   ├── 📁 styles/                 CSS/Tailwind
│   │   │   ├── main.css               Global styles
│   │   │   └── tailwind.css           Tailwind imports
│   │   │
│   │   ├── App.vue                    Root component
│   │   └── main.ts                    Entry point
│   │
│   ├── 📁 __tests__/                  Testes (80%+ coverage)
│   │   ├── 📁 unit/
│   │   │   ├── composables/
│   │   │   ├── stores/
│   │   │   └── utils/
│   │   │
│   │   ├── 📁 integration/
│   │   │   ├── pages/
│   │   │   └── services/
│   │   │
│   │   └── setup.ts                   Test configuration
│   │
│   ├── 📁 public/                     Static assets
│   │   ├── favicon.ico
│   │   └── robots.txt
│   │
│   ├── index.html                     HTML entry point
│   ├── package.json                   Dependencies
│   ├── tsconfig.json                  TypeScript config
│   ├── vite.config.ts                 Vite build config
│   ├── vitest.config.ts               Test configuration
│   └── .eslintrc.cjs                  Lint rules
│
├── 📁 .github/
│   └── 📁 workflows/                  CI/CD Pipelines
│       ├── ci.yml                     Tests + Lint
│       └── deploy.yml                 Automated Deploy
│
├── docker-compose.yml                 Development environment
├── Dockerfile                         Production image
├── .gitignore                         Git ignore rules
└── .editorconfig                      Editor configuration
```

---

## 📋 Responsabilidades por Pasta

### Backend (`backend/`)

#### 🎮 Controllers (`app/Http/Controllers/`)
- **O quê:** Controla entrada de requisições HTTP
- **Responsabilidade:** 
  - Receber requisição
  - Validar com Requests
  - Chamar Service/Repository
  - Retornar resposta JSON
- **Exemplo:**
  ```php
  // Nunca coloque lógica aqui!
  public function store(StoreTransactionRequest $request) {
      $transaction = $this->service->create($request->validated());
      return response()->json($transaction, 201);
  }
  ```

#### 📨 Requests (`app/Http/Requests/`)
- **O quê:** Validação de input
- **Responsabilidade:**
  - Validar dados recebidos
  - Autorizar ação
  - Transformar dados se necessário
- **Exemplo:**
  ```php
  public function rules() {
      return [
          'amount' => 'required|numeric|min:0.01|max:100000',
          'description' => 'required|string|max:255',
      ];
  }
  ```

#### 🗄️ Models (`app/Models/`)
- **O quê:** Representação do banco de dados
- **Responsabilidade:**
  - Representar entidade
  - Definir relações
  - Acessar/modificar dados
  - **Lógica mínima** (delegue ao Service!)
- **Exemplo:**
  ```php
  class Transaction extends Model {
      public function user() { return $this->belongsTo(User::class); }
      public function category() { return $this->belongsTo(Category::class); }
  }
  ```

#### 🔧 Services (`app/Services/`)
- **O quê:** Lógica de negócio
- **Responsabilidade:**
  - Complexa lógica
  - Orquestração entre models
  - Integrações (IA, externos)
  - **AQUI VAI 80% DA LÓGICA!**
- **Exemplo:**
  ```php
  class TransactionService {
      public function create($data): Transaction {
          // 1. Validar logica negócio
          // 2. Categorizar com IA
          // 3. Detectar anomalias
          // 4. Salvar no DB
          // 5. Disparar eventos
      }
  }
  ```

#### 📚 Repositories (`app/Repositories/`)
- **O quê:** Acesso a dados
- **Responsabilidade:**
  - Abstrair banco de dados
  - Queries complexas
  - Cache layer
- **Exemplo:**
  ```php
  class TransactionRepository {
      public function findLastMonth($userId) {
          return Transaction::where('user_id', $userId)
              ->where('created_at', '>=', now()->subMonth())
              ->get();
      }
  }
  ```

#### ⚙️ Actions (`app/Actions/`)
- **O quê:** Operações complexas
- **Responsabilidade:**
  - Operações multi-step
  - Transações DB
  - Rollback automático
- **Exemplo:**
  ```php
  class ImportTransactionsAction {
      // Importar CSV → Validar → Salvar → Notificar
  }
  ```

#### 🧪 Tests (`tests/`)
- **O quê:** Garantir funcionamento
- **Responsabilidade:**
  - Unit tests (serviços isolados)
  - Feature tests (APIs completas)
  - >80% coverage

---

### Frontend (`frontend/`)

#### 🧩 Components (`src/components/`)
- **O quê:** Pedaços da UI reutilizáveis
- **Responsabilidade:**
  - Renderizar UI
  - Emitir eventos
  - Props bem definidas
- **Exemplo:**
  ```vue
  <TransactionTable 
    :transactions="transactions"
    @import="handleImport"
    @analyze="handleAnalyze"
  />
  ```

#### 📄 Pages (`src/pages/`)
- **O quê:** Páginas completas
- **Responsabilidade:**
  - Componentes do nível de página
  - Chamar stores/services
  - Render completo
- **Exemplo:** `Dashboard.vue`, `Transactions.vue`

#### 🏪 Stores (`src/stores/`)
- **O quê:** Estado global (Pinia)
- **Responsabilidade:**
  - Estado reativo
  - Ações
  - Getters/computed
  - Persistência
- **Exemplo:**
  ```ts
  export const useTransactionStore = defineStore('transactions', {
      state: () => ({ transactions: [] }),
      actions: { 
          async fetch() { /* call API */ }
      },
      getters: {
          total: (state) => state.transactions.reduce(...)
      }
  })
  ```

#### 🔗 Services (`src/services/`)
- **O quê:** Integração com backend
- **Responsabilidade:**
  - HTTP requests
  - Auth tokens
  - Error handling
- **Exemplo:**
  ```ts
  export const transactionService = {
      async list() { return api.get('/transactions') },
      async create(data) { return api.post('/transactions', data) },
  }
  ```

#### 🎣 Composables (`src/composables/`)
- **O quê:** Lógica reutilizável
- **Responsabilidade:**
  - Lógica Vue 3 composition
  - Hooks customizados
  - Reutilização
- **Exemplo:**
  ```ts
  export const useTransactions = () => {
      const store = useTransactionStore()
      const loading = ref(false)
      
      const fetch = async () => { /* fetch */ }
      
      return { loading, fetch, transactions: store.transactions }
  }
  ```

---

## 🔄 Fluxo de Dados

### Como uma requisição flui:

```
1. FRONTEND (src/)
   ├─ User clica botão em TransactionTable.vue
   ├─ Chama: transactionService.create(data)
   └─ Envia: POST /api/transactions

2. BACKEND API (routes/api.php)
   ├─ Route::post('/transactions', [TransactionController::class, 'store'])
   └─ Chama: TransactionController@store

3. CONTROLLER (app/Http/Controllers/)
   ├─ Recebe requisição
   ├─ Valida com StoreTransactionRequest
   ├─ Chama: $this->transactionService->create($data)
   └─ Retorna: response()->json($transaction)

4. SERVICE (app/Services/)
   ├─ Lógica de negócio
   ├─ Valida regras
   ├─ Chama: AIAnalysisService->analyze($transaction)
   ├─ Chama: $repository->save($transaction)
   ├─ Dispara: TransactionCreated event
   └─ Retorna: Transaction object

5. REPOSITORY (app/Repositories/)
   ├─ Executa query no BD
   ├─ Cacheia resultado
   └─ Retorna: Model instance

6. DATABASE (PostgreSQL)
   ├─ Valida schema
   ├─ Salva dados
   └─ Retorna: ID gerado

7. FRONTEND (componente reativo)
   ├─ Store recebe nova transação
   ├─ Componente re-renderiza automaticamente
   └─ User vê a mudança
```

---

## 📊 Estatísticas da Estrutura

```
Backend (Laravel)
├─ Controllers:        4-5 arquivos
├─ Models:            4-6 arquivos
├─ Services:          5-7 arquivos
├─ Repositories:      3-4 arquivos
├─ Requests:          4-5 arquivos
└─ Tests:             15+ arquivos
   └─ Coverage: 85%+

Frontend (Vue 3)
├─ Components:        20+ arquivos
├─ Pages:            6-8 arquivos
├─ Stores:           4-5 arquivos
├─ Services:         4-5 arquivos
├─ Composables:      4-5 arquivos
└─ Tests:            15+ arquivos
   └─ Coverage: 80%+

Total linhas de código:
├─ Backend: ~3.500 LOC
├─ Frontend: ~2.800 LOC
└─ Testes: ~1.500 LOC
```

---

## ✅ Checklist: Estou no lugar certo?

| Preciso... | Vou para... | Arquivo |
|-----------|-----------|---------|
| Adicionar um novo endpoint | Controllers | `app/Http/Controllers/` |
| Validar input | Requests | `app/Http/Requests/` |
| Implementar lógica | Services | `app/Services/` |
| Acessar dados | Repositories | `app/Repositories/` |
| Criar componente | Components | `src/components/` |
| Controlar estado | Stores | `src/stores/` |
| Chamar API | Services | `src/services/` |
| Reutilizar lógica | Composables | `src/composables/` |
| Salvar tipos | Types | `src/types/` |
| Testemunhar | Tests | `tests/` ou `__tests__/` |

---

## 🎯 Princípios de Organização

### 1. **Single Responsibility**
Cada arquivo tem UMA responsabilidade clara.

### 2. **Separation of Concerns**
Controllers ≠ Services ≠ Models ≠ Repositories

### 3. **DRY (Don't Repeat Yourself)**
Lógica reutilizável vai em Services/Composables

### 4. **Type Safety**
TypeScript (frontend) e PHP 8.3 typed (backend)

### 5. **Testability**
Estrutura facilita testes unitários e de integração

---

## 🔗 Links Relacionados

- [DESIGN_PATTERNS.md](DESIGN_PATTERNS.md) - Como as pastas se comunicam
- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Padrão de código por pasta
- [API_ARCHITECTURE.md](API_ARCHITECTURE.md) - Fluxo de dados entre camadas
- [BUSINESS_RULES.md](../Business/BUSINESS_RULES.md) - Lógica que vai em Services

---

## 📝 Notas de Manutenção

- **Atualize esta documentação** quando mover arquivos
- **Siga esta estrutura** para novas features
- **Questione a estrutura** se não fizer sentido
- **Refatore regularmente** para manter limpo

---

<div align="center">

**Próximo passo:** [DESIGN_PATTERNS.md](DESIGN_PATTERNS.md) ↗️

</div>
