<?php
session_name('VALIDAPRO_TESTE');
// Não iniciar sessão aqui - será iniciada no arquivo principal
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

function authenticateUser($email, $password) {
    global $pdo;
    
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, password, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erro na autenticação: " . $e->getMessage());
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Redireciona para o login se não estiver logado
        if (!headers_sent()) {
            header('Location: index.php');
            exit();
        } else {
            echo '<script>window.location.href = "index.php";</script>';
            exit();
        }
    }
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Log para debug
    error_log("Logout chamado. Sessão antes: " . print_r($_SESSION, true));

    // Limpa todas as variáveis de sessão
    $_SESSION = array();

    // Se estiver usando cookies de sessão, apaga o cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, '/');
    }

    // Destroi a sessão
    session_destroy();

    // Log para debug
    error_log("Logout chamado. Sessão depois: " . print_r($_SESSION, true));

    // Redireciona para a página inicial
    if (!headers_sent()) {
        header('Location: index.php');
        exit();
    } else {
        echo '<script>window.location.href = "index.php";</script>';
        exit();
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'name' => $_SESSION['user_name']
    ];
}
?> 