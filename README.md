# 🐄 System Milk — Gestão de Leiteria

> Plataforma web para controle de produção de leite, cadastros de animais e gestão financeira (lucros e despesas).  
> Desenvolvido em **Vue 3 + Vite** (frontend), **PHP 8** (backend) e **PostgreSQL** (banco de dados).

---

## 🧭 Visão Geral

O **System Milk** centraliza as rotinas de uma leiteria em módulos simples e práticos:

- **Produção de Leite**: registro diário por data/turno, litros, qualidade, equipamento e armazenamento.
- **Animais**: base de vacas/identificação para vincular contribuintes da produção.
- **Financeiro**:
  - **Lucros**: receitas por categoria (venda de leite, animais, serviços etc.), com método/status de pagamento, NF-e e vencimentos.
  - **Despesas**: classificação por categoria/subcategoria, prioridade e fluxo de pagamento.
- **Filtros e busca**: por período, categoria e texto livre.
- **UX aprimorada**: componentes modernos, toasts de feedback e modais de criação/edição.

---

## ⚙️ Stack Principal

| Camada | Tecnologia |
|--------|-------------|
| **Frontend** | Vue 3 (Composition API), Vite, TailwindCSS, Axios |
| **Backend** | PHP 8 (roteamento leve com JSON APIs REST) |
| **Banco de Dados** | PostgreSQL |

---

## ✨ Destaques do Projeto

- Interface moderna e responsiva.
- Estrutura organizada com Composition API.
- Feedback visual (toasts, modais e mensagens de erro).
- Cálculo automático de valores e validação de campos.
- Filtros por período, categoria e busca textual.
- API RESTful padronizada (`/leite`, `/lucros`, etc.).

---

## 🔗 Links Rápidos

- **Frontend:** `frontend/src/views/Lucro.vue`
- **HTTP Client:** `frontend/src/lib/http.js`
- **Rotas de API:** `backend/routes/`
- **Schema do Banco:** `database/schema.sql`

---

## 📌 Status Atual

✅ Módulos de **Leite** e **Lucros** totalmente funcionais.  
🧩 Ajustes em andamento para padronização de rotas e respostas da API (`POST /lucros`).  
🚀 Integração futura com módulo de **Despesas** e **Dashboard** de relatórios.

---

# 🛠️ Parte 2 — Setup Local (Frontend + Backend + Banco)

> Pré-requisitos: **Node 18+**, **npm** ou **pnpm**, **PHP 8.1+**, **PostgreSQL 13+**.

### Clonar o projeto
```bash
git clone https://github.com/SEU_USUARIO/system-milk.git
cd system-milk
```

### Backend (PHP)

## Estrutura sugerida:
```
backend/
  public/index.php        # ponto de entrada
  routes/                 # define endpoints (ex.: /lucros, /leite)
  src/                    # controllers/services/dao
```

### Variáveis de ambiente (crie backend/.env):
```
DB_HOST=localhost
DB_PORT=5432
DB_NAME=system_milk
DB_USER=system_milk_user
DB_PASS=system_milk_pass

# CORS (ajuste conforme porta do front)
CORS_ORIGIN=http://localhost:5173
```

### Subir servidor PHP (dev)
```
cd backend
php -S localhost:8001 -t public
```
























