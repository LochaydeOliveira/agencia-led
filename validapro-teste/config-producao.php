<?php
/**
 * Configurações de Produção - Checklist do Produto Lucrativo
 * 
 * ⚠️ IMPORTANTE: Configure estas variáveis para seu ambiente de produção
 */

// Configurações do Banco de Dados (PRODUÇÃO)
// (Agora centralizadas no .env)

// Configurações da Aplicação (PRODUÇÃO)
define('APP_NAME', 'Valida Pro');
define('APP_URL', 'https://agencialed.com/validapro'); // URL da sua aplicação
define('APP_VERSION', '2.0.0');

// Configurações de Email (PRODUÇÃO)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'validapro@agencialed.com');
define('SMTP_PASSWORD', 'ProValida@LED$2025');
define('FROM_EMAIL', 'validapro@agencialed.com');
define('FROM_NAME', 'Valida Pro');

// Configurações de Segurança (PRODUÇÃO)
define('SESSION_TIMEOUT', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 8); // Senha mais forte
define('MAX_LOGIN_ATTEMPTS', 3); // Menos tentativas
define('LOGIN_TIMEOUT', 1800); // 30 minutos

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

// Headers de Segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Função para obter configuração
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Função para debug (desabilitada em produção)
function debug($message) {
    // Não fazer nada em produção
    return;
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