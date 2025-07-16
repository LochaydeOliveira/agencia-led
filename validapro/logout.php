<?php
// Logout seguro padrão
session_start();

// Limpar todas as variáveis de sessão
$_SESSION = array();

// Se existir um cookie de sessão, removê-lo
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destruir a sessão
session_destroy();

// Redirecionar para a tela de login
header('Location: index.php');
exit(); 