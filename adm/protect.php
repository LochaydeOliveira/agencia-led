<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['nivel'])) {
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
