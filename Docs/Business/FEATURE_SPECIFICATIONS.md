# ✨ Feature Specifications

> **Parte:** BUSINESS  
> **Propósito:** Detalhe de cada feature com user stories  
> **Última atualização:** Junho 2026

---

## 🎯 Estrutura de cada Feature

Cada feature segue este formato:

```
Feature: [Nome]
├─ Descrição
├─ Usuário-alvo
├─ User Story
├─ Fluxo
├─ Acceptance Criteria
├─ Implementação (Backend/Frontend)
└─ Tests
```

---

## 📋 Feature 1: Dashboard

### Descrição
Página home mostrando resumo financeiro em cards

### User Story
```
Como usuário
Quero ver meu resumo financeiro em um relance
Para entender minha situação financeira atual
```

### Fluxo

```
1. User acessa /dashboard
2. Sistema busca dados do mês atual:
   ├─ Saldo total
   ├─ Gastos mês
   ├─ Receita mês
   └─ % orçamento usado
3. Mostra em 4 cards coloridos
4. User clica card → vai para detail page
```

### Acceptance Criteria

- [ ] 4 cards visivelmente distintos (cores diferentes)
- [ ] Valores formatados em currency (R$)
- [ ] Números atualizados em real-time
- [ ] Responsivo em mobile (cards stackam)
- [ ] Cards clicáveis (navegam para detail)
- [ ] Loading spinner durante fetch

### Implementação

**Backend:**
```php
// Endpoint: GET /api/dashboard/summary
{
  "total_balance": 5420.50,
  "month_spending": 2150.00,
  "month_income": 7500.00,
  "budget_percentage": 43
}
```

**Frontend:**
```vue
<!-- Dashboard.vue -->
<SummaryCards 
  :balance="store.balance"
  :spending="store.spending"
  @navigate="goToPage"
/>
```

### Tests

```php
✅ Backend: test_dashboard_returns_correct_totals
✅ Frontend: renders 4 cards with correct values
```

---

## 🏦 Feature 2: Transaction Management

### Descrição
CRUD completo de transações (criar, listar, editar, deletar)

### User Stories

```
Como usuário
Quero importar minhas transações do CSV
Para não ter que inserir manualmente

Como usuário
Quero editar uma transação
Para corrigir erros

Como usuário
Quero deletar uma transação
Para remover entradas erradas
```

### Fluxo: Criar/Importar

```
Opção A: Form manual
├─ Fill form
├─ Backend valida
├─ Salva em DB
└─ UI atualiza

Opção B: CSV import
├─ Upload arquivo
├─ Backend parseia CSV
├─ Valida cada linha
├─ Salva em batch
└─ Mostra resultado (X criadas, Y erradas)
```

### Acceptance Criteria

**Create:**
- [ ] Form tem campos: description, amount, type, date
- [ ] Validações em tempo real (frontend)
- [ ] Server-side validation (backend)
- [ ] Success toast + reload table

**List:**
- [ ] Tabela mostra 20 items
- [ ] Pagination funciona
- [ ] Sorting por data/amount
- [ ] Filtros: categoria, tipo, data range

**Edit:**
- [ ] Pre-fill form com dados
- [ ] Save atualiza tabela
- [ ] Dirty state indicator
- [ ] Confirm antes de salvar

**Delete:**
- [ ] Soft delete (não remove do DB)
- [ ] Confirm modal antes
- [ ] Undo por 10 segundos
- [ ] Remove de tela imediatamente

### Implementação

**Backend:**
```php
// POST   /api/transactions (create)
// GET    /api/transactions (list, paginated)
// PUT    /api/transactions/{id} (update)
// DELETE /api/transactions/{id} (soft delete)
// POST   /api/transactions/import (CSV)
```

**Frontend:**
```vue
<TransactionTable :transactions="store.transactions" />
<TransactionForm @save="store.create" />
```

### Tests

```
✅ CREATE: validation, save, response
✅ READ:   pagination, filtering, sorting
✅ UPDATE: modify data, audit log
✅ DELETE: soft delete, recover
✅ IMPORT: CSV parsing, batch save
```

---

## 🤖 Feature 3: AI Categorization

### Descrição
Claude analisa descrição da transação e sugere categoria

### User Story

```
Como usuário
Quero que o sistema categorize minhas transações automaticamente
Para não ter que fazer manualmente
```

### Fluxo

```
1. User cria/importa transação
2. Backend chama Claude:
   "Categorize: {description}"
3. Claude retorna:
   {
     "category": "Food",
     "confidence": 0.95
   }
4. Salva categoria em DB
5. UI mostra com badge colorido
6. User pode override se discordar
```

### Acceptance Criteria

- [ ] Categorização automática ao criar
- [ ] Confidence score visível (0-100%)
- [ ] Se confiança < 70%, mostra "needs review"
- [ ] User pode override
- [ ] Cache respostas (24h)
- [ ] Fallback se IA falhar

### Implementação

**Backend:**
```php
// Service: AIAnalysisService::categorize($description)
// Prompts em: config/claude.php
// Caching em: Redis
```

**Frontend:**
```vue
<CategoryBadge 
  :category="transaction.category"
  :confidence="transaction.confidence"
  @override="changecategory"
/>
```

### Tests

```
✅ SUCCESS: returns valid category
✅ CONFIDENCE: ranges 0-1
✅ FALLBACK: handles API failure
✅ CACHE: returns cached result
✅ OVERRIDE: user can change
```

---

## 🚨 Feature 4: Anomaly Detection

### Descrição
Claude detecta padrões anormais de gastos

### User Story

```
Como usuário
Quero ser alertado sobre gastos incomuns
Para identificar problemas potenciais
```

### Fluxo

```
1. User importa/cria transação
2. Backend executa:
   └─ AnalyzeTransactionJob (async)
3. Análise IA:
   ├─ Compare com histórico
   ├─ Checar padrões
   └─ Score de "estranheza"
4. Se anômala:
   ├─ Cria AnomalyAlert
   ├─ Notifica user (email, push)
   ├─ Mostra badge na UI
   └─ Adiciona em "Insights" dashboard
```

### Acceptance Criteria

- [ ] Detecta >20% acima da média
- [ ] Novo padrão de gasto
- [ ] Categoria frequência mudou
- [ ] Notificações opcionais (user pode mute)
- [ ] Acessível via dashboard
- [ ] Histórico de anomalias salvado

### Implementação

**Backend:**
```php
// Service: AIAnalysisService::detectAnomalies($transactionId)
// Job: AnalyzeTransactionJob
// Event: AnomalyDetected::dispatch()
// Listeners: NotifyUserListener, LogListener
```

**Frontend:**
```vue
<AIInsights :anomalies="store.anomalies" />
<AnomalyAlert v-for="a in anomalies" :anomaly="a" />
```

### Tests

```
✅ DETECT: identify spending spikes
✅ NOTIFY: send alerts
✅ PERSIST: save to DB
✅ RETRIEVE: show in UI
✅ MUTE: user can silence
```

---

## 💡 Feature 5: Budget Recommendations

### Descrição
Claude analisa gastos e sugere economia

### User Story

```
Como usuário
Quero saber em quais categorias posso economizar
Para controlar meus gastos
```

### Fluxo

```
1. User acessa /ai/recommendations
2. Backend calcula:
   ├─ Gastos por categoria (últimos 30d)
   ├─ Médias históricas
   └─ Tendências
3. Chama Claude com dados
4. Claude retorna:
   ├─ "Gasta 40% com alimentação"
   ├─ "Pode economizar R$ 450/mês"
   ├─ "3 assinaturas sem uso"
   └─ "Orçamento sugerido: R$ 4.500"
5. Mostra recomendações na UI
6. User pode aceitar/ignorar
```

### Acceptance Criteria

- [ ] Análise clara e acionável
- [ ] Recomendações específicas (nomes, valores)
- [ ] Baseado em dados real do user
- [ ] Atualizado diariamente
- [ ] User pode marcar como "implementado"
- [ ] Histórico de recomendações

### Implementação

**Backend:**
```php
// Service: AIAnalysisService::optimizeBudget($userId)
// Endpoint: GET /api/ai/recommendations
// Cache: 24h
```

**Frontend:**
```vue
<RecommendationCard 
  v-for="rec in recommendations"
  :recommendation="rec"
  @accept="markAsImplemented"
/>
```

### Tests

```
✅ ANALYSIS: correct category breakdown
✅ RECOMMENDATIONS: valid suggestions
✅ ACTIONS: track implementations
✅ UPDATES: refresh daily
```

---

## 📊 Feature 6: Analytics & Charts

### Descrição
Dashboard com gráficos de gastos

### User Story

```
Como usuário
Quero ver gráficos de meus gastos por categoria
Para visualizar onde meu dinheiro vai
```

### Fluxo

```
1. User acessa /analytics
2. Sistema busca dados:
   ├─ Gastos por categoria (pie)
   ├─ Tendência mensal (line)
   └─ Top 5 categorias (bar)
3. Renderiza charts
4. User pode:
   ├─ Filtrar por período
   ├─ Comparar meses
   └─ Exportar PDF (opcional)
```

### Charts

**Pie Chart (Gastos por Categoria)**
```
Food: 35%
Transportation: 20%
Entertainment: 15%
Utilities: 15%
Other: 15%
```

**Line Chart (Tendência Mensal)**
```
Mostra gastos total por mês últimos 12 meses
Com trend line
```

**Bar Chart (Top 5 Categorias)**
```
Food: R$ 1.200
Transport: R$ 800
Entertainment: R$ 600
...
```

### Acceptance Criteria

- [ ] Charts renderizam corretamente
- [ ] Dados atualizados em real-time
- [ ] Filtros funcionam (período, categoria)
- [ ] Responsivo em mobile
- [ ] Legend visível
- [ ] Hover tooltips

### Implementação

**Backend:**
```php
// GET /api/analytics/spending-by-category
// GET /api/analytics/monthly-trend
// GET /api/analytics/top-categories
```

**Frontend:**
```vue
<SpendingChart :data="store.chartData" />
<TrendChart :months="store.monthlyData" />
```

### Tests

```
✅ DATA: correct aggregation
✅ RENDER: charts display
✅ FILTER: period filtering
✅ RESPONSIVE: mobile friendly
```

---

## 📱 Feature 7: Authentication & Authorization

### Descrição
Login/signup com JWT tokens

### User Story

```
Como novo usuário
Quero criar conta e fazer login
Para acessar meus dados

Como usuário logado
Quero ser mantido logado
Para não fazer login toda vez
```

### Fluxo

```
Signup:
1. Form: email, password, name
2. Backend: validate, hash, create user
3. Return: JWT token
4. Frontend: save token, redirect

Login:
1. Form: email, password
2. Backend: validate, return JWT
3. Frontend: save token, redirect

Logout:
1. Frontend: delete token
2. Redirect: /login

Auto-refresh:
1. Token expira em 1h
2. Refresh token válido 30 dias
3. Auto-refresh antes de expirar
```

### Acceptance Criteria

- [ ] Validações (email, password strength)
- [ ] Password hasheado (bcrypt)
- [ ] JWT tokens funcionam
- [ ] Auto-refresh funciona
- [ ] Logout limpa tokens
- [ ] Protected routes bloqueiam unauth users

### Tests

```
✅ SIGNUP: create user, return token
✅ LOGIN: validate creds, return token
✅ REFRESH: get new token
✅ LOGOUT: clear tokens
✅ PROTECTED: reject unauth requests
```

---

## 🔗 Dependências entre Features

```
Auth (7)
  ↓
Dashboard (1) + TransactionManagement (2)
  ↓
AI Categorization (3) + Anomaly Detection (4)
  ↓
Budget Recommendations (5)
  ↓
Analytics (6)
```

---

## 📊 Feature Matrix

| Feature | Backend | Frontend | IA | Priority | Status |
|---------|---------|----------|----|----|--------|
| Dashboard | Light | Medium | No | P0 | Phase 4 |
| Transactions | Heavy | Medium | No | P0 | Phase 2 |
| Categorization | Medium | Light | Yes | P0 | Phase 3 |
| Anomalies | Medium | Light | Yes | P1 | Phase 3 |
| Recommendations | Medium | Medium | Yes | P1 | Phase 3 |
| Analytics | Light | Heavy | No | P1 | Phase 4 |
| Auth | Medium | Medium | No | P0 | Phase 2 |

---

## 🔗 Links Relacionados

- [BUSINESS_RULES.md](BUSINESS_RULES.md) - Regras que governam features
- [DEVELOPMENT_ROADMAP.md](DEVELOPMENT_ROADMAP.md) - Timeline de features
- [CONSTRAINTS.md](CONSTRAINTS.md) - Limitações por feature

---

<div align="center">

**Próximo passo:** [TECHNICAL_NOTES.md](TECHNICAL_NOTES.md) ↗️

</div>
