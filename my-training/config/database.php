<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'paymen58_my_training_db'; // Banco criado com prefixo da hospedagem
$username = 'paymen58'; // Seu usuário MySQL
$password = 'u4q7+B6ly)obP_gxN9sNe'; // Sua senha MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Função para sanitizar dados
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Função para gerar hash seguro
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Função para verificar senha
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Função para gerar token JWT simples
function generateToken($user_id, $user_name) {
    $payload = [
        'user_id' => $user_id,
        'user_name' => $user_name,
        'exp' => time() + (60 * 60 * 24) // 24 horas
    ];
    return base64_encode(json_encode($payload));
}

// Função para verificar se usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Função para redirecionar se não estiver logado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?> 