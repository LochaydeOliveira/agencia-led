<?php
/**
 * Conexão com Banco de Dados - ValidaPro
 * Sistema completamente independente
 */

require_once __DIR__ . '/db_config.php';

try {
    // Primeiro, tentar conectar sem especificar o banco
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se o banco existe
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $databaseExists = $stmt->fetch();
    
    if (!$databaseExists) {
        // Criar o banco se não existir
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_COLLATE);
    }
    
    // Agora conectar ao banco específico
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabelas se não existirem
    createValidaProTables($pdo);
    
} catch (PDOException $e) {
    // Log do erro em vez de exibir
    error_log("Erro na conexão com o banco ValidaPro: " . $e->getMessage());
    $pdo = null;
}

function createValidaProTables($pdo) {
    if (!$pdo) return;
    
    // Tabela de usuários com campo active
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            usuario ENUM('cliente', 'admin') DEFAULT 'cliente',
            active TINYINT(1) DEFAULT 1,
            last_login DATETIME DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE
    );
    
    // Adicionar campos se não existirem
    $fields_to_add = [
        'active' => 'TINYINT(1) DEFAULT 1',
        'usuario' => "ENUM('cliente', 'admin') DEFAULT 'cliente'",
        'last_login' => 'DATETIME DEFAULT NULL',
        'reset_token' => 'VARCHAR(100) DEFAULT NULL',
        'reset_token_expira' => 'DATETIME DEFAULT NULL'
    ];
    
    foreach ($fields_to_add as $field => $definition) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN $field $definition");
        } catch (PDOException $e) {
            // Campo já existe, ignorar erro
        }
    }
    
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
        ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE
    );
    
    // Inserir usuário padrão se não existir
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@validapro.com']);
    
    if ($stmt->fetchColumn() == 0) {
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, usuario, active) VALUES (?, ?, ?, 'admin', 1)");
        $stmt->execute(['admin@validapro.com', $hashed_password, 'Administrador ValidaPro']);
    }
}
?> 