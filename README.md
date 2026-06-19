# AI-Powered Financial Transaction Analyzer

**Stack:** Laravel 12 · Vue 3 · Claude AI · PostgreSQL · Redis · Docker  
**Status:** Em desenvolvimento · v1.0.0

> Sistema full-stack para análise inteligente de transações financeiras. Combina Laravel 12 e Vue 3 com a Claude API para categorização automática, detecção de anomalias e recomendações de orçamento em tempo real.

---

## Visão Geral

O sistema permite importar transações financeiras (Pix, cartão, transferências), que são processadas automaticamente pela Claude AI para categorização, identificação de padrões incomuns e geração de recomendações personalizadas de economia.

**Principais features:**
- Categorização automática de transações via Claude AI
- Detecção de anomalias de gasto com alertas
- Dashboard com analytics e gráficos interativos
- Importação em lote via CSV
- Recomendações de orçamento geradas por IA
- API REST paginada com autenticação JWT

---

## Stack Tecnológico

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| Frontend | Vue.js + TypeScript | 3.4+ / 5.0+ |
| Frontend | Tailwind CSS + Pinia | 3.x / 2.x |
| Backend | Laravel + PHP | 12 / 8.3+ |
| Database | PostgreSQL | 15+ |
| Cache / Queue | Redis | 7+ |
| IA | Claude API | claude-opus-4-8 |
| Infra | Docker + GitHub Actions | 24+ |

---

## Pré-requisitos

- Docker 24+ e Docker Compose 2.x
- Claude API Key ([obter aqui](https://console.anthropic.com))
- Git

---

## Quick Start

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/ai-financial-analyzer.git
cd ai-financial-analyzer

# 2. Configure o ambiente
cp backend/.env.example backend/.env
# Edite backend/.env e adicione sua CLAUDE_API_KEY

# 3. Suba os serviços
docker-compose up -d

# 4. Prepare o banco de dados
docker-compose exec app php artisan migrate --seed

# 5. Instale dependências do frontend
docker-compose exec frontend npm install
```

**Acesso:**

| Serviço | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000 |
| API Docs | http://localhost:8000/api/docs |

---

## Comandos Úteis

```bash
# Testes
docker-compose exec app php artisan test
docker-compose exec frontend npm run test

# Com coverage
docker-compose exec app php artisan test --coverage
docker-compose exec frontend npm run test:coverage

# Lint e formatação
docker-compose exec app ./vendor/bin/pint
docker-compose exec frontend npm run lint

# Filas (processamento IA)
docker-compose exec app php artisan queue:work

# Logs
docker-compose logs -f app
docker-compose logs -f frontend
```

---

## Arquitetura

```
Frontend (Vue 3 SPA)
    │  REST API (JWT)
    ▼
Backend (Laravel 12)
  ├─ Controllers → Services → Repositories → PostgreSQL
  └─ AIAnalysisService → Claude API (async via Queue)
                                  │
                              Redis (cache 24h)
```

O backend segue **Service Layer + Repository Pattern**. A análise de IA é disparada de forma assíncrona via jobs enfileirados no Redis, evitando bloqueio nas requisições.

Detalhes em [`Docs/Architecture/`](Docs/Architecture/).

---

## Estrutura de Pastas

```
ai-financial-analyzer/
├── Docs/                    # Documentação completa
│   ├── GETTING_STARTED.md   # Ponto de entrada da documentação
│   ├── Architecture/        # Padrões, stack, API
│   └── Business/            # Regras de negócio, roadmap, features
├── backend/                 # Laravel 12 (a implementar)
├── frontend/                # Vue 3 (a implementar)
├── .github/workflows/       # CI/CD (a implementar)
├── docker-compose.yml       # Ambiente de desenvolvimento
└── Dockerfile               # Imagem de produção
```

---

## Documentação

| Documento | Conteúdo |
|-----------|----------|
| [`Docs/GETTING_STARTED.md`](Docs/GETTING_STARTED.md) | Guia completo de onboarding |
| [`Docs/Architecture/API_ARCHITECTURE.md`](Docs/Architecture/API_ARCHITECTURE.md) | Endpoints e fluxos da API |
| [`Docs/Architecture/DESIGN_PATTERNS.md`](Docs/Architecture/DESIGN_PATTERNS.md) | Padrões arquiteturais usados |
| [`Docs/Architecture/TECHNOLOGY_STACK.md`](Docs/Architecture/TECHNOLOGY_STACK.md) | Stack completo com justificativas |
| [`Docs/Business/BUSINESS_RULES.md`](Docs/Business/BUSINESS_RULES.md) | Regras de negócio |
| [`Docs/Business/DEVELOPMENT_ROADMAP.md`](Docs/Business/DEVELOPMENT_ROADMAP.md) | Fases e timeline do projeto |

---

## Licença

MIT License — sinta-se livre para usar este projeto como referência.
