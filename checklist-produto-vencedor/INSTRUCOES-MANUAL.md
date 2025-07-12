# Instruções Manuais - Criar Tabelas no phpMyAdmin

## Passo a Passo para Criar o Banco de Dados

### 1. Acessar o phpMyAdmin
- Entre no painel do seu hosting
- Procure por "phpMyAdmin" ou "Banco de Dados"
- Clique para abrir o phpMyAdmin

### 2. Executar o Script SQL
1. No phpMyAdmin, clique na aba "SQL" (geralmente no topo)
2. Copie TODO o conteúdo do arquivo `criar-tabelas.sql`
3. Cole no campo de texto do SQL
4. Clique em "Executar" ou "Go"

### 3. Verificar se Funcionou
Após executar, você deve ver:
- Mensagem: "Tabelas criadas com sucesso!"
- Lista das tabelas: `users` e `results`
- Um usuário criado: `admin@exemplo.com`

### 4. Testar a Aplicação
Depois de criar as tabelas:
- Acesse: `https://agencialed.com/checklist-produto-vencedor/`
- Login: `admin@exemplo.com`
- Senha: `123456`

## Script SQL Completo (para copiar e colar)

```sql
-- 1. Criar banco de dados
CREATE DATABASE IF NOT EXISTS `paymen58_checklist_produto_lucrativo` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Selecionar banco
USE `paymen58_checklist_produto_lucrativo`;

-- 3. Criar tabela de usuários
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Criar tabela de resultados
CREATE TABLE `results` (
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

-- 5. Inserir usuário padrão
INSERT INTO `users` (`email`, `password`, `name`) VALUES 
('admin@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador');

-- 6. Verificar
SELECT 'Tabelas criadas com sucesso!' as status;
SHOW TABLES;
SELECT * FROM users;
```

## Se Der Erro

### Erro de Permissão
Se der erro de permissão para criar banco:
1. Use um banco existente (como `paymen58_my_training_db`)
2. Mude o nome no arquivo `includes/db.php`
3. Execute apenas as partes 3, 4 e 5 do script

### Erro de Tabela Já Existe
Se der erro que a tabela já existe:
1. Execute apenas as partes 5 e 6 do script
2. Ou delete as tabelas antigas primeiro

## Depois de Criar as Tabelas

1. Teste a aplicação: `https://agencialed.com/checklist-produto-vencedor/`
2. Se ainda não funcionar, verifique os logs de erro do PHP
3. Entre em contato se precisar de mais ajuda 