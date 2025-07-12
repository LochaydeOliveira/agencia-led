<?php
// Configuração do banco de dados MySQL
$host = "localhost";
$db = "paymen58_checklist_produto_lucrativo";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar tabelas se não existirem
    createTables($pdo);
    
} catch (PDOException $e) {
    die("Erro na conexão com o banco: " . $e->getMessage());
}

function createTables($pdo) {
    // Tabela de usuários
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
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
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
        $stmt->execute(['admin@exemplo.com', $hashed_password, 'Administrador']);
    }
}
?> 