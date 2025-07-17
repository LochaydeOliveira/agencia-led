<?php
require_once __DIR__ . '/../config.php';

try {
    // Primeiro, tentar conectar sem especificar o banco
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se o banco existe
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $databaseExists = $stmt->fetch();
    
    if (!$databaseExists) {
        // Criar o banco se não existir
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    
    // Agora conectar ao banco específico
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabelas se não existirem
    createTables($pdo);
    
} catch (PDOException $e) {
    // Log do erro em vez de exibir
    error_log("Erro na conexão com o banco: " . $e->getMessage());
    $pdo = null;
}

function createTables($pdo) {
    if (!$pdo) return;
    
    // Tabela de usuários com campo active
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Adicionar campo active se não existir
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN active TINYINT(1) DEFAULT 1");
    } catch (PDOException $e) {
        // Campo já existe, ignorar erro
    }
    
    // Adicionar campos para recuperação de senha se não existirem
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(100) DEFAULT NULL");
    } catch (PDOException $e) {}
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token_expira DATETIME DEFAULT NULL");
    } catch (PDOException $e) {}
    
    // Tabela de resultados
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            promessa_principal TEXT,
            cliente_consciente TEXT,
            beneficios TEXT,
            mecanismo_unico TEXT,
            pontos INT DEFAULT 0,
            nota_final INT DEFAULT 0,
            mensagem VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Inserir usuário padrão se não existir
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@exemplo.com']);
    
    if ($stmt->fetchColumn() == 0) {
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, active) VALUES (?, ?, ?, 1)");
        $stmt->execute(['admin@exemplo.com', $hashed_password, 'Administrador']);
    }
}
?> 