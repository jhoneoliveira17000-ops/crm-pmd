# PMDCRM 🚀

Sistema de CRM profissional para agências de marketing, com foco em gestão de MRR (Receita Recorrente), Contratos e Métricas Financeiras (CAC, LTV, Churn).

## 📋 Funcionalidades

- **Gestão de Clientes**: Cadastro completo com link para Drive.
- **Gestão de Contratos**: Controle de vigência e valores.
- **Dashboard Financeiro**: 
    - MRR (Monthly Recurring Revenue)
    - CAC (Custo de Aquisição de Clientes)
    - LTV (Lifetime Value)
    - Churn Rate
- **PWA (Progressive Web App)**: Instalável em celulares Android/iOS.
- **Segurança**: Login, Senha Criptografada (bcrypt), Controle de Sessão.

## 🛠 Tecnologias

- **Backend**: PHP 7.4+ (Sem frameworks, PHP Puro)
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, JavaScript (Fetch API)
- **Estilização**: TailwindCSS (via CDN)
- **Gráficos**: Chart.js (via CDN)

## 🚀 Instalação e Deploy

### 1. Requisitos
- Servidor PHP (Apache/Nginx)
- Banco de Dados MySQL

### 2. Configuração do Banco de Dados
1. Crie um banco de dados (ex: `pmdcrm`).
2. Importe o arquivo `database.sql` incluído na raiz do projeto.
3. O script criará o usuário admin padrão:
    - **Email**: `admin@pmdcrm.com`
    - **Senha**: `admin123`

### 3. Configuração do Projeto
1. Edite o arquivo `src/db.php` com as credenciais do seu banco:
```php
$host = 'localhost';
$db   = 'pmdcrm';
$user = 'seu_usuario';
$pass = 'sua_senha';
```

### 4. Permissões (Linux/Mac)
Certifique-se de que o servidor web tenha permissão de leitura nos arquivos:
```bash
chmod -R 755 .
```

### 5. Executando Localmente (PHP Built-in Server)
Para testar rapidamente sem Apache/Nginx:
```bash
cd PMDCRM
php -S localhost:8000
```
Acesse `http://localhost:8000` no seu navegador.

## 📱 PWA (Instalação no Celular)
1. Acesse o sistema pelo navegador do celular.
2. No Android (Chrome): Toque em "Adicionar à Tela Inicial".
3. No iOS (Safari): Toque em Compartilhar > "Adicionar à Tela de Início".
4. O app funcionará como um aplicativo nativo.

## 📂 Estrutura de Pastas
- `/api`: Endpoints REST JSON.
- `/src`: Lógica de conexão e autenticação.
- `/assets`: Ícones e imagens.
- `*.php`: Páginas do frontend.
