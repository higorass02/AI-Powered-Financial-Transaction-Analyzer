# 🔌 API Architecture

> **Parte:** ARCHITECTURE  
> **Propósito:** Documentar estrutura e endpoints da API  
> **Última atualização:** Junho 2026

---

## 🎯 Overview da API

```
Type:        RESTful JSON API
Auth:        JWT (Bearer tokens)
Version:     v1
Port:        8000
Base URL:    http://localhost:8000/api/
Content:     application/json
```

---

## 🔐 Autenticação

Todos endpoints (exceto auth) requerem header:

```bash
Authorization: Bearer {jwt_token}
```

### Endpoints de Auth

```
POST /api/auth/register
├─ Body: { email, password, name }
└─ Response: { token, user }

POST /api/auth/login
├─ Body: { email, password }
└─ Response: { token, user, refresh_token }

POST /api/auth/refresh
├─ Body: { refresh_token }
└─ Response: { token }

POST /api/auth/logout
├─ Response: { success: true }
```

---

## 📋 Transações (Transactions)

### List Transactions

```
GET /api/transactions

Query Parameters:
├─ page: integer (default: 1)
├─ per_page: integer (default: 20, max: 100)
├─ sort_by: string (date, amount, default: date)
├─ sort_order: asc|desc (default: desc)
├─ category_id: integer (filter)
├─ type: debit|credit|pix (filter)
├─ from_date: date (YYYY-MM-DD)
└─ to_date: date (YYYY-MM-DD)

Response:
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "description": "UBER BRASIL",
      "amount": 45.50,
      "type": "debit",
      "category_id": 5,
      "category": { "id": 5, "name": "Transportation" },
      "date": "2024-01-15",
      "created_at": "2024-01-15T10:00:00Z",
      "updated_at": "2024-01-15T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8
  }
}
```

### Get Single Transaction

```
GET /api/transactions/{id}

Response:
{
  "data": {
    "id": 1,
    "description": "UBER BRASIL",
    "amount": 45.50,
    ...
    "ai_analysis": {
      "category": "Transportation",
      "confidence": 0.95,
      "is_anomaly": false,
      "created_at": "2024-01-15T10:05:00Z"
    }
  }
}
```

### Create Transaction

```
POST /api/transactions

Body:
{
  "description": "UBER BRASIL",
  "amount": 45.50,
  "type": "debit",
  "category_id": null,
  "date": "2024-01-15"
}

Response: 201 Created
{
  "data": {
    "id": 1,
    "description": "UBER BRASIL",
    "amount": 45.50,
    "type": "debit",
    "category_id": 5,
    "category": { "id": 5, "name": "Transportation" },
    "ai_analysis": {
      "category": "Transportation",
      "confidence": 0.95
    },
    "created_at": "2024-01-15T10:00:00Z"
  }
}
```

### Update Transaction

```
PUT /api/transactions/{id}

Body: (todos os campos opcionais)
{
  "description": "UBER - SP",
  "amount": 50.00,
  "category_id": 5
}

Response: 200 OK (mesmo schema que GET)
```

### Delete Transaction (Soft Delete)

```
DELETE /api/transactions/{id}

Response: 204 No Content

Nota: Soft delete - dados continuam no BD
      Pode fazer undo por 10 segundos
```

### Import Transactions (CSV)

```
POST /api/transactions/import

Body (multipart/form-data):
├─ file: file (CSV)
└─ auto_categorize: boolean (default: true)

CSV Format:
description,amount,type,date
UBER BRASIL,45.50,debit,2024-01-15
SALÁRIO,5000.00,credit,2024-01-01

Response: 201 Created
{
  "data": {
    "imported": 100,
    "errors": 2,
    "error_details": [
      {
        "line": 5,
        "error": "Invalid amount: abc"
      }
    ]
  }
}
```

---

## 🤖 IA Analysis

### Analyze Single Transaction

```
POST /api/transactions/{id}/analyze

Body (opcional):
{
  "scope": "detailed"  // basic|detailed
}

Response: 200 OK
{
  "data": {
    "transaction_id": 1,
    "analysis": {
      "category": "Transportation",
      "confidence": 0.95,
      "is_anomaly": false,
      "reasoning": "Matches historical Uber pattern"
    }
  }
}
```

### Get AI Insights

```
GET /api/ai/insights

Query Parameters:
├─ period: day|week|month (default: month)
└─ include: anomalies,recommendations (default: both)

Response: 200 OK
{
  "data": {
    "anomalies": [
      {
        "transaction_id": 42,
        "description": "LUXURY STORE",
        "amount": 5000.00,
        "confidence": 0.98,
        "reason": "5x above average for category"
      }
    ],
    "recommendations": [
      {
        "category": "Food",
        "current_spending": 1200.00,
        "suggested_budget": 800.00,
        "savings": 400.00,
        "reasoning": "Can reduce by 30% based on analysis"
      }
    ]
  }
}
```

### Get Budget Recommendations

```
GET /api/ai/recommendations

Query Parameters:
├─ period: month|quarter|year (default: month)
└─ depth: basic|detailed (default: detailed)

Response: 200 OK
{
  "data": {
    "recommendations": [
      {
        "action": "Reduce streaming subscriptions",
        "current_cost": 150.00,
        "potential_savings": 100.00,
        "items": [
          { "name": "Netflix", "cost": 50.00 },
          { "name": "Spotify", "cost": 50.00 }
        ]
      },
      {
        "action": "Optimize food spending",
        "current_cost": 1200.00,
        "potential_savings": 400.00,
        "reasoning": "30% above average for similar users"
      }
    ]
  }
}
```

---

## 📊 Analytics

### Dashboard Summary

```
GET /api/dashboard/summary

Response: 200 OK
{
  "data": {
    "balance": {
      "total": 5420.50,
      "currency": "BRL"
    },
    "current_month": {
      "income": 7500.00,
      "spending": 2150.00,
      "net": 5350.00
    },
    "budget": {
      "total": 5000.00,
      "spent": 2150.00,
      "percentage": 43
    },
    "period": "2024-01"
  }
}
```

### Spending by Category

```
GET /api/analytics/spending-by-category

Query Parameters:
├─ from_date: YYYY-MM-DD
└─ to_date: YYYY-MM-DD

Response: 200 OK
{
  "data": [
    {
      "category_id": 5,
      "category_name": "Food",
      "amount": 1200.00,
      "percentage": 35,
      "transactions_count": 50,
      "average_transaction": 24.00
    },
    {
      "category_id": 2,
      "category_name": "Transportation",
      "amount": 800.00,
      "percentage": 23,
      ...
    }
  ]
}
```

### Monthly Trend

```
GET /api/analytics/monthly-trend

Query Parameters:
├─ months: integer (default: 12)
└─ include_forecast: boolean (default: false)

Response: 200 OK
{
  "data": [
    {
      "month": "2024-01",
      "income": 7500.00,
      "spending": 2150.00,
      "net": 5350.00,
      "transactions_count": 120
    },
    {
      "month": "2024-02",
      "income": 7500.00,
      "spending": 1950.00,
      "net": 5550.00,
      "transactions_count": 95
    }
  ],
  "forecast": [
    {
      "month": "2024-03",
      "forecast_spending": 2100.00
    }
  ]
}
```

---

## 📁 Categorias

### List Categories

```
GET /api/categories

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "name": "Food",
      "icon": "utensils",
      "color": "#FF6B6B"
    },
    {
      "id": 2,
      "name": "Transportation",
      "icon": "car",
      "color": "#4ECDC4"
    },
    ...
  ]
}
```

---

## 🔄 Fluxos Principais

### Fluxo: Criar Transação com IA

```
1. POST /api/transactions
   ├─ Body: { description, amount, type, date }
   └─ Response: 201 Created

2. Backend (automaticamente):
   ├─ Valida com Requests
   ├─ Chama TransactionService::create()
   ├─ Dispara AnalyzeTransactionJob (async)
   └─ Retorna transaction

3. Job assíncrono:
   ├─ Chama AIAnalysisService::analyze()
   ├─ Chama Claude API
   ├─ Salva em AIAnalysis table
   ├─ Dispara evento AnomalyDetected (se aplicável)
   └─ Invalida cache

4. Frontend:
   ├─ Recebe response
   ├─ Atualiza store
   ├─ Re-render automático
   ├─ Mostra categoria IA
   └─ Mostra confidence badge
```

### Fluxo: Importar CSV

```
1. POST /api/transactions/import
   ├─ Body: { file: CSV }
   └─ Response: 201 Created

2. Backend:
   ├─ Parse CSV
   ├─ Validate cada linha
   ├─ Para cada transação válida:
   │  ├─ Create transaction
   │  ├─ Enqueue analyze job
   │  └─ Track progress
   ├─ Se erro, salva em error_details
   └─ Retorna summary

3. Frontend:
   ├─ Mostra progress bar
   ├─ Mostra resultado (imported/errors)
   ├─ Se erro, mostra detalhes
   └─ Refresh transaction list
```

---

## 🐛 Error Handling

### Error Response Format

```
HTTP Status: 4xx | 5xx

Body:
{
  "error": "Validation error",
  "message": "The given data was invalid.",
  "errors": {
    "amount": ["The amount must be greater than 0"],
    "description": ["The description field is required"]
  }
}
```

### Common Status Codes

```
200 OK                  - Sucesso GET
201 Created             - Sucesso POST (criar)
204 No Content          - Sucesso DELETE
400 Bad Request         - Input inválido
401 Unauthorized        - Token inválido/expirado
403 Forbidden           - Sem permissão
404 Not Found           - Recurso não existe
422 Unprocessable       - Validação falhou
429 Too Many Requests   - Rate limit atingido
500 Server Error        - Erro interno
503 Service Unavailable - Maintenance/down
```

---

## 🚀 Pagination

Todos endpoints que retornam listas usam pagination:

```
Query:
GET /api/transactions?page=2&per_page=50

Response:
{
  "data": [...],
  "meta": {
    "current_page": 2,
    "per_page": 50,
    "total": 1000,
    "last_page": 20,
    "from": 51,
    "to": 100
  },
  "links": {
    "first": "http://localhost:8000/api/transactions?page=1",
    "last": "http://localhost:8000/api/transactions?page=20",
    "prev": "http://localhost:8000/api/transactions?page=1",
    "next": "http://localhost:8000/api/transactions?page=3"
  }
}
```

---

## 📍 Rate Limiting Headers

Cada response inclui headers de rate limit:

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1705352400

Significado:
├─ Limit: 100 requests/minuto
├─ Remaining: 95 requests restantes
└─ Reset: Unix timestamp do reset
```

---

## 🔗 Links Relacionados

- [CODING_STANDARDS.md](CODING_STANDARDS.md) - Como estruturar código
- [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Onde cada coisa fica
- [DESIGN_PATTERNS.md](DESIGN_PATTERNS.md) - Padrões usados

---

<div align="center">

**Voltar ao índice:** [ARCHITECTURE/](.) ↗️

</div>
