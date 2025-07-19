<?php
// Sistema de Autenticação ValidaPro - Versão 21(Segurança Aprimorada)
// Sistema robusto, testado e seguro

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
            @ini_set('session.cookie_samesite', 'Strict');
            @ini_set('session.gc_maxlifetime', 3600);
            
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

// Função para autenticar usuário com proteções adicionais
function authenticateUser($email, $password) {
    global $pdo;

    if (!$pdo) {
        error_log("Erro: Conexão com banco não disponível");
        return false;
    }

    // Proteção contra ataques de força bruta
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $attempts_key = 'login_attempts_' . $ip; 
    // Verificar tentativas de login
    if (isset($_SESSION[$attempts_key]) && $_SESSION[$attempts_key]['count'] >= 5) {
        $timeout = 15 * 60; // 15 minutos
        if (time() - $_SESSION[$attempts_key]['time'] < $timeout) {
            error_log("Tentativas de login excedidas para IP: $ip");
            return false;
        } else {
            // Reset após timeout
            unset($_SESSION[$attempts_key]);
        }
    }

    try {
        $stmt = $pdo->prepare("SELECT id, email, password, name, active, last_login FROM users WHERE email = ? AND active = 1");
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
            $_SESSION['session_id'] = session_id();
            $_SESSION['ip_address'] = $ip;
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Atualizar último login no banco
            $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_stmt->execute([$user['id']]);
            
            // Limpar tentativas de login
            unset($_SESSION[$attempts_key]);
            
            // Log de login bem-sucedido
            error_log("Login bem-sucedido: " . $user['email'] . " (IP: $ip)");
            
            return true;
        }

        // Incrementar tentativas de login
        if (!isset($_SESSION[$attempts_key])) {
            $_SESSION[$attempts_key] = ['count' => 1, 'time' => time()];
        } else {
            $_SESSION[$attempts_key]['count']++;
            $_SESSION[$attempts_key]['time'] = time();
        }

        error_log("Tentativa de login falhou para: " . $email . " (IP: $ip)");
        return false;
    } catch (PDOException $e) {
        error_log("Erro na autenticação: " . $e->getMessage());
        return false;
    }
}

// Função para verificar se usuário está logado com validações adicionais
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
    
    // Verificar se o IP mudou (proteção contra session hijacking)
    $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $current_ip) {
        error_log("Possível session hijacking detectado - IP mudou de {$_SESSION['ip_address']} para $current_ip");
        logout();
        return false;
}

    // Verificar se o User Agent mudou
    $current_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $current_ua) {
        error_log("Possível session hijacking detectado - User Agent mudou");
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

// Função de logout robusta e segura


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

// Função para gerar token CSRF seguro
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    // Renovar token a cada 30 minutos
    if (time() - $_SESSION['csrf_token_time'] > 1800) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

// Função para validar token CSRF
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Verificar se o token não expirou (1 hora)
    if (time() - $_SESSION['csrf_token_time'] > 3600) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Função para sanitizar entrada do usuário
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para registrar atividade suspeita
function logSuspiciousActivity($activity, $details = []) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $user_id = $_SESSION['user_id'] ?? 'not_logged_in';
    
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $ip,
        'user_agent' => $user_agent,
        'user_id' => $user_id,
        'activity' => $activity,
        'details' => $details
    ];
    
    error_log("ATIVIDADE SUSPEITA: " . json_encode($log_data));
}

function gerarTokenSenha($email) {
    global $pdo;

    error_log('[RECUPERAÇÃO] Iniciando processo para: ' . $email);
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ? AND active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log('[RECUPERAÇÃO] Usuário não encontrado ou inativo: ' . $email);
        return false;
    }

    $token = hash('sha256', bin2hex(random_bytes(32)));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

    try {
        $stmt = $pdo->prepare("INSERT INTO recuperacao_senha (user_id, token, expira) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $token, $expira]);
        error_log('[RECUPERAÇÃO] Token gerado e salvo para user_id ' . $user['id'] . ' | Token: ' . $token);
    } catch (PDOException $e) {
        error_log('[RECUPERAÇÃO] ERRO ao salvar token: ' . $e->getMessage());
        return false;
    }

    require_once __DIR__ . '/mailer.php';
    $enviado = sendRecoveryEmail($email, $user['name'], $token);
    if ($enviado) {
        error_log('[RECUPERAÇÃO] E-mail enviado com sucesso para: ' . $email);
    } else {
        error_log('[RECUPERAÇÃO] FALHA ao enviar e-mail para: ' . $email);
    }
    return $enviado;
}
?>
