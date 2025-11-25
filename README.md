# 🐄 System Milk — Backend (API REST em PHP)

[![PHP](https://img.shields.io/badge/PHP-8.2-blue)]()
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-13+-blue)]()
[![Tests](https://img.shields.io/badge/Tests-PHPUnit-success)]()
[![Status](https://img.shields.io/badge/Status-Ativo-brightgreen)]()

API REST desenvolvida para o **System Milk**, uma plataforma de gestão de leiteria com controle de produção, animais e financeiro.  
Este repositório contém **apenas o backend** — o frontend Vue 3 está em outro repositório.

---

## 🚀 Visão Geral

O backend do **System Milk** foi construído com foco em:

- **Arquitetura simples e eficiente**  
- **Roteamento leve** escrito à mão (`Routes.php`)
- **Respostas JSON padronizadas**
- **Integração com PostgreSQL**
- **Testes automatizados com PHPUnit**
- **Endpoints RESTful por módulos (leite, lucros, animais, usuários)**

---

## 🧩 Funcionalidades principais

### 🐄 Animais
- Cadastro de vacas
- Listagem
- Atualização
- Exclusão
- Endpoint de integração para o frontend

### 🥛 Produção de Leite
- Registro de produção diária
- Turno, temperatura, tipo, qualidade, responsável e armazenamento
- Relatórios mensais (via frontend)

### 💰 Financeiro
- **Lucros**  
  - Categoria  
  - Fonte  
  - Pagamentos  
  - NF-e  
  - Datas de vencimento e recebimento  
- **Despesas (em desenvolvimento)**  
  - Categoria / Subcategoria  
  - Prioridade  
  - Fluxo de pagamento  

### 👤 Usuários
- Criação de usuários
- Login
- Verificação de cargo
- Autenticação por token (WIP)

---

## ⚙️ Tecnologias Utilizadas

| Camada        | Tecnologia |
|---------------|------------|
| **Linguagem** | PHP 8.2 |
| **Banco**     | PostgreSQL 13+ |
| **Testes**    | PHPUnit |
| **Servidor Dev** | PHP Built-in Server |
| **Arquitetura** | Controllers + DAO + Router minimalista |

---

# 🛠️ Instalação e Setup (Local)

## 1. Clonar o repositório

```bash
git clone https://github.com/SEU_USUARIO/system-milk-backend.git
cd system-milk-backend
```

## 2. Configurar Banco de Dados

Crie o banco:
```
CREATE DATABASE system_milk;
```
Edite o arquivo conexao.php:
```
$host = "localhost";
$db   = "system_milk";
$user = "postgres";
$pass = "SUA_SENHA";
```

## 3. Subir API (modo desenvolvimento)
```
php -S localhost:8001 index.php
```
A API ficará disponível em:
```
http://localhost:8001
```
# 🧪 Testes Automatizados (PHPUnit)

## O projeto contém testes de unidade, integração e rotas.

📌 Rodar todos os testes
```
php vendor/bin/phpunit
```
📌 Rodar testes individuais

Usuários
```
php vendor/bin/phpunit tests/UsuarioTest.php
```
Vacas
```
php vendor/bin/phpunit tests/VacaTest.php
```
Rotas
```
php vendor/bin/phpunit tests/RoutesTest.php
```
Endpoints reais (precisa do servidor ativo)
```
php vendor/bin/phpunit tests/AnimaisEndpointsTest.php
```

## ☑️ Status atual dos testes

🟢 UsuarioTest.php — OK
🟢 VacaTest.php — OK
🟢 RoutesTest.php — OK
🟢 AnimaisEndpointsTest.php — OK

## 🌐 Endpoints Principais
🐄 Vacas
Método	      Rota	        Descrição
GET	          /vacas	      Lista todas as vacas
POST	        /vacas	      Cria uma vaca
PUT	          /vacas/{id}	  Atualiza
DELETE	      /vacas/{id}	  Exclui

## 🥛 Leite
Método	      Rota	        Descrição
GET	          /leite	      Lista produção
POST	        /leite	      Registra produção

## 💰 Lucros
Método	       Rota	        Descrição
GET	           /lucros	    Lista lucros
POST	          /lucros	     Cria novo lucro

## 🤝 Contribuição

Pull requests são bem-vindos!
Antes de contribuir:

1. Crie uma branch
2. Rode todos os testes
3. Abra o PR com descrição clara
```
git checkout -b feature/minha-feature
git commit -m "feat: implementei X"
git push origin feature/minha-feature
```

## 📣 Observação importante

➡️ O frontend Vue 3 está em outro repositório e se integra a este backend via HTTP na porta 8001.
➡️ O README do frontend será criado separadamente.









