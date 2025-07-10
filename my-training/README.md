# My Training - Aplicação de Plano de Treino e Alimentação

Uma aplicação web completa para gerenciar planos de treino e alimentação, desenvolvida em PHP com MySQL e interface responsiva mobile-first.

## 🚀 Funcionalidades

### ✅ Implementadas
- **Sistema de Autenticação**: Login e cadastro de usuários
- **Dashboard Principal**: Resumo do dia com progresso visual
- **Gerenciamento de Treinos**: 
  - Adicionar/remover exercícios por dia da semana
  - Configurar séries, repetições, carga e descanso
  - Marcar treinos como realizados
- **Plano Alimentar**:
  - Cadastrar refeições por horário
  - Informações nutricionais (calorias, proteínas, carboidratos, gorduras)
  - Marcar refeições como consumidas
- **Acompanhamento de Progresso**:
  - Registro de peso e medidas corporais
  - Gráfico de evolução do peso
  - Estatísticas de consistência
- **Perfil do Usuário**:
  - Editar informações pessoais
  - Alterar senha
  - Configurações de notificações
- **PWA (Progressive Web App)**:
  - Instalável no celular
  - Funciona offline
  - Interface nativa

### 🔄 Futuras Implementações
- Edição de treinos e refeições
- Upload de fotos de progresso
- Notificações push
- Exportação de dados
- Gráficos mais avançados
- Compartilhamento de treinos

## 📋 Requisitos

- **Servidor Web**: Apache/Nginx
- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior
- **Extensões PHP**: PDO, PDO_MySQL, JSON

## 🛠️ Instalação

### 1. Configuração do Banco de Dados

1. Acesse o painel de controle da sua hospedagem (cPanel, phpMyAdmin, etc.)
2. Crie um novo banco de dados MySQL
3. Execute o script SQL localizado em `db/schema.sql`
4. Anote as credenciais do banco (host, nome, usuário, senha)

### 2. Configuração da Aplicação

1. Faça upload dos arquivos para a pasta `my-training` no seu servidor
2. Edite o arquivo `config/database.php` e atualize as credenciais:

```php
$host = 'localhost'; // ou seu host MySQL
$dbname = 'my_training_db'; // nome do banco criado
$username = 'seu_usuario_mysql';
$password = 'sua_senha_mysql';
```

### 3. Configuração do Servidor

#### Para Apache (.htaccess já incluído):
O arquivo `.htaccess` já está configurado para:
- Redirecionar para HTTPS (se disponível)
- Configurar cache para PWA
- Compressão GZIP

#### Para Nginx:
Adicione ao seu arquivo de configuração:

```nginx
location /my-training {
    try_files $uri $uri/ /my-training/index.php?$query_string;
    
    # Cache para PWA
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 4. Teste a Instalação

1. Acesse `https://agencialed.com/my-training/`
2. Você será redirecionado para a página de login
3. Use as credenciais de teste:
   - **Email**: `teste@exemplo.com`
   - **Senha**: `123456`

## 📱 Como Usar

### Primeiro Acesso
1. Faça login com as credenciais de teste ou crie uma nova conta
2. Configure seu perfil com altura, peso e objetivo
3. Comece adicionando seus treinos e refeições

### Gerenciando Treinos
1. Acesse "Treinos" no menu
2. Clique em "Adicionar" para criar exercícios
3. Organize por dia da semana
4. Configure séries, repetições e carga sugerida

### Plano Alimentar
1. Acesse "Alimentação" no menu
2. Adicione refeições com horário e quantidade
3. Inclua informações nutricionais (opcional)
4. Marque como consumidas no dashboard

### Acompanhando Progresso
1. Acesse "Progresso" no menu
2. Registre peso e medidas regularmente
3. Visualize gráficos de evolução
4. Acompanhe estatísticas de consistência

## 🔧 Personalização

### Cores e Tema
As cores principais estão definidas no CSS usando variáveis Tailwind:
- **Primária**: `#3B82F6` (azul)
- **Secundária**: `#10B981` (verde)
- **Gradiente**: `#667eea` para `#764ba2`

### Logo e Branding
Para alterar o logo:
1. Substitua o emoji no `manifest.json`
2. Atualize o ícone no header das páginas
3. Crie ícones personalizados para PWA

## 📊 Estrutura do Banco

### Tabelas Principais
- **usuarios**: Dados dos usuários
- **treinos**: Exercícios organizados por dia
- **alimentacao**: Refeições com informações nutricionais
- **checklist**: Registro diário de atividades
- **progresso**: Medidas e peso ao longo do tempo
- **configuracoes_usuario**: Preferências do usuário

## 🔒 Segurança

- Senhas criptografadas com `password_hash()`
- Proteção contra SQL Injection usando PDO
- Sanitização de dados de entrada
- Sessões seguras
- Validação de formulários

## 📱 PWA Features

### Instalação
- Acesse a aplicação no Chrome/Edge
- Clique em "Instalar" na barra de endereços
- Ou use o menu do navegador

### Funcionalidades Offline
- Cache de páginas principais
- Funciona sem internet
- Sincronização quando online

## 🚨 Troubleshooting

### Problemas Comuns

**Erro de Conexão com Banco:**
- Verifique as credenciais em `config/database.php`
- Confirme se o banco foi criado corretamente
- Teste a conexão via phpMyAdmin

**Páginas não Carregam:**
- Verifique se o mod_rewrite está ativo
- Confirme as permissões dos arquivos (644 para arquivos, 755 para pastas)
- Verifique os logs de erro do servidor

**PWA não Instala:**
- Certifique-se de que está usando HTTPS
- Verifique se o `manifest.json` está acessível
- Confirme se o service worker está registrado

## 📞 Suporte

Para suporte técnico ou dúvidas:
- Verifique os logs de erro do servidor
- Teste em diferentes navegadores
- Confirme a compatibilidade do PHP/MySQL

## 📄 Licença

Este projeto é de uso livre para fins educacionais e comerciais.

---

**Desenvolvido com ❤️ para agencialed.com** 