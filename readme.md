# EcoFlow 💸 
> **Seu controle financeiro inteligente e automatizado.**

![Status](https://img.shields.io/badge/STATUS-EM%20DESENVOLVIMENTO-yellow?style=for-the-badge)

## 📖 Sobre o Projeto

**EcoFlow** é uma plataforma web de gestão financeira pessoal projetada para simplificar a forma como você lida com seu dinheiro. Diferente de planilhas complexas, o EcoFlow oferece uma interface intuitiva com automação de lançamentos e inteligência de dados.

O grande diferencial do projeto é o uso de **Inteligência Artificial** para analisar os gastos do usuário e fornecer recomendações personalizadas de economia e investimentos.

---

## ✨ Funcionalidades Principais

* **📊 Dashboard Interativa:** Visão geral das finanças com gráficos dinâmicos de receitas vs. despesas.
* **🤖 EcoFlow AI:** Receba dicas e recomendações financeiras baseadas nos seus hábitos de consumo geradas por Inteligência Artificial.
* **🔄 Sistema de Recorrência:** Lançamento automático de contas fixas (aluguel, salário, assinaturas) na virada do mês, sem precisar redigitar.
* **📅 Relatórios Anuais:** Acompanhamento de evolução patrimonial e fechamento de ano.
* **🔐 Segurança:** Sistema de login robusto com proteção contra CSRF e validação de dados.
* **📱 Responsivo:** Acesso fácil via computador ou dispositivos móveis.

---

## 🛠️ Tecnologias Utilizadas

* **Back-end:** PHP (Vanilla)
* **Banco de Dados:** MySQL / MariaDB
* **Front-end:** HTML5, Tailwind, JavaScript

---

## 🚀 Como Rodar o Projeto

### Pré-requisitos
* Ter o [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou ambiente similar instalado.
* Git instalado.

### Passo a Passo

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/SEU-USUARIO/ecoflow.git](https://github.com/SEU-USUARIO/ecoflow.git)
    ```

2.  **Configure o Banco de Dados:**
    * Acesse o PHPMyAdmin (ou seu gerenciador preferido).
    * Crie um banco de dados chamado `ecoflow`.
    * Importe o arquivo `ecoflow.sql` (localizado na pasta `/database`).

3.  **Configuração:**
    * Verifique o arquivo `backend/conexao.php` e ajuste as credenciais do banco se necessário.

4.  **Executar:**
    * Inicie o Apache e MySQL no XAMPP.
    * Acesse no navegador: `http://localhost/ecoflow`

---

## 🚧 Status do Projeto

O projeto encontra-se em **fase ativa de desenvolvimento**. Novas funcionalidades estão sendo implementadas semanalmente.
* [x] Sistema de Login/Cadastro
* [x] Lançamento de Despesas/Rendas
* [x] Lógica de Recorrência Mensal
* [ ] Integração completa da API de IA
* [ ] Modo Escuro (Dark Mode)
* [ ] Sistema de Metas

---

## 🤝 Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir uma *issue* para relatar bugs ou sugerir novas features.

---

## 📝 Autor

Desenvolvido por **Walysson Ribeiro**.

---
<p align="center">
  Feito com 💙 e PHP.
</p>