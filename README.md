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

