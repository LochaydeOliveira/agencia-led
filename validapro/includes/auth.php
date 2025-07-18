<?php
// Sistema de Autenticação ValidaPro - Versão 2.0 (Completamente Independente)
// Sistema robusto, testado e seguro - Ecossistema próprio

// Carregar configurações independentes
require_once __DIR__ . '/email_config.php';
require_once __DIR__ . '/security_config.php';

// Função para iniciar sessão de forma segura
function initValidaProSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configurar nome da sessão específico do ValidaPro
        session_name(VALIDAPRO_SESSION_NAME);
        
        // Verificar se headers já foram enviados
        if (headers_sent()) {
            error_log("ValidaPro: Headers já enviados - tentando iniciar sessão com supressão de warnings");
            @session_start();
            
            if (session_status() === PHP_SESSION_NONE) {
                error_log("ValidaPro: Sessão não pôde ser iniciada - usando sessão alternativa");
                if (!isset($_COOKIE[VALIDAPRO_SESSION_NAME])) {
                    $session_id = uniqid('validapro_', true);
                    setcookie(VALIDAPRO_SESSION_NAME, $session_id, time() + SESSION_TIMEOUT, '/');
                    $_COOKIE[VALIDAPRO_SESSION_NAME] = $session_id;
                }
                
                if (!isset($_SESSION)) {
                    $_SESSION = [];
                }
            }
        } else {
            // Configurar parâmetros de sessão seguros
            @ini_set('session.cookie_httponly', 1);
            @ini_set('session.use_only_cookies', 1);
            @ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            @ini_set('session.cookie_samesite', 'Strict');
            @ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
            
            session_start();
        }
        
        // Regenerar ID da sessão para segurança
        if (!isset($_SESSION['validapro_initialized']) && !headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
            $_SESSION['validapro_initialized'] = true;
        } elseif (!isset($_SESSION['validapro_initialized'])) {
            $_SESSION['validapro_initialized'] = true;
        }
    }
}

// Função para autenticar usuário com proteções adicionais
function authenticateValidaProUser($email, $password) {
    global $pdo;

    if (!$pdo) {
        error_log("ValidaPro: Erro - Conexão com banco não disponível");
        return false;
    }

    // Proteção contra ataques de força bruta
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $attempts_key = 'validapro_login_attempts_' . $ip;
    
    // Verificar tentativas de login
    if (isset($_SESSION[$attempts_key]) && $_SESSION[$attempts_key]['count'] >= MAX_LOGIN_ATTEMPTS) {
        $timeout = LOGIN_TIMEOUT;
        if (time() - $_SESSION[$attempts_key]['time'] < $timeout) {
            error_log("ValidaPro: Tentativas de login excedidas para IP: $ip");
            logValidaProSuspiciousActivity('login_attempts_exceeded', ['ip' => $ip]);
            return false;
        } else {
            // Reset após timeout
            unset($_SESSION[$attempts_key]);
        }
    }

    try {
        $stmt = $pdo->prepare("SELECT id, email, password, name, active, last_login, usuario FROM users WHERE email = ? AND active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Iniciar sessão
            initValidaProSession();
            
            // Limpar sessão anterior
            $_SESSION = [];
            
            // Definir dados do usuário
            $_SESSION['validapro_user_id'] = $user['id'];
            $_SESSION['validapro_user_email'] = $user['email'];
            $_SESSION['validapro_user_name'] = $user['name'];
            $_SESSION['validapro_user_tipo'] = $user['usuario'];
            $_SESSION['validapro_login_time'] = time();
            $_SESSION['validapro_last_activity'] = time();
            $_SESSION['validapro_session_id'] = session_id();
            $_SESSION['validapro_ip_address'] = $ip;
            $_SESSION['validapro_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Atualizar último login no banco
            $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_stmt->execute([$user['id']]);
            
            // Limpar tentativas de login
            unset($_SESSION[$attempts_key]);
            
            // Log de login bem-sucedido
            error_log("ValidaPro: Login bem-sucedido: " . $user['email'] . " (IP: $ip)");
            
            return true;
        }

        // Incrementar tentativas de login
        if (!isset($_SESSION[$attempts_key])) {
            $_SESSION[$attempts_key] = ['count' => 1, 'time' => time()];
        } else {
            $_SESSION[$attempts_key]['count']++;
            $_SESSION[$attempts_key]['time'] = time();
        }

        error_log("ValidaPro: Tentativa de login falhou para: " . $email . " (IP: $ip)");
        logValidaProSuspiciousActivity('login_failed', ['email' => $email, 'ip' => $ip]);
        return false;
    } catch (PDOException $e) {
        error_log("ValidaPro: Erro na autenticação: " . $e->getMessage());
        return false;
    }
}

// Função para verificar se usuário está logado
function isValidaProLoggedIn() {
    initValidaProSession();
    
    // Verificar se há dados de usuário na sessão
    if (!isset($_SESSION['validapro_user_id']) || !isset($_SESSION['validapro_login_time'])) {
        return false;
    }
    
    // Verificar timeout da sessão
    if (time() - $_SESSION['validapro_last_activity'] > SESSION_TIMEOUT) {
        logoutValidaPro();
        return false;
    }
    
    // Verificar se o IP mudou (proteção contra session hijacking)
    $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (isset($_SESSION['validapro_ip_address']) && $_SESSION['validapro_ip_address'] !== $current_ip) {
        error_log("ValidaPro: Possível session hijacking detectado - IP mudou de {$_SESSION['validapro_ip_address']} para $current_ip");
        logValidaProSuspiciousActivity('session_hijacking_suspected', [
            'old_ip' => $_SESSION['validapro_ip_address'],
            'new_ip' => $current_ip
        ]);
        logoutValidaPro();
        return false;
    }

    // Verificar se o User Agent mudou
    $current_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isset($_SESSION['validapro_user_agent']) && $_SESSION['validapro_user_agent'] !== $current_ua) {
        error_log("ValidaPro: Possível session hijacking detectado - User Agent mudou");
        logValidaProSuspiciousActivity('session_hijacking_suspected', ['user_agent_changed' => true]);
        logoutValidaPro();
        return false;
    }
    
    // Atualizar última atividade
    $_SESSION['validapro_last_activity'] = time();
    
    return true;
}

// Função para requerer login
function requireValidaProLogin() {
    if (!isValidaProLoggedIn()) {
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

// Função de logout robusta e segura
function logoutValidaPro() {
    initValidaProSession();
    
    // Log de logout
    if (isset($_SESSION['validapro_user_email'])) {
        error_log("ValidaPro: Logout do usuário: " . $_SESSION['validapro_user_email']);
    }
    
    // Limpar dados da sessão
    $_SESSION = [];
    
    // Destruir cookie da sessão
    if (isset($_COOKIE[VALIDAPRO_SESSION_NAME])) {
        setcookie(VALIDAPRO_SESSION_NAME, '', time() - 3600, '/');
        unset($_COOKIE[VALIDAPRO_SESSION_NAME]);
    }
    
    // Destruir sessão
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

// Função para obter dados do usuário atual
function getCurrentValidaProUser() {
    if (!isValidaProLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['validapro_user_id'],
        'email' => $_SESSION['validapro_user_email'],
        'name' => $_SESSION['validapro_user_name'],
        'tipo' => $_SESSION['validapro_user_tipo']
    ];
}

// Funções de compatibilidade (para manter compatibilidade com código existente)
function initSession() { return initValidaProSession(); }
function authenticateUser($email, $password) { return authenticateValidaProUser($email, $password); }
function isLoggedIn() { return isValidaProLoggedIn(); }
function requireLogin() { return requireValidaProLogin(); }
function logout() { return logoutValidaPro(); }
function getCurrentUser() { return getCurrentValidaProUser(); }
function validateEmail($email) { return validateValidaProEmail($email); }
function generateCSRFToken() { return generateValidaProCSRFToken(); }
function validateCSRFToken($token) { return validateValidaProCSRFToken($token); }
function sanitizeInput($input) { return sanitizeValidaProInput($input); }
?>
