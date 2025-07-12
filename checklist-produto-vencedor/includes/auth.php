<?php
// Iniciar sessão apenas se não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function authenticateUser($email, $password) {
    global $pdo;
    
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
    if (!isLoggedIn()) {
        // Verificar se headers já foram enviados
        if (!headers_sent()) {
            header('Location: index.php');
            exit();
        } else {
            // Se headers já foram enviados, usar JavaScript
            echo '<script>window.location.href = "index.php";</script>';
            exit();
        }
    }
}

function logout() {
    session_destroy();
    // Verificar se headers já foram enviados
    if (!headers_sent()) {
        header('Location: index.php');
        exit();
    } else {
        // Se headers já foram enviados, usar JavaScript
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