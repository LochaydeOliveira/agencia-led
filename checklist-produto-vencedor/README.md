# 🎯 Checklist do Produto Lucrativo

Uma aplicação web completa para análise e pontuação de produtos, com sistema de login e dashboard interativo.

## ✨ Funcionalidades

- 🔐 **Sistema de Login**: Acesso restrito com email e senha
- 📋 **Formulário Interativo**: Perguntas de qualificação + checklist de pontuação
- 🧮 **Cálculo Automático**: Nota final de 0 a 10 baseada nos itens marcados
- 📊 **Resultado Interpretativo**: Mensagem baseada na pontuação obtida
- 💾 **Armazenamento**: Banco de dados MySQL para usuários e resultados
- 📧 **Email Automático**: Envio de credenciais de acesso (opcional)
- 🖨️ **Impressão**: Funcionalidade de imprimir resultados
- 📱 **Responsivo**: Design moderno e adaptável a dispositivos móveis

## 🚀 Instalação

### Requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensão PDO MySQL habilitada
- Servidor web (Apache, Nginx, etc.)

### Passos de Instalação

1. **Clone ou baixe o projeto**
   ```bash
   git clone [url-do-repositorio]
   cd checklist-produto-vencedor
   ```

2. **Configure o banco de dados**
   - Crie um banco MySQL chamado `paymen58_checklist_produto_lucrativo`
   - As tabelas serão criadas automaticamente na primeira execução

3. **Configure o servidor web**
   - Coloque os arquivos na pasta do seu servidor web
   - As credenciais do banco já estão configuradas no `includes/db.php`

4. **Acesse a aplicação**
   - Abra `http://seudominio.com/app/` no navegador
   - O banco de dados será configurado automaticamente na primeira execução

5. **Credenciais padrão**
   - **Email**: `admin@exemplo.com`
   - **Senha**: `123456`

## 📁 Estrutura do Projeto

```
checklist-produto-vencedor/
├── index.php              # Página de login
├── dashboard.php          # Formulário principal
├── resultado.php          # Página de resultado
├── logout.php            # Logout
├── config.php            # Configurações centralizadas
├── includes/
│   ├── db.php            # Conexão com banco de dados MySQL
│   ├── auth.php          # Autenticação e sessões
│   └── mailer.php        # Configuração de email (exemplo)
├── admin/
│   └── add_user.php      # Script para adicionar usuários
└── README.md
```

## 🔧 Configuração

### Banco de Dados
O sistema está configurado para usar MySQL com as seguintes credenciais:
- **Host**: localhost
- **Database**: paymen58_checklist_produto_lucrativo
- **User**: paymen58
- **Password**: u4q7+B6ly)obP_gxN9sNe

As tabelas são criadas automaticamente na primeira execução.

### Email (Opcional)
Para enviar emails automáticos de acesso:

1. Edite `admin/add_user.php`
2. Configure as variáveis SMTP:
   ```php
   $smtp_username = 'seu-email@gmail.com';
   $smtp_password = 'sua-senha-app';
   $from_email = 'seu-email@gmail.com';
   ```

3. Para Gmail, use uma "Senha de App" em vez da senha normal

## 📋 Como Usar

### Para Administradores

1. **Adicionar Usuários**
   - Acesse `admin/add_user.php`
   - Preencha nome, email e senha
   - Marque a opção para enviar email (se configurado)

2. **Gerenciar Acesso**
   - Os usuários receberão credenciais por email
   - Podem fazer login em `index.php`

### Para Usuários

1. **Login**
   - Acesse a aplicação com email e senha
   - Será redirecionado para o dashboard

2. **Preencher Checklist**
   - **Bloco 1**: Responda as 4 perguntas de qualificação
   - **Bloco 2**: Marque os itens que se aplicam ao seu produto (1 ponto cada)

3. **Ver Resultado**
   - Clique em "Calcular Resultado"
   - Veja a nota final e mensagem interpretativa
   - Opção de imprimir o resultado

## 🎯 Sistema de Pontuação

### Checklist (10 pontos possíveis)
- ✅ Deixa a vida do cliente mais fácil
- ✅ Criativos são dinâmicos e de qualidade
- ✅ Possui buscas no Google
- ✅ Já está sendo vendido em lojas
- ✅ Economiza dinheiro
- ✅ Economiza tempo
- ✅ Não é nicho sensível
- ✅ Custa menos de 50 dólares
- ✅ Só encontra na internet
- ✅ Produto não é commodity

### Interpretação dos Resultados
- **8-10 pontos**: "Produto com alto potencial!" 🏆
- **5-7 pontos**: "Produto razoável, com potencial" ⭐
- **0-4 pontos**: "Produto fraco, repense a escolha" ⚠️

## 🔒 Segurança

- Senhas criptografadas com `password_hash()`
- Proteção contra SQL Injection
- Validação de entrada de dados
- Sessões seguras
- Escape de saída HTML
- Headers de segurança no .htaccess

## 🎨 Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5 + TailwindCSS
- **Ícones**: Font Awesome
- **Autenticação**: Sessões PHP

## 📧 Suporte

Para dúvidas ou problemas:
1. Verifique se todas as extensões PHP estão habilitadas (PDO MySQL)
2. Confirme a conexão com o banco de dados MySQL
3. Teste as configurações de email (se usar)
4. Verifique os logs de erro do PHP

## 🚀 Deploy

### Hosting Compartilhado (Hostgator, InfinityFree, etc.)
1. Faça upload dos arquivos via FTP
2. Configure o banco MySQL conforme as credenciais
3. Acesse a URL do seu domínio

### VPS/Dedicado
1. Configure Apache/Nginx
2. Instale PHP e extensões necessárias (PDO MySQL)
3. Configure MySQL e crie o banco de dados
4. Configure SSL para segurança
5. Configure backup do banco de dados

## 📝 Licença

Este projeto é de uso livre para fins comerciais e educacionais.

---

**Desenvolvido com ❤️ para ajudar empreendedores a escolherem produtos lucrativos!** 