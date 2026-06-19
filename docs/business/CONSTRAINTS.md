# ⚠️ Restrições & Limitações do Projeto (Constraints)

> **Parte:** BUSINESS  
> **Propósito:** Documentar limitações e constraints conhecidas  
> **Última atualização:** Junho 2026

---

## 📌 Scope Original

### In Scope ✅

```
✅ Importar transações (CSV, manual)
✅ Categorizar automaticamente com IA
✅ Detectar anomalias
✅ Recomendações de orçamento
✅ Dashboard com analytics
✅ Autenticação básica
✅ Testes unitários/feature
✅ CI/CD pipeline
✅ Documentação
```

### Out of Scope ❌

```
❌ Pix integration (API real - seria integração complexa)
❌ Blockchain features
❌ Mobile app native
❌ Multi-currency
❌ AI model training customizado
❌ API pagada (freemium)
❌ Internacionalização (i18n)
❌ Micro-frontend architecture
```

---

## 🔒 Restrições Técnicas

### Backend

#### Limite de Memória
```
Current: 256MB (Docker)
Impact: ~1000 concurrent requests antes de OOM

Quando problema: Muito processamento AI paralelo
Solução: Aumentar container, usar queue, cache
```

#### Database Connection Pool
```
Current: 5 connections (PostgreSQL)
Impact: ~50-100 requisições/segundo

Quando problema: Muitos usuários simultâneos
Solução: Aumentar pool, add read replicas
```

#### IA API Rate Limit
```
Claude API:
├─ Free tier: ~100 requests/day
├─ Paid: ~1000 requests/day
└─ Burst: 10 simultaneous

Solução implementada:
├─ Cache 24h
├─ Queue com delay
├─ Circuit breaker para fallback
```

### Frontend

#### Bundle Size
```
Current: ~250KB (minified + gzip)
Target: < 300KB

Quando problema: Lento em 3G
Solução: Lazy load components, optimize assets
```

#### Local Storage
```
Current: ~5MB max no browser
Impact: Caching limitado

Não usamos para dados principais (BD é source of truth)
```

---

## 📊 Limitações de Dados

### User Limits

```
Por usuário:
├─ Máx transações: 1.000.000 (teórico)
├─ Máx por dia: 1.000
├─ Máx por hora: 100
└─ Máx simultaneamente: 10

Limite arquivo importação:
├─ Máx 50MB
├─ Máx 10.000 linhas
└─ Máx 1 arquivo/minuto
```

### Transação Limits

```
Amount:
├─ Mín: 0.01
├─ Máx: 100.000

Description:
├─ Mín: 3 caracteres
├─ Máx: 255 caracteres

Date:
├─ Mín: 10 anos atrás
├─ Máx: Hoje
└─ Não suporta data futura
```

### Analytics

```
História máxima: 5 anos
Agregação automática:
├─ > 1 ano: agregar por mês
├─ > 5 anos: agregar por trimestre
└─ Motivo: performance
```

---

## ⏱️ Restrições de Performance

### Response Time SLA

```
API Endpoints:
├─ Dashboard: < 200ms (P95)
├─ List transações: < 300ms (P95)
├─ Create: < 100ms (P95)
├─ AI Analysis: < 5000ms (vem de API externa)
└─ Analytics: < 500ms (P95)
```

### Caching Constraints

```
Não cacheable:
├─ Dados do usuário logado (pode mudar)
├─ Balances (precisa ser real-time)
└─ Anomalias (recentes)

Cacheable:
├─ Categorias: 7 dias
├─ IA responses: 24 horas
├─ User stats: 1 hora
└─ Tags: 30 dias
```

---

## 🔐 Restrições de Segurança

### Authentication

```
JWT Token:
├─ Válido por: 1 hora
├─ Refresh by: 30 dias
└─ Max simultâneo: 5 tokens por usuário

Password:
├─ Mín 8 caracteres
├─ Deve ter: maiúscula + minúscula + número
└─ Expire: Nunca (user manda reset)
```

### Rate Limiting

```
API:
├─ 100 requests/minuto por IP
├─ 1000 requests/hora por user
└─ 404 = block por 1 minuto

Login:
├─ Max 5 tentativas/hora
└─ Lockout: 15 minutos
```

### CORS

```
Allowed origins:
├─ http://localhost:5173 (dev)
├─ https://example.com (prod)
└─ https://app.example.com (prod)

Forbidden:
├─ File:// (local files)
└─ Credentials via wildcard
```

---

## 🌍 Restrições Geográficas/Legais

### Data Residency

```
Current: Sem restrição (global)
Future: Pode precisar em região específica

Implicação: DB/Redis devem estar no mesmo local
Custo: Caches distribuídos, replicação
```

### LGPD (Lei Geral de Proteção de Dados)

```
Compliance:
├─ Direito ao esquecimento (soft delete)
├─ Acesso aos dados (API JSON)
├─ Portabilidade (export CSV)
└─ Consentimento (terms acceptance)

Implementado:
├─ Privacy policy
├─ Terms of service
├─ Audit logs (30 dias)
└─ Data deletion (anonymize, não hard delete)
```

---

## 🏗️ Restrições Arquiteturais

### Monolith

```
Current: Monolith Laravel + Vue SPA

Restrição:
├─ Escala: ~10.000 usuários
├─ Deploy: Tudo junto ou nada
└─ Resourcing: Single point of failure

Quando migrar para microsserviços:
├─ Usuários: >50.000
├─ Transações: >100M/dia
├─ Features: Totalmente diferentes
└─ Teams: > 10 desenvolvedores
```

### Database

```
Single PostgreSQL:
├─ Único ponto de falha
├─ Scaling: Vertical (mais CPU/RAM)
└─ Backup: Manual por agora

Melhorias futuras:
├─ Primary-replica
├─ Sharding por user_id
└─ Read-only replicas
```

---

## 🤖 Restrições de IA

### Claude API

```
Model: claude-opus-4-8
├─ Context window: 100K tokens
├─ Latency: 1-5 segundos típico
├─ Availability: 99.9% SLA
└─ Cost: Tokens in/out

Limitações conhecidas:
├─ Pode hallucinar categorias
├─ Confiança nem sempre acurada
├─ Não treina em dados user
└─ Requer internet (offline impossible)
```

### Fallback Strategy

```
Se Claude falhar:
1. Tenta de novo (3x com backoff)
2. Se still falha:
   ├─ Retorna categoria genérica
   ├─ Confidence = 0
   ├─ Marca como needs_review
   └─ Notifica user

Impact: Feature degradada mas não quebrada
```

---

## 📱 Restrições de Browser

### Frontend Compatibility

```
Supported:
├─ Chrome 90+
├─ Firefox 88+
├─ Safari 14+
├─ Edge 90+
└─ Modern mobile browsers

NOT Supported:
├─ IE 11 (ES6 não suportado)
├─ Navegadores muito antigos
└─ Experimental features
```

### Feature Detection

```
Usamos:
├─ LocalStorage (required)
├─ IndexedDB (for offline draft, nice to have)
├─ WebSocket (para real-time, v2.0)
└─ Service Workers (PWA, v2.0)
```

---

## 💾 Restrições de Deployment

### Hosting

```
Requisitos mínimos:
├─ 2 vCPU
├─ 4 GB RAM
├─ 20 GB SSD
└─ Banda: 100 Mbps

Testado em:
├─ Docker (desarrollo)
├─ Railway (production)
├─ AWS EC2 (compatible)
└─ DigitalOcean App Platform (compatible)

NÃO testado em:
├─ Heroku (fim do suporte free)
├─ Vercel (serverless)
└─ Serverless (cold starts problems)
```

### Deployment Frequency

```
Current: Manual push to Railway
Frequency: Ad-hoc
Downtime: ~2 minutos

Automated: GitHub Actions (v1.1)
Frequency: Every push to main
Downtime: Zero (blue-green deploy)
```

---

## ⏰ Restrições de Tempo (Time Constraints)

### Development Timeline

```
Fase 1: 2-3 horas
Fase 2: 8-10 horas
Fase 3: 6-8 horas
Fase 4: 10-12 horas
Fase 5: 6-8 horas
─────────────────
Total: 35-45 horas

Assumindo: 10 horas/semana
Duration: 4-5 semanas
```

### Maintenance

```
Expected: ~5 horas/mês
├─ Dependency updates
├─ Security patches
├─ Bug fixes
└─ Documentation

If 0 maintenance: Technical debt acumula
```

---

## 🎯 Conhecido Bugs & Workarounds

### Bug 1: Race Condition em Categorização

**Problema:**
```
User edit transação enquanto IA categoriza
Resultado: Sobrescreve categoria manualmente

Workaround:
├─ Mostrar "analyzing..." state
├─ Bloquear edição enquanto IA trabalha
└─ User vê resultado antes de editar
```

### Bug 2: Cache Invalidation Falha Rara

**Problema:**
```
Stats desatualizado raramente

Workaround:
├─ User pode fazer "Refresh" manual
├─ Auto-refresh a cada 5 minutos
└─ Log issue para debug
```

---

## 🔄 Quando Rever Constraints

### Semestralmente

```
Review:
├─ Usage metrics
├─ Performance benchmarks
├─ User feedback
└─ Cloud provider limits

Decisões:
├─ Escalar?
├─ Otimizar?
├─ Refatorar?
└─ Migrar?
```

---

## ✅ Checklist: Entendo as Constraints?

- [ ] Máx de usuários suportados
- [ ] Máx de transações
- [ ] Response time targets
- [ ] Restrições IA/Claude
- [ ] Limites de rate limiting
- [ ] Quando escalar
- [ ] Segurança/compliance
- [ ] Browser support
- [ ] Deployment constraints

---

## 🔗 Links Relacionados

- [TECHNICAL_NOTES.md](TECHNICAL_NOTES.md) - Decisões que resultaram nessas constraints
- [DEVELOPMENT_ROADMAP.md](DEVELOPMENT_ROADMAP.md) - Timeline, também limitado
- [BUSINESS_RULES.md](BUSINESS_RULES.md) - Regras que definem constraints

---

<div align="center">

**Parabéns!** Você chegou ao fim da documentação! 🎉

Para voltar ao índice geral: [GETTING_STARTED.md](../GETTING_STARTED.md) ↗️

</div>
