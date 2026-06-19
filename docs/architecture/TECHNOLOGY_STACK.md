# 💻 Technology Stack

> **Parte:** ARCHITECTURE  
> **Propósito:** Documentar todas as tecnologias usadas  
> **Última atualização:** Junho 2026

---

## 📊 Overview do Stack

```
🎯 OBJETIVO: Demonstrar domínio sênior de tech stack moderno
📍 CONTEXTO: Vaga Senior Full Stack Laravel + Vue (Financeiro)
✅ REQUISITOS COBERTOS: Todos da vaga Sioux + Liqd
```

---

## 🎯 Frontend Stack

### Core Technologies

| Tech | Versão | Propósito | Por que? |
|------|--------|----------|---------|
| **Vue.js** | 3.4+ | Framework reativo | Requisito vaga, Composition API moderno |
| **TypeScript** | 5.0+ | Type safety | Evita bugs em runtime, DX melhor |
| **Vite** | 5.x | Build tool | 10x mais rápido que Webpack, HMR perfeito |
| **Tailwind CSS** | 3.x | Styling | Utility-first, production-ready, rapid design |
| **Pinia** | 2.x | State management | Official Vue state, mais simples que Vuex |

### UI & Visualization

| Tech | Versão | Propósito |
|------|--------|----------|
| **Chart.js** | 4.x | Gráficos de dados |
| **ApexCharts** | 3.x | Gráficos avançados (alternativa) |
| **HeadlessUI** | 1.x | Componentes sem estilo |
| **Heroicons** | 2.x | Ícones SVG |

### Development Tools

| Tool | Versão | Propósito |
|------|--------|----------|
| **Vitest** | Latest | Unit testing (rápido, Vite-native) |
| **Vue Test Utils** | 2.x | Testar componentes Vue |
| **ESLint** | 8.x | Lint JavaScript/TypeScript |
| **Prettier** | 3.x | Format code |
| **Cypress** | Latest | E2E testing (opcional) |

### Dependencies

```json
{
  "dependencies": {
    "vue": "^3.4.0",
    "axios": "^1.6.0",
    "pinia": "^2.1.0",
    "chart.js": "^4.4.0",
    "tailwindcss": "^3.3.0"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.0.0",
    "typescript": "^5.3.0",
    "vitest": "^1.0.0",
    "@vue/test-utils": "^2.4.0",
    "eslint": "^8.56.0",
    "prettier": "^3.1.0",
    "tailwindcss": "^3.3.0"
  }
}
```

---

## 🐘 Backend Stack

### Core Technologies

| Tech | Versão | Propósito | Por que? |
|------|--------|----------|---------|
| **Laravel** | 12 | Framework web | Requisito vaga, ecosystem robusto |
| **PHP** | 8.3+ | Linguagem | Moderna, typed properties, match expressions |
| **PostgreSQL** | 15+ | Database principal | ACID, jsonb, índices avançados, relações |
| **Redis** | 7+ | Cache/Sessions | Performance, pub/sub, queues |

### AI & External Services

| Service | Versão | Propósito | Custo |
|---------|--------|----------|-------|
| **Claude API** | claude-opus-4-8 | Análise inteligente | ~$15/mês uso leve |
| **Anthropic SDK** | Latest | Client PHP | Integração oficial |

### Development Tools

| Tool | Versão | Propósito |
|------|--------|----------|
| **PHPUnit** | 11+ | Unit/Feature testing |
| **Pest** | Latest | Testing alternative (prettier syntax) |
| **PHPStan** | Latest | Static analysis |
| **PHP CS Fixer** | Latest | Code formatting |
| **laravel/horizon** | Latest | Background tasks / Queue monitoring |
| **Telescope** | Latest | Debugging tool (dev only) |

### Packages Principais

```json
{
  "require": {
    "laravel/framework": "12.*",
    "laravel/tinker": "^2.8",
    "anthropic-sdk/sdk": "^1.0",
    "illuminate/auth": "12.*",
    "illuminate/cache": "12.*",
    "illuminate/queue": "12.*"
  },
  "require-dev": {
    "phpunit/phpunit": "^11.0",
    "laravel/pint": "^1.13",
    "phpstan/phpstan": "^1.10"
  }
}
```

### Laravel Packages

```php
// Oficiais Laravel
- laravel/framework       (Core)
- laravel/tinker         (CLI shell)
- laravel/sail           (Docker)
- laravel/sanctum        (API auth)

// Community
- spatie/laravel-permission (Roles/Permissions)
- spatie/laravel-query-builder (Query builder)
- laravel/telescope (Debug)
```

---

## 🐳 DevOps & Infrastructure

### Containerization

| Tech | Versão | Propósito |
|------|--------|----------|
| **Docker** | 24+ | Containerização |
| **Docker Compose** | 2.x | Multi-container |

### CI/CD

| Tool | Versão | Propósito |
|------|--------|----------|
| **GitHub Actions** | Native | Automated testing/deploy |
| **PHPUnit** | 11+ | Run tests |
| **Vitest** | Latest | Test frontend |

### Cloud & Deployment

| Platform | Uso | Staging |
|----------|-----|---------|
| **Railway** | Deploy | mysql-free, postgres-free |
| **AWS** | Optional | EC2, RDS, CloudFront |
| **DigitalOcean** | Optional | App Platform |

### Environment

```
Development:
├─ Docker Compose (local)
├─ PostgreSQL container
└─ Redis container

Staging:
├─ Railway (free tier)
├─ PostgreSQL managed
└─ Redis managed

Production:
├─ Cloud provider (AWS/DO/Railway)
├─ PostgreSQL managed
├─ Redis managed
└─ CDN para assets
```

---

## 📚 Testing Stack

### Backend Testing

```php
// PHPUnit structure
tests/
├─ Unit/
│  ├─ Services/AIAnalysisServiceTest.php
│  └─ Models/TransactionTest.php
├─ Feature/
│  ├─ TransactionApiTest.php
│  └─ AuthApiTest.php
└─ TestCase.php (base class)

// Exemplo test
#[Test]
public function it_analyzes_transaction() {
    $transaction = Transaction::factory()->create();
    
    $analysis = $this->service->analyze($transaction);
    
    $this->assertIsArray($analysis);
    $this->assertArrayHasKey('category', $analysis);
}

// Comando
php artisan test
php artisan test --coverage
php artisan test tests/Unit/Services/AIAnalysisServiceTest.php
```

### Frontend Testing

```ts
// Vitest structure
__tests__/
├─ unit/
│  ├─ composables/useTransactions.test.ts
│  └─ stores/transactions.test.ts
└─ integration/
   └─ pages/Dashboard.test.ts

// Exemplo test
it('fetches transactions', async () => {
  const store = useTransactionStore()
  await store.fetch()
  
  expect(store.transactions).toBeDefined()
  expect(store.transactions.length).toBeGreaterThan(0)
})

// Comando
npm run test
npm run test:coverage
npm run test -- useTransactions
```

---

## 📱 Code Quality Tools

### Linting & Formatting

| Tool | Configuração | Comando |
|------|--------------|---------|
| **ESLint** | `.eslintrc.cjs` | `npm run lint` |
| **Prettier** | `.prettierrc.json` | `npm run format` |
| **PHPStan** | `phpstan.neon` | `./vendor/bin/phpstan analyse` |
| **Pint** | `pint.json` | `./vendor/bin/pint` |

### Pre-commit Hooks

```yaml
# .husky/pre-commit
#!/bin/sh

# Frontend lint
npm run lint -- --fix

# Backend lint
./vendor/bin/pint

# Testes (opcional)
npm run test -- --run
php artisan test
```

---

## 🔒 Security Stack

| Aspecto | Implementação |
|---------|---------------|
| **Auth** | Laravel Sanctum (API tokens) |
| **Password** | bcrypt (Laravel) |
| **CSRF** | Token automaticamente |
| **XSS** | Vue escapes automaticamente |
| **SQL Injection** | Eloquent prepared statements |
| **API Rate Limiting** | Middleware built-in |
| **Secrets** | Environment variables |
| **CORS** | Configurável in config/ |

---

## 📊 Performance Stack

| Tech | Propósito | Config |
|------|-----------|--------|
| **Redis** | Cache queries | TTL: 1h |
| **Query caching** | Evitar N+1 | Eager load |
| **Asset minification** | Vite production | Automático |
| **Gzip compression** | Nginx/Apache | Configurar |
| **CDN** | Static assets | CloudFront/CloudFlare |

---

## 🗂️ File Structure by Tech

```
Frontend:
├─ node_modules/          (npm packages)
├─ src/
│  ├─ components/         (Vue components)
│  ├─ pages/             (Page-level)
│  ├─ stores/            (Pinia)
│  ├─ services/          (API clients)
│  ├─ types/             (TypeScript types)
│  └─ main.ts            (Entry point)
├─ vite.config.ts        (Vite config)
├─ tsconfig.json         (TypeScript config)
├─ eslintrc.cjs          (ESLint config)
└─ vitest.config.ts      (Test config)

Backend:
├─ vendor/               (Composer packages)
├─ app/
│  ├─ Http/             (Controllers, Requests)
│  ├─ Models/           (Eloquent models)
│  ├─ Services/         (Business logic)
│  └─ Repositories/     (Data access)
├─ config/              (Configuration)
├─ database/            (Migrations, seeders)
├─ routes/              (Route definitions)
├─ tests/               (PHPUnit tests)
├─ .env.example         (Env template)
├─ composer.json        (Dependencies)
└─ artisan              (CLI tool)
```

---

## 🚀 Versioning Strategy

### Semantic Versioning

```
1.2.3
│ │ │
│ │ └─ Patch (fixes)
│ └──── Minor (features, backward-compatible)
└────── Major (breaking changes)

Current: 1.0.0
```

### Dependency Updates

- **Monthly:** Review security updates
- **Quarterly:** Update non-breaking packages
- **Yearly:** Plan major version upgrades

---

## 📈 Technology Decisions

### Por que Vue 3?

✅ Requisito vaga  
✅ Composition API moderno  
✅ Type-safe com TypeScript  
✅ Performance excelente  
✅ Comunidade ativa  

### Por que Laravel 12?

✅ Requisito vaga  
✅ Ecosystem maduro  
✅ Built-in features (auth, validation)  
✅ Performance ótimo  
✅ Community suporte  

### Por que PostgreSQL?

✅ Relacional (transações financeiras)  
✅ ACID compliance  
✅ JSON support (jsonb)  
✅ Índices avançados  
✅ Escalável  

### Por que Claude API?

✅ Melhor reasoning para financeiro  
✅ Modelo mais capaz (claude-opus-4-8)  
✅ Integração simples  
✅ Pricing justo  
✅ Documentação excelente  

---

## 🔄 Upgrade Path

### Frontend
```
Vue 2 → Vue 3 ✅ (já feito)
JS → TypeScript ✅ (já feito)
Webpack → Vite ✅ (já feito)
```

### Backend
```
Laravel 10 → 12 ✅ (já feito)
PHP 8.1 → 8.3+ ✅ (já feito)
```

### Database
```
MySQL → PostgreSQL ✅ (já feito)
```

---

## 🔗 Links Úteis

- [Vue.js Docs](https://vuejs.org)
- [Laravel Docs](https://laravel.com)
- [PostgreSQL Docs](https://postgresql.org)
- [Claude API Docs](https://anthropic.com/docs)
- [Docker Docs](https://docker.com)

---

<div align="center">

**Próximo passo:** Leia [BUSINESS_RULES.md](../Business/BUSINESS_RULES.md) para entender regras de negócio ↗️

</div>
