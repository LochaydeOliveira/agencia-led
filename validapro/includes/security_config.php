<?php
/**
 * Configuração de Segurança - ValidaPro
 * Sistema completamente independente
 */

// Configurações de segurança específicas do ValidaPro
define('VALIDAPRO_SESSION_NAME', 'validapro_session');
define('VALIDAPRO_CSRF_TOKEN_NAME', 'validapro_csrf_token');

// Headers de segurança específicos do ValidaPro
function setValidaProSecurityHeaders() {
    if (!headers_sent()) {
        // Headers básicos de segurança
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Headers específicos do ValidaPro
        header('X-ValidaPro-Version: 2.0.0');
        header('X-ValidaPro-Security: enabled');
        
        // Headers adicionais de segurança
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; img-src \'self\' data: https:; font-src \'self\' https://cdnjs.cloudflare.com; connect-src \'self\' https://cdnjs.cloudflare.com');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('X-Permitted-Cross-Domain-Policies: none');
    }
}

// Função para gerar token CSRF específico do ValidaPro
function generateValidaProCSRFToken() {
    if (!isset($_SESSION[VALIDAPRO_CSRF_TOKEN_NAME])) {
        $_SESSION[VALIDAPRO_CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[VALIDAPRO_CSRF_TOKEN_NAME];
}

// Função para validar token CSRF específico do ValidaPro
function validateValidaProCSRFToken($token) {
    return isset($_SESSION[VALIDAPRO_CSRF_TOKEN_NAME]) && 
           hash_equals($_SESSION[VALIDAPRO_CSRF_TOKEN_NAME], $token);
}

// Função para sanitizar entrada específica do ValidaPro
function sanitizeValidaProInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeValidaProInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validar email específica do ValidaPro
function validateValidaProEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para log de atividades suspeitas do ValidaPro
function logValidaProSuspiciousActivity($activity, $details = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'activity' => $activity,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'details' => $details
    ];
    
    error_log("ValidaPro Security: " . json_encode($log_entry));
}

// Função para verificar força da senha do ValidaPro
function validateValidaProPasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = "A senha deve ter pelo menos " . PASSWORD_MIN_LENGTH . " caracteres";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "A senha deve conter pelo menos uma letra maiúscula";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "A senha deve conter pelo menos uma letra minúscula";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "A senha deve conter pelo menos um número";
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "A senha deve conter pelo menos um caractere especial";
    }
    
    return $errors;
}

// Função para obter pontuação de força da senha do ValidaPro
function getValidaProPasswordStrength($password) {
    $score = 0;
    
    if (strlen($password) >= 8) $score += 1;
    if (preg_match('/[A-Z]/', $password)) $score += 1;
    if (preg_match('/[a-z]/', $password)) $score += 1;
    if (preg_match('/[0-9]/', $password)) $score += 1;
    if (preg_match('/[^A-Za-z0-9]/', $password)) $score += 1;
    if (strlen($password) >= 12) $score += 1;
    
    return min($score, 5); // Máximo 5 pontos
}

// Função para obter mensagem de força da senha do ValidaPro
function getValidaProPasswordStrengthMessage($strength) {
    $messages = [
        0 => ['text' => 'Muito fraca', 'color' => 'text-red-600', 'bg' => 'bg-red-100'],
        1 => ['text' => 'Fraca', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'],
        2 => ['text' => 'Regular', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-100'],
        3 => ['text' => 'Boa', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100'],
        4 => ['text' => 'Forte', 'color' => 'text-green-600', 'bg' => 'bg-green-100'],
        5 => ['text' => 'Muito forte', 'color' => 'text-green-700', 'bg' => 'bg-green-200']
    ];
    
    return $messages[$strength] ?? $messages[0];
}
?> 