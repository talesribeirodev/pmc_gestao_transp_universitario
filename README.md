# 📦 Gestão - Transporte Universitário

---

## 🚀 Funcionalidades

- 📋 Listagem de inscrições
- ✅ Aprovação de inscrições
- ❌ Cancelamento com justificativa
- 📂 Upload e exibição de documentos (residência, matrícula, foto, etc.)
- 🔐 Controle de acesso por sessão

---

## 📁 Estrutura do Projeto

```
app/
├── Controllers/ 
├── Services/ 
├── Models/
├── Views/
├── Core/
.env
```

---

## ⚙️ Configuração do Ambiente

1. Copie o arquivo `.env.example` e configure o `.env`:
   ```bash
   cp .env.example .env
   ```

   Exemplo de variáveis no `.env`:
   ```
   API_BASE_URL=api.url
   MAIN_PROJECT_URL=meuprojeto.local (nome do projeto do formulário, para consultar as fotos)
   CPFS_BLOQUEADOS=12345678901,98765432100,11122233344
   ```

2. Configure o servidor web (Apache/Nginx) para apontar para o projeto.

---

## ▶️ Executando o Projeto

- Execute em um servidor local:
  ```bash
  php -S localhost:8000 -t app
  ```
- Ou configure no Apache/Nginx com suporte a PHP.

---

## 🛠️ Tecnologias Utilizadas

- PHP 8+
- PDO (PHP Data Objects)
- HTML/CSS
- JavaScript
- MySQL

---

## 📦 Dependências

- PHP >= 8.0
- MySQL

---
