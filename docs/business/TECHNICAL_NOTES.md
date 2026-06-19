# 📝 Anotações Técnicas (Technical Notes)

> **Parte:** BUSINESS  
> **Propósito:** Documentar decisões técnicas e trade-offs  
> **Última atualização:** Junho 2026

---

## 🏗️ Decisões Arquiteturais

### Decision 1: Service Layer

**O quê:** Toda lógica de negócio em Services, não em Controllers

**Por quê?**
- ✅ Testável (moque Service)
- ✅ Reutilizável (múltiplos Controllers)
- ✅ Simples manutenção
- ✅ Separação de responsabilidades

**Trade-off:**
- ⚠️ Mais arquivos
- ⚠️ Mais abstrações
- ⚠️ Curva de aprendizado

**Quando quebra:**
- Lógica trivial (2-3 linhas)
- Raro usada
- Custo > benefício

**Alternativas consideradas:**
- Usar lógica diretamente em Controller (rejeitado)
- Action classes apenas (rejeitado - menos reutilizável)

---

### Decision 2: Repository Pattern

**O quê:** Abstrair acesso ao banco de dados

**Por quê?**
- ✅ Trocar banco sem mudar Service
- ✅ Queries complexas isoladas
- ✅ Cache centralizado
- ✅ Fácil mockar em testes

**Trade-off:**
- ⚠️ Overhead para queries simples
- ⚠️ Mais camadas de indireção

**Quando quebra:**
- Projeto muito pequeno
- Só uma model
- Queries super simples

---

### Decision 3: PostgreSQL (vs MySQL)

**O quê:** Usar PostgreSQL em vez de MySQL

**Por quê?**
- ✅ ACID compliance (transações financeiras!)
- ✅ JSON/JSONB support
- ✅ Índices avançados
- ✅ Melhor tipo sistema
- ✅ Array support nativo
- ✅ Melhor para analytics

**Trade-off:**
- ⚠️ Mais pesado que MySQL
- ⚠️ Menos hosting barato

**Quando rever:**
- Performance problem específico
- Precisa escalar horizontalmente

**Alternativas consideradas:**
- MySQL (rejeitado - ACID fraco)
- MongoDB (rejeitado - precisa de relações)

---

### Decision 4: Pinia (vs Vuex)

**O quê:** Usar Pinia para state management

**Por quê?**
- ✅ Oficial Vue (menor churn)
- ✅ Mais simples que Vuex
- ✅ TypeScript melhor
- ✅ Composable API
- ✅ Menor bundle size

**Trade-off:**
- ⚠️ Comunidade menor que Redux
- ⚠️ Novo (vs Vuex maduro)

**Quando muda:**
- Projeto muito grande (complexidade extrema)
- Precisa MobX-style reactivity

---

### Decision 5: IA = Claude (vs OpenAI)

**O quê:** Usar Claude API (Anthropic)

**Por quê?**
- ✅ Melhor reasoning (importante para finanças)
- ✅ Menos hallucinations
- ✅ Context window maior (100K tokens!)
- ✅ Prompt engineering mais simples
- ✅ Pricing competitivo
- ✅ Dados não são usados para training

**Trade-off:**
- ⚠️ Menor comunidade que OpenAI
- ⚠️ Menos exemplos publicamente

**Alternativas consideradas:**
- OpenAI GPT (rejeitado - mais hallucinations)
- Llama local (rejeitado - mais lento)
- Google PaLM (rejeitado - menos capaz)

---

## ⚠️ Trade-offs Importantes

### Laravel vs FastAPI

**Considerado:** Usar FastAPI (Python) para backend

**Rejeitado porque:**
- ❌ Requisito vaga é Laravel
- ❌ Ecossistema menor
- ❌ Menos pacotes prontos

**Mantemos:** Laravel

---

### GraphQL vs REST

**Considerado:** Usar GraphQL para API

**Rejeitado porque:**
- ❌ Maior complexidade
- ❌ Overkill para este projeto
- ❌ REST adequado e conhecido

**Mantemos:** REST

---

### Monolith vs Microserviços

**Considerado:** Separar em microsserviços

**Rejeitado porque:**
- ❌ Projeto pequeno
- ❌ Complexidade desnecessária
- ❌ Operacional overhead

**Mantemos:** Monolith (Laravel) com estrutura escalável

---

## 🔧 Soluções para Problemas Comuns

### Problema 1: N+1 Queries

**Solução:**
```php
// ❌ ERRADO
foreach ($transactions as $t) {
    echo $t->user->name; // Query por transação!
}

// ✅ CORRETO: Eager load
$transactions = Transaction::with('user')->get();
foreach ($transactions as $t) {
    echo $t->user->name; // Já loaded
}
```

**Prevention:**
- Usar `with()` sempre
- PHPStan verificar com plugin
- Tests para N+1 (query counter)

---

### Problema 2: Claude API Rate Limiting

**Solução:**
```php
// Implementar queue
AnalyzeTransactionJob::dispatch($transaction)
    ->delay(now()->addSeconds($randomDelay));

// Ou usar circuit breaker
CircuitBreaker::attempt(
    fn() => $this->aiService->analyze($transaction),
    fallback: fn() => $this->defaultCategory
);
```

**Prevention:**
- Cache respostas (24h)
- Rate limit internamente (10/min)
- Monitoring de custos

---

### Problema 3: Transações não foram salvas

**Solução:**
```php
// Use DB::transaction para múltiplas operações
DB::transaction(function () {
    $transaction = Transaction::create($data);
    AIAnalysis::create(['transaction_id' => $transaction->id]);
    Cache::forget("user_{$userId}_stats");
    
    // Se qualquer query falhar, tudo é rolled back
});
```

---

### Problema 4: Frontend não atualiza quando backend muda

**Solução:**
```vue
// ✅ Use store.transactions (reativo)
const store = useTransactionStore()
watch(() => store.transactions, () => {
    // Re-render automático
})

// ❌ Evite estado local duplicado
const localTransactions = ref([])
// Sync problems!
```

---

## 🐛 Known Issues & Workarounds

### Issue 1: Chart.js com Vue 3

**Problema:** Re-renders causam flickering  
**Workaround:** Usar `key` para forçar re-render correto

```vue
<SpendingChart :key="store.selectedMonth" />
```

---

### Issue 2: PostgreSQL JSON casting

**Problema:** JSON fields precisam de cast explícito  
**Workaround:** Usar `cast` na model

```php
protected $casts = [
    'metadata' => 'json',
];
```

---

### Issue 3: Redis connection timeout

**Problema:** Redis falha em desenvolvimento  
**Workaround:** Usar `database` cache se Redis offline

```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'database'),
```

---

## 📊 Performance Decisions

### Caching Strategy

```
Layer 1: HTTP cache (CDN, browser)
  └─ Static assets: 1 year

Layer 2: Redis cache (server)
  ├─ IA responses: 24h
  ├─ User stats: 1h
  └─ Categories: 7 days

Layer 3: Query optimization
  ├─ Índices database
  ├─ Eager loading
  └─ Pagination
```

### Database Indexes

```
Created:
├─ (user_id) - frequente filter
├─ (created_at) - sorting
├─ (user_id, created_at) - composite
└─ Full-text em description (futura)

Resultado:
├─ Queries < 100ms (P95)
└─ Scanning rápido em 1M+ rows
```

---

## 🔒 Security Decisions

### Why JWT over Sessions?

```
✅ JWT:
├─ Stateless (scalável)
├─ Funciona em múltiplos servidores
├─ Mobile-friendly

⚠️ Sessions:
├─ State no servidor
└─ Difícil com APIs
```

### Why bcrypt not salted SHA-256?

```
✅ bcrypt:
├─ Slow by design (resiste brute force)
├─ Adaptive (pode aumentar rounds)
├─ Built-in salt

❌ SHA-256:
├─ Fast (bom para hackers)
└─ Não adaptável
```

---

## 📈 Scalability Considerations

### Current (1.0.0)
```
Suporta: ~1.000 usuários
Transações: ~1M por usuário
Sem problema com:
├─ Monolith Laravel
├─ PostgreSQL único
├─ Redis local
└─ Servidor 2 vCPU
```

### Quando escalar

```
Limite: ~10.000 usuários

Mudanças necessárias:
├─ Load balance Laravel (2-3 instâncias)
├─ PostgreSQL read replicas
├─ Redis cluster
├─ CDN para assets
└─ Message queue (RabbitMQ)
```

---

## 📝 Learnings & Insights

### O que funcionou bem

✅ Service Layer - Fácil testar  
✅ Repository Pattern - Flexível para mudanças DB  
✅ Pinia stores - Simples state management  
✅ Docker - Dev/prod consistency  
✅ GitHub Actions - CI/CD automático  

### O que poderia melhorar

⚠️ Mais validações frontend  
⚠️ WebSocket para real-time (futura)  
⚠️ API versioning desde o início  
⚠️ More granular permissions  
⚠️ Event sourcing (futuro)  

---

## 🔗 Links Relacionados

- [CONSTRAINTS.md](CONSTRAINTS.md) - Limitações técnicas
- [DESIGN_PATTERNS.md](../Architecture/DESIGN_PATTERNS.md) - Padrões implementados
- [TECHNOLOGY_STACK.md](../Architecture/TECHNOLOGY_STACK.md) - Tecnologias usadas

---

<div align="center">

**Próximo passo:** [CONSTRAINTS.md](CONSTRAINTS.md) ↗️

</div>
