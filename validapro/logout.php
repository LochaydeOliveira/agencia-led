<?php
// Configurações para exibir erros no navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destrói o cookie da sessão
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header('Location: login.php');
exit(); 

