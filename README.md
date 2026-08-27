# Sistema de Ordem de Serviços — JM Informática

Sistema web para gerenciamento de ordens de serviço da JM Informática.

Desenvolvido em **PHP 8.4+ (OOP)**, seguindo o padrão **MVC**, com **MySQL 8, PDO e JavaScript vanilla**, sem utilização de frameworks ou Composer.

## Funcionalidades

* Login e controle de sessão
* Cadastro de usuários
* Dashboard com resumo dos serviços
* Cadastro, edição, exclusão e finalização de serviços
* Filtros por período, descrição, status e usuário
* Paginação
* Cálculo automático de comissão
* Máscara de valores em reais
* Notificação por e-mail ao finalizar um serviço
* Layout responsivo
* Controle de acesso por usuário

## Regras de comissão

| Valor do serviço      | Comissão |
| --------------------- | -------: |
| Até R$ 1.000,00       |       5% |
| Acima de R$ 1.000,00  |      10% |
| Acima de R$ 10.000,00 |      20% |

## Tecnologias

* PHP 8.4+
* MySQL 8.0
* HTML5 / CSS3
* JavaScript
* PDO
* Apache

## Segurança

* Senhas armazenadas com bcrypt
* Proteção contra CSRF
* Prepared Statements com PDO
* Escape de saída com `htmlspecialchars()`
* Regeneração da sessão após o login
* Cookies `HttpOnly` e `SameSite`
* Controle de acesso aos serviços pelo usuário responsável

## Como executar

### Pré-requisitos

* PHP 8.4+
* MySQL 8.0

Copie `.env.example` para `.env` e configure as informações de conexão com o banco de dados.

Execute o script `database.sql` no MySQL para criar as tabelas e os dados iniciais.

### Iniciar o servidor

Na raiz do projeto, execute:

```bash
php -S localhost:8000 -t src src/router.php
```

Acesse:

```text
http://localhost:8000
```

## Credenciais de teste

| Usuário      | E-mail                                    | Senha  |
| ------------ | ----------------------------------------- | ------ |
| Administrador| admin@jminformatica.com.br                | 123456 |

## Observações

Projeto desenvolvido como parte do processo de avaliação técnica da **Titan**.

A proposta foi desenvolver a aplicação sem frameworks ou bibliotecas externas, mantendo a separação de responsabilidades entre as camadas.

**Obrigada pela oportunidade de participar do processo!**
