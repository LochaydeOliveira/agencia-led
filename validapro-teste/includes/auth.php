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
    // Limpa todas as variáveis da sessão
    $_SESSION = [];

    // Remove o cookie de sessão com path global
    setcookie(session_name(), '', time() - 3600, '/');

    // Destroi a sessão
    session_destroy();

    // Redireciona
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