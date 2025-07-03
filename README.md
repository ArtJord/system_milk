name: Leiteria Milk Bom
description: >
  Projeto de TCC para gestão de uma leiteria, com controle de vacas, produção de leite
  e demais operações do cotidiano rural. Utiliza PHP com padrão DAO e, futuramente,
  contará com um front-end moderno usando Vue.js.

version: 1.0.0

author:
  name: Seu Nome Aqui
  email: seuemail@email.com
  linkedin: https://www.linkedin.com/in/seu-usuario
  github: https://github.com/seu-usuario

status: "Em desenvolvimento"
license: MIT

features:
  - Cadastro e gerenciamento de vacas
  - Registro da produção de leite
  - Relatórios de produtividade
  - API RESTful com PHP
  - Integração futura com Vue.js no front-end
  - Arquitetura organizada usando DAO (Data Access Object)

tech_stack:
  backend:
    language: PHP
    architecture: DAO (Data Access Object)
    database: MySQL
    server: Apache ou servidor embutido PHP
  frontend:
    current: HTML, CSS, JavaScript simples
    future: Vue.js
  tools:
    - Postman (para testar API)
    - Git & GitHub (controle de versão)
    - XAMPP ou similar (ambiente local)

project_structure:
  - backend/
      - controllers/
      - models/
      - dao/
      - config/
      - index.php
      - .htaccess
  - frontend/
      - public/
        - index.html
        - css/
        - js/
      - vue-src/ # futuro código Vue
  - database/
      - leitera.sql
  - README.md

requirements:
  php: ">=7.4"
  mysql: ">=5.7"
  node: opcional (para futura integração com Vue.js)
  composer: opcional (caso use bibliotecas externas)

installation:
  steps:
    - Clonar o repositório:
      command: git clone https://github.com/seu-usuario/leitera-tcc.git
    - Importar o banco de dados:
      file: database/leitera.sql
    - Configurar conexão com o banco:
      file: backend/config/Database.php
    - Iniciar servidor local (modo simples):
      command: php -S localhost:8000 -t backend
    - Acessar via navegador:
      url: http://localhost:8000

api_example:
  endpoint: /vacas/create
  method: POST
  body:
    numero: 123
    status: "ativa"
  response:
    success: true
    message: "Vaca cadastrada com sucesso"

future_features:
  - Painel administrativo com Vue.js
  - Login com autenticação
  - Dashboard com gráficos de produção
  - Controle de alimentação e saúde dos animais

academic_info:
  institution: Nome da sua faculdade
  course: Nome do seu curso
  project_type: Trabalho de Conclusão de Curso (TCC)
  advisor: Nome do orientador

quote: "🐄 Onde tem leite, tem sistema!"

