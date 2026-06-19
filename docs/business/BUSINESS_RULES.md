# 📋 Regras de Negócio (Business Rules)

> **Parte:** BUSINESS  
> **Propósito:** Documentar lógica e regras de negócio  
> **Última atualização:** Junho 2026

---

## 🎯 Contexto de Negócio

**Domínio:** Análise financeira pessoal com IA  
**Usuários:** Pessoas físicas gerenciando suas finanças  
**Problema:** Pessoas não sabem onde gastam dinheiro  
**Solução:** Importar transações + IA analisa + recomendações  

---

## 💰 Regras de Transações

### 1. **Criação de Transação**

```
Uma transação DEVE ter:
├─ user_id (quem é dono)
├─ description (o quê foi)
├─ amount (quanto custou) - > 0
├─ type (debit, credit, pix)
├─ date (quando foi)
└─ category_id (opcional, será preenchida por IA)

Uma transação NÃO PODE:
├─ Ser criada sem user_id
├─ Ter amount negativo
├─ Ter data no futuro
├─ Ter description vazia
└─ Ser deletada (apenas marcada como "canceled")
```

### 2. **Tipos de Transação**

```
DEBIT (débito em conta)
├─ Saída de dinheiro
├─ Reduz saldo
└─ Exemplo: compra supermercado

CREDIT (crédito/entrada)
├─ Entrada de dinheiro
├─ Aumenta saldo
└─ Exemplo: salário, freelance

PIX (transferência instantânea)
├─ Pode ser entrada ou saída
├─ Integração futura com APIs
└─ Exemplo: pagar alguém
```

### 3. **Validações de Negócio**

```
Regra: Valor máximo de transação = R$ 100.000
├─ Evita erros de digitação
└─ Pode ser desabilitado com admin flag

Regra: Não mais de 1000 transações por dia
├─ Evita spam
├─ Segurança
└─ Performance

Regra: Descrição máx 255 caracteres
├─ Armazenamento eficiente
└─ Padronização
```

---

## 🤖 Regras de IA (Claude Integration)

### 1. **Categorização Automática**

```
Processo:
1. User importa transação
2. Backend chama Claude API:
   "Categorize this: {description}"
3. Claude retorna:
   {
     "category": "Food",
     "confidence": 0.95,
     "reasoning": "Mentions restaurant name"
   }
4. Sistema salva categoria
5. Frontend mostra com confidence visual

Categorias Válidas:
├─ Food (Alimentação)
├─ Transportation (Transporte)
├─ Entertainment (Entretenimento)
├─ Utilities (Utilidades)
├─ Healthcare (Saúde)
├─ Shopping (Compras)
├─ Subscription (Assinaturas)
├─ Transfer (Transferências)
├─ Salary (Salário)
├─ Investment (Investimento)
└─ Other (Outro)

Regra: Se confidence < 0.7
├─ Marca como "needs_review"
├─ Mostra para user confirmar
└─ Usa resposta para treinar IA
```

### 2. **Detecção de Anomalias**

```
Claude analisa:
├─ Valor muito diferente do normal
├─ Padrão de gasto mudou
├─ Novo vendedor
└─ Quantidade inusitada

Exemplo anomalia:
├─ Normal: R$ 50/semana em café
├─ Anomalia: R$ 2.000 em café (20x normal!)

Ação ao detectar:
├─ Dispara evento "AnomalyDetected"
├─ Notifica user (email/push)
├─ Mostra alert na UI
└─ Guarda para análise posterior
```

### 3. **Recomendações de Orçamento**

```
Claude gera:
├─ "Você gasta 40% com alimentação"
├─ "Pode economizar cortando streaming"
├─ "Padrão: ~R$ 5.000/mês"
├─ "Se continuar assim, limite em 18 dias"
└─ "3 assinaturas podem ser cortadas"

Frequência:
├─ Diária: anomalias
├─ Semanal: summary
└─ Mensal: análise detalhada
```

---

## 📊 Regras de Analytics

### 1. **Cálculos Obrigatórios**

```
Total gastos do mês:
├─ SUM(amount) WHERE type='debit' AND date IN [month]
└─ Não inclui créditos/entradas

Total receitas do mês:
├─ SUM(amount) WHERE type IN ['credit','salary']
└─ Inclui tudo que entra

Saldo atualizado:
├─ Calcula em tempo real
├─ Valor mínimo é 0 (sem negativo)
└─ Mostra com 2 decimais

Gastos por categoria:
├─ GROUP BY category
├─ SUM(amount) por categoria
└─ Percentual do total
```

### 2. **Período Padrão**

```
Mês = 1º ao último dia do mês
Semana = Seg-Dom
Ano = Jan-Dec

Período customizado:
├─ User pode selecionar qualquer range
├─ Salvar como "favorite" (opcional)
└─ Máximo: últimos 5 anos
```

---

## 🔐 Regras de Segurança & Privacidade

### 1. **Acesso a Dados**

```
User A não PODE ver dados de User B
├─ Validação em TODOS endpoints
├─ Verificar user_id em requests
└─ Bloquear unauthorized

User admin:
├─ Pode ver relatórios anônimos
├─ Não pode ver dados pessoais
└─ Audit log de todas ações
```

### 2. **Claude API**

```
Dados enviados para Claude:
├─ Description (anonimizado)
├─ Amount
├─ Date
└─ NÃO enviamos: user_id, email, passwords

Dados NÃO são usados para treinar Claude:
├─ Opt-out de training via API
└─ Confirmado em contrato Anthropic
```

### 3. **Rate Limiting**

```
API:
├─ 100 requisições/minuto por user
├─ 1000 requisições/hora por user
└─ 404 = block por 1 minuto

Claude API:
├─ Max 10 análises simultâneas
├─ Fila para resto
└─ Timeout: 30 segundos
```

---

## 💾 Regras de Dados (Database)

### 1. **Soft Deletes**

```
Transações não são deletadas:
├─ Apenas marcadas com deleted_at
├─ Motivo: auditoria, recuperação
└─ Usuário não vê deletadas (default)

Recuperar deletada:
├─ Admin pode fazer restore
├─ Gera audit log
└─ Recalcula analytics
```

### 2. **Auditoria**

```
TODOS os events são logados:
├─ Usuário criou transação
├─ Categoria foi mudada
├─ IA fez análise
├─ Admin fez ação
└─ Alguém deletou

Guardado:
├─ Who: user_id
├─ What: ação realizada
├─ When: timestamp
├─ Why: motivo (se aplicável)
└─ Where: IP address
```

### 3. **Backup & Recovery**

```
Backup automático:
├─ Diário (full)
├─ Hourly (incremental)
└─ Guardado 30 dias

Recovery:
├─ < 1 hora de RTO
├─ < 5 minutos de RPO
└─ Testado mensalmente
```

---

## 🚀 Regras de Performance

### 1. **Cache**

```
Cache AI Responses:
├─ TTL: 24 horas
├─ Key: transaction_id + "ai_analysis"
├─ Invalidar ao: editar transaction

Cache User Stats:
├─ TTL: 1 hora
├─ Key: user_id + "monthly_stats"
├─ Invalidar ao: criar/deletar transaction

Cache Categories:
├─ TTL: 7 dias
├─ Key: "categories"
└─ Invalidar ao: editar categoria
```

### 2. **Pagination**

```
Transações:
├─ Default: 20 por página
├─ Max: 100 por página
└─ Ordem: desc by date

Analytics:
├─ Default: últimos 30 dias
├─ Máximo: últimos 5 anos
└─ Aggregate por mês se > 1 ano
```

### 3. **Índices Database**

```
PostgreSQL:
├─ user_id (frequent filter)
├─ created_at (sorting)
├─ category_id (grouping)
├─ (user_id, created_at) composite
└─ (user_id, category_id) composite

Resultado:
├─ Queries < 100ms (P95)
└─ Sem slow queries
```

---

## 📱 Regras de UX

### 1. **Feedback Visual**

```
Ao criar transação:
├─ Loading spinner até confirmado
├─ Toast "Transação salva!"
├─ Aparecer na lista automaticamente
└─ Scroll para nova transação

Ao editar:
├─ Form pre-filled
├─ Dirty state indicator
├─ Confirm antes de salvar

Ao deletar:
├─ Modal de confirmação
├─ "Are you sure?"
└─ Undo por 10 segundos
```

### 2. **Validação de Form**

```
Real-time validation:
├─ Amount: apenas números
├─ Date: date picker
├─ Description: character counter

On submit:
├─ Validar tudo server-side
├─ Se erro, mostrar acima do form
└─ Não permitir double-submit
```

---

## 📬 Regras de Notificações

### 1. **Tipos de Notificação**

```
Anomalia detectada:
├─ Email imediato
├─ Push notification
├─ In-app alert
└─ Sumário semanal

Resumo mensal:
├─ Email automático dia 1
├─ Attachments: relatório PDF
└─ Link para dashboard

Limite de orçamento:
├─ Alert ao atingir 80%
├─ Novo alert ao 100%
└─ Desabilitar notificações = opção
```

### 2. **Opt-out**

```
User pode:
├─ Desabilitar todas as notificações
├─ Desabilitar por tipo
├─ Mute por período
└─ Salvar preferência
```

---

## 🎓 Regras de Aprendizado & Melhoria

### 1. **Feedback do User**

```
User pode categorizar transação:
├─ Se discorda de IA
├─ Salva feedback
└─ Feedback usado para refinar prompts da aplicação

Feedback guardado:
├─ Para análise posterior
├─ Melhorar prompts
└─ Treinar modelo custom (futuro)
```

---

## ✅ Checklist: É regra de negócio?

Faça essas perguntas:

```
❓ O business owner pediu isso?         → Sim? É regra
❓ Muda se user for diferente?          → Sim? É regra
❓ Relacionado a $, risco, compliance?  → Sim? É regra
❓ Deve ser igual para todos users?     → Sim? É regra

Se sim em qualquer, DOCUMENTAR AQUI
```

---

## 🔗 Links Relacionados

- [FEATURE_SPECIFICATIONS.md](FEATURE_SPECIFICATIONS.md) - Features implementando regras
- [DEVELOPMENT_ROADMAP.md](DEVELOPMENT_ROADMAP.md) - Timeline das regras
- [CONSTRAINTS.md](CONSTRAINTS.md) - Limitações das regras

---

<div align="center">

**Próximo passo:** [FEATURE_SPECIFICATIONS.md](FEATURE_SPECIFICATIONS.md) ↗️

</div>
