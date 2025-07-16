<?php
// Sistema de Autenticação ValidaPro - Versão 2.0
// Sistema robusto e testado

// Função para iniciar sessão de forma segura
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Verificar se headers já foram enviados
        if (headers_sent()) {
            // Se headers já foram enviados, tentar iniciar sessão com supressão de warnings
            error_log("Headers já enviados - tentando iniciar sessão com supressão de warnings");
            @session_start();
            
            // Se ainda não conseguiu, usar sessão alternativa
            if (session_status() === PHP_SESSION_NONE) {
                error_log("Sessão não pôde ser iniciada - usando sessão alternativa");
                // Criar sessão alternativa usando cookies
                if (!isset($_COOKIE['validapro_session'])) {
                    $session_id = uniqid('validapro_', true);
                    setcookie('validapro_session', $session_id, time() + 3600, '/');
                    $_COOKIE['validapro_session'] = $session_id;
                }
                
                // Simular dados de sessão
                if (!isset($_SESSION)) {
                    $_SESSION = [];
                }
            }
        } else {
            // Configurar parâmetros de sessão seguros apenas se possível
            @ini_set('session.cookie_httponly', 1);
            @ini_set('session.use_only_cookies', 1);
            @ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            
            session_start();
        }
        
        // Regenerar ID da sessão para segurança (apenas se possível)
        if (!isset($_SESSION['initialized']) && !headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
            $_SESSION['initialized'] = true;
        } elseif (!isset($_SESSION['initialized'])) {
            $_SESSION['initialized'] = true;
        }
    }
}

// Função para autenticar usuário
function authenticateUser($email, $password) {
    global $pdo;

    if (!$pdo) {
        error_log("Erro: Conexão com banco não disponível");
        return false;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, email, password, name FROM users WHERE email = ? AND active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Iniciar sessão
            initSession();
            
            // Limpar sessão anterior
            $_SESSION = [];
            
            // Definir dados do usuário
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Log de login bem-sucedido
            error_log("Login bem-sucedido: " . $user['email']);
            
            return true;
        }

        error_log("Tentativa de login falhou para: " . $email);
        return false;
    } catch (PDOException $e) {
        error_log("Erro na autenticação: " . $e->getMessage());
        return false;
    }
}

// Função para verificar se usuário está logado
function isLoggedIn() {
    initSession();
    
    // Verificar se há dados de usuário na sessão
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
        return false;
    }
    
    // Verificar timeout da sessão
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600;
    if (time() - $_SESSION['last_activity'] > $timeout) {
        logout();
        return false;
    }
    
    // Atualizar última atividade
    $_SESSION['last_activity'] = time();
    
    return true;
}

// Função para requerer login
function requireLogin() {
    if (!isLoggedIn()) {
        // Limpar qualquer saída anterior
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Redirecionar para login
        if (!headers_sent()) {
            header('Location: login.php');
            exit();
        } else {
            echo '<script>window.location.href = "login.php";</script>';
            exit();
        }
    }
}

// Função de logout robusta
function logout() {
    // Iniciar sessão se necessário
    initSession();
    
    // Log do logout
    if (isset($_SESSION['user_email'])) {
        error_log("Logout do usuário: " . $_SESSION['user_email']);
    } else {
        error_log("Logout sem dados de usuário na sessão");
    }
    
    // Limpar todas as variáveis de sessão
    $_SESSION = [];
    
    // Destruir o cookie de sessão
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
    
    // Destruir a sessão se estiver ativa
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    
    // Limpar qualquer saída anterior
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Redirecionar para login
    if (!headers_sent()) {
        header('Location: login.php');
        exit();
    } else {
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    }
}

// Função para obter dados do usuário atual
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

// Função para verificar se a sessão expirou
function checkSessionTimeout() {
    if (!isLoggedIn()) {
        return false;
    }
    
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600;
    if (time() - $_SESSION['last_activity'] > $timeout) {
        logout();
        return false;
    }
    
    return true;
}

// Função para renovar sessão
function renewSession() {
    if (isLoggedIn()) {
        $_SESSION['last_activity'] = time();
        return true;
    }
    return false;
}
?>
