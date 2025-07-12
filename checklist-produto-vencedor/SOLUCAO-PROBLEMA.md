# Solução para Problema de Acesso - Checklist do Produto Vencedor

## Problema Identificado
Você não está conseguindo acessar a aplicação em: `https://agencialed.com/checklist-produto-vencedor/index.php`

## Possíveis Causas e Soluções

### 1. Banco de Dados Não Existe
O banco de dados `paymen58_checklist_produto_lucrativo` pode não ter sido criado.

**Solução:**
- Acesse: `https://agencialed.com/checklist-produto-vencedor/test-connection.php`
- Este arquivo irá verificar a conexão e criar o banco automaticamente

### 2. Executar Script SQL Manualmente
Se preferir, execute o script SQL no phpMyAdmin:

1. Acesse o phpMyAdmin do seu hosting
2. Execute o conteúdo do arquivo `setup-database.sql`
3. Ou copie e cole o seguinte SQL:

```sql
CREATE DATABASE IF NOT EXISTS `paymen58_checklist_produto_lucrativo` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `paymen58_checklist_produto_lucrativo`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `promessa_principal` TEXT,
    `cliente_consciente` TEXT,
    `beneficios` TEXT,
    `mecanismo_unico` TEXT,
    `pontos` INT DEFAULT 0,
    `nota_final` INT DEFAULT 0,
    `mensagem` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `users` (`email`, `password`, `name`) VALUES 
('admin@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador');
```

### 3. Credenciais de Acesso
Após configurar o banco, use as seguintes credenciais:

- **Email:** admin@exemplo.com
- **Senha:** 123456

### 4. URLs de Acesso
- **Página principal:** `https://agencialed.com/checklist-produto-vencedor/`
- **Login direto:** `https://agencialed.com/checklist-produto-vencedor/index.php`
- **Teste de conexão:** `https://agencialed.com/checklist-produto-vencedor/test-connection.php`

### 5. Verificação de Permissões
Se ainda houver problemas, verifique:

1. **Permissões de arquivo:** Todos os arquivos devem ter permissão 644
2. **Permissões de pasta:** Todas as pastas devem ter permissão 755
3. **Arquivo .htaccess:** Deve estar funcionando corretamente

### 6. Logs de Erro
Se a página aparecer em branco, verifique os logs de erro do PHP no seu hosting.

## Passos para Testar

1. Primeiro, acesse: `https://agencialed.com/checklist-produto-vencedor/test-connection.php`
2. Se der erro, execute o script SQL no phpMyAdmin
3. Depois tente acessar: `https://agencialed.com/checklist-produto-vencedor/`
4. Faça login com as credenciais fornecidas

## Contato
Se ainda houver problemas, verifique os logs de erro do seu hosting ou entre em contato com o suporte. 