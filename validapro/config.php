<?php
/**
 * Configurações do ValidaPro
 * 
 * Ambiente padrão de produção
 */

// Configurações do Banco de Dados
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost'); // Seu host do banco
define('DB_NAME', 'paymen58_validapro'); // Seu nome do banco
define('DB_USER', 'paymen58'); // Seu usuário do banco
define('DB_PASS', 'u4q7+B6ly)obP_gxN9sNe'); // Sua senha do banco

// Configurações da Aplicação
define('APP_NAME', 'ValidaPro');
define('APP_URL', 'https://agencialed.com/validapro/'); // URL da sua aplicação
define('APP_VERSION', '2.0.0');

// Configurações de Email (PRODUÇÃO)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'validapro@agencialed.com'); // Seu email
define('SMTP_PASSWORD', 'Valida@2025'); // Sua senha de app
define('FROM_EMAIL', 'validapro@agencialed.com');
define('FROM_NAME', 'ValidaPro');

// Configurações de Segurança (PRODUÇÃO)
define('SESSION_TIMEOUT', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 8); // Senha mais forte
define('MAX_LOGIN_ATTEMPTS', 5); // Máximo de tentativas
define('LOGIN_TIMEOUT', 90); // 15 minutos de bloqueio
define('CSRF_TOKEN_TIMEOUT', 1800); // 30 minutos
define('SESSION_REGENERATION_TIME', 300); // 5 segundos

// Configurações de Pontuação
define('MAX_POINTS', 10);
define('HIGH_POTENTIAL_MIN', 8);
define('MEDIUM_POTENTIAL_MIN', 5);

// Configurações de Debug (DESATIVADO em produção)
define('DEBUG_MODE', false);
define('SHOW_ERRORS', false);

// Configurações de Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Idioma
setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');

// Headers de Segurança (apenas se headers não foram enviados)
if (!headers_sent()) {
    // Headers básicos de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Headers adicionais de segurança
    header('Strict-Transport-Security: max-age=315360 includeSubDomains; preload');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; img-src \'self\' data: https:; font-src \'self\' https://cdnjs.cloudflare.com; connect-src \'self\' https://cdnjs.cloudflare.com');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('X-Permitted-Cross-Domain-Policies: none');
}

// Função para obter configuração
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Função para debug (habilitada temporariamente)
function debug($message) {
    if (getConfig('DEBUG_MODE', false)) {
        echo "<div style='background: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px; font-family: monospace;'>";
        echo "<strong>DEBUG:</strong> " . htmlspecialchars($message);
        echo "</div>";
    }
}

// Função para obter mensagem baseada na pontuação
function getResultMessage($points) {
    $messages = [
        'high' => [
            'text' => 'Produto com alto potencial!',
            'icon' => 'fas fa-trophy',
            'color' => 'text-green-600',
            'bg_color' => 'bg-green-100'
        ],
        'medium' => [
            'text' => 'Produto razoável, com potencial',
            'icon' => 'fas fa-star',
            'color' => 'text-yellow-600',
            'bg_color' => 'bg-yellow-100'
        ],
        'low' => [
            'text' => 'Produto fraco, repense a escolha',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'text-red-600',
            'bg_color' => 'bg-red-100'
        ]
    ];
    
    if ($points >= getConfig('HIGH_POTENTIAL_MIN')) {
        return $messages['high'];
    } elseif ($points >= getConfig('MEDIUM_POTENTIAL_MIN')) {
        return $messages['medium'];
    } else {
        return $messages['low'];
    }
}
?> 