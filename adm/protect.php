<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verifica se a sessão existe e se o usuário está logado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    error_log("Tentativa de acesso sem sessão válida");
    header("Location: ../login_usuarios.php");
    exit;
}

// Verifica se o nível de acesso é válido
if (!isset($_SESSION['nivel']) || !in_array($_SESSION['nivel'], ['admin', 'operador'])) {
    error_log("Tentativa de acesso com nível inválido: " . $_SESSION['nivel']);
    header("Location: ../login_usuarios.php");
    exit;
}

// Verifica se o usuário está ativo
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'ativo') {
    error_log("Tentativa de acesso de usuário inativo");
    header("Location: ../login_usuarios.php");
    exit;
}
?>
