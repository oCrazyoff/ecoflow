# EcoFlow - Seu Gerenciador Financeiro Pessoal

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
</p>

<p align="center">
  Um sistema simples e intuitivo para gerenciar suas finanças pessoais, ajudando você a ter uma visão clara de suas receitas e despesas.
</p>

## 📋 Tabela de Conteúdos

- [Sobre o Projeto](#-sobre-o-projeto)
- [✨ Funcionalidades](#-funcionalidades)
- [🛠️ Tecnologias Utilizadas](#️-tecnologias-utilizadas)
- [🚀 Começando](#-começando)
  - [Pré-requisitos](#pré-requisitos)
  - [Instalação](#instalação)
- [🤝 Como Contribuir](#-como-contribuir)

## 📝 Sobre o Projeto

O **EcoFlow** nasceu da necessidade de ter uma ferramenta de controle financeiro que seja ao mesmo tempo poderosa e fácil de usar. Com ele, você pode cadastrar todas as suas entradas e saídas, categorizá-las e visualizar relatórios que te ajudarão a tomar decisões financeiras mais inteligentes.

## ✨ Funcionalidades

-   ✅ Cadastro de Receitas (salário, vendas, etc.)
-   ✅ Cadastro de Despesas (aluguel, alimentação, lazer, etc.)
-   ✅ Categorização de lançamentos.
-   ✅ Dashboard com resumo mensal e anual.
-   ✅ Interface limpa e responsiva.

## 🛠️ Tecnologias Utilizadas

Este projeto foi construído com as seguintes tecnologias:

-   **Backend:** PHP
-   **Banco de Dados:** MySQL
-   **Frontend:** HTML, Tailwind CSS, JavaScript
-   **Gerenciador de Pacotes:** NPM

## 🚀 Começando

Para ter uma cópia local do projeto rodando, siga estes passos.

### Pré-requisitos

Você vai precisar ter as seguintes ferramentas instaladas em sua máquina:
-   Um servidor web local (XAMPP, WAMP, Laragon, etc.) que inclua:
    -   PHP (versão 8.0 ou superior recomendada)
    -   MySQL
-   [Node.js e npm](https://nodejs.org/en/) (para compilar o Tailwind CSS)
-   [Git](https://git-scm.com/)

### Instalação

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/seu-usuario/ecoflow.git](https://github.com/seu-usuario/ecoflow.git)
    ```

2.  **Acesse a pasta do projeto:**
    ```bash
    cd ecoflow
    ```

3.  **Instale as dependências do NPM:**
    ```bash
    npm install
    ```

4.  **Configure o Banco de Dados:**
    -   Crie um novo banco de dados no seu MySQL (ex: `ecoflow`).
    -   Importe o arquivo `.sql` que está na pasta `database/`.
    -   Configure a conexão com o banco de dados no arquivo de conexão (backend/conexao.php).

5.  **Compile o Tailwind CSS:**
    -   Para compilar os assets e ficar observando por mudanças durante o desenvolvimento, rode:
    ```bash
    npm run dev
    ```
    -   Para compilar a versão final para produção (minificada), você pode criar um script `build` no seu `package.json`.

6.  **Inicie o servidor:**
    -   Inicie seu servidor Apache/MySQL e acesse o projeto pelo seu navegador (ex: `http://localhost/ecoflow`).

Pronto! O sistema deve estar funcionando.

## 🤝 Como Contribuir

Contribuições são o que tornam a comunidade de código aberto um lugar incrível para aprender, inspirar e criar. Qualquer contribuição que você fizer será **muito apreciada**.

1.  Faça um **Fork** do projeto.
2.  Crie uma **Branch** para sua feature (`git checkout -b feature/AmazingFeature`).
3.  Faça o **Commit** de suas mudanças (`git commit -m 'Add some AmazingFeature'`).
4.  Faça o **Push** para a Branch (`git push origin feature/AmazingFeature`).
5.  Abra um **Pull Request**.

---
Feito com por [Walysson](https://www.walysson.com.br)
