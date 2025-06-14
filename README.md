# 🗳️ Sistema de Enquetes em PHP

Este é um sistema de enquetes desenvolvido em PHP com banco de dados MySQL, que permite criar, visualizar, votar e gerenciar enquetes de forma simples e segura.

---

## 🚀 Funcionalidades

- ✅ Cadastro de enquetes com data/hora de início e fim
- ✅ Definição de até 5 opções, sendo as 3 primeiras obrigatórias e as 2 últimas opcionais
- ✅ Sistema de votação com restrição por IP (um voto por IP por enquete)
- ✅ Contagem em tempo real dos votos (via AJAX)
- ✅ Proteção contra:
  - CSRF (Cross-Site Request Forgery)
  - SQL Injection (Prepared Statements)
  - XSS (Cross-Site Scripting) nas saídas
- ✅ Edição e exclusão de enquetes
- ✅ Validação de período ativo da enquete (não permite votar fora da data/hora)
- ✅ Interface simples, responsiva e amigável
- ✅ Logs de votos registrados no banco
- ✅ Formulário responsivo e estilizado

---

## 🏢 Estrutura do Projeto

```
/
├── assets/
│   └── css/
│       └── style.css
├── controllers/
│   ├── create_poll.php
│   ├── edit_poll.php
│   ├── delete_poll.php
│   └── vote.php
├── includes/
│   └── db.php
├── views/
│   ├── index.php
│   └── poll.php
└── README.md
```

---

## 💄 Banco de Dados

### 🎯 Estrutura das tabelas:

```sql
CREATE TABLE polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL
);

CREATE TABLE options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    votes INT DEFAULT 0,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
);

CREATE TABLE votes_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    vote_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
);
```
🔧 Instalação e Execução

1. Clone o projeto:

```bash
git clone https://github.com/seu-usuario/seu-repositorio.git
```

2. Crie um banco de dados MySQL e execute o script SQL acima.

3. Configure a conexão no arquivo:

```php
/includes/db.php
```

Exemplo de configuração:

```php
$pdo = new PDO("mysql:host=localhost;dbname=seu_banco", "seu_usuario", "sua_senha");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

4. Coloque o projeto em seu servidor local (ex.: XAMPP, WAMP ou Apache Nativo).

5. Acesse via navegador:

```
http://localhost/seu-projeto/views/index.php

```

## 🔒 Segurança

- ✅ CSRF Token implementado em todos os formulários
- ✅ Prepared Statements em todas as queries (protege contra SQL Injection)
- ✅ Escape de saídas com `htmlspecialchars()` (protege contra XSS)
- ✅ Restrição de múltiplos votos por IP
- ✅ Validação de datas e entradas tanto no front quanto no back-end

---

## 🛠️ Tecnologias Utilizadas

- 🐘 PHP (puro)
- 📔 MySQL
- 🎨 HTML, CSS(flexBox)
- ⚙️ JavaScript (AJAX)
- 🔥 Servidor Apache

---

## ✍️ Autor

- **Vinicius André Froggel de Miranda**
- [LinkedIn](https://www.linkedin.com/in/viniciusandr%C3%A9/)
- 

