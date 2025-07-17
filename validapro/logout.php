<?php
// Logout Simples e Funcional - ValidaPro
// Versão revisada

// Iniciar sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log do logout
if (isset($_SESSION['user_email'])) {
    error_log("Logout do usuário: " . $_SESSION['user_email']);
}

// Limpar sessão
$_SESSION = [];

// Destruir cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir cookie alternativo se existir
if (isset($_COOKIE['validapro_session'])) {
    setcookie('validapro_session', '', time() - 42000, '/');
    unset($_COOKIE['validapro_session']);
}

// Destruir sessão
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Redirecionar para login
if (!headers_sent()) {
    header('Location: login.php');
    exit();
} else {
    echo '<script>window.location.href = "login.php";</script>';
    exit();
}
?> 