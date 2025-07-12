-- Script para criar o banco de dados e tabelas do Checklist do Produto Lucrativo

-- Criar banco de dados (se não existir)
CREATE DATABASE IF NOT EXISTS `paymen58_checklist_produto_lucrativo` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE `paymen58_checklist_produto_lucrativo`;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de resultados
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

-- Inserir usuário padrão (senha: 123456)
INSERT IGNORE INTO `users` (`email`, `password`, `name`) VALUES 
('admin@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador');

-- Verificar se as tabelas foram criadas
SHOW TABLES;

-- Verificar usuários
SELECT * FROM users; 