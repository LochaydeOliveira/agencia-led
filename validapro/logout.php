<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destrói o cookie da sessão
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destrói a sessão
destroy_session();

// Redireciona para a página de login
header('Location: login.php');
exit();

function destroy_session() {
    session_destroy();
} 

