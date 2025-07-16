<?php
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
    if (!isLoggedIn()) {
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
    echo "<pre>🔵 LOGOUT: iniciando função logout()</pre>";

    // Garante que a sessão foi iniciada
    if (session_status() === PHP_SESSION_NONE) {
        echo "<pre>🔵 LOGOUT: iniciando sessão</pre>";
        session_start();
    } else {
        echo "<pre>🔵 LOGOUT: sessão já iniciada</pre>";
    }

    // Exibir conteúdo da sessão antes de apagar
    echo "<pre>🔵 LOGOUT: conteúdo da sessão antes do reset:\n" . print_r($_SESSION, true) . "</pre>";

    // Limpa todas as variáveis de sessão
    $_SESSION = [];

    // Remove o cookie de sessão
    if (ini_get("session.use_cookies")) {
        echo "<pre>🔵 LOGOUT: removendo cookie de sessão</pre>";
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroi a sessão
    echo "<pre>🔵 LOGOUT: destruindo sessão</pre>";
    session_destroy();

    // Exibir se os headers já foram enviados
    if (headers_sent($file, $line)) {
        echo "<pre>🔴 HEADERS JÁ ENVIADOS em $file na linha $line</pre>";
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    } else {
        echo "<pre>🟢 HEADERS OK - redirecionando via header()</pre>";
        header('Location: login.php');
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
