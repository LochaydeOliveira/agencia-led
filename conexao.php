<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$db = "paymen58_sistema_integrado_led";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    // Em ambiente de produção, você pode querer mostrar uma mensagem mais amigável
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    } else {
        die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
    }
}
?>