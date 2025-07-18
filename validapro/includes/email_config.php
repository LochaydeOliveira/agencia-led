<?php
// Configuração exclusiva de e-mail para o ValidaPro
// Ecossistema completamente independente

// Configurações da Aplicação ValidaPro
if (!defined('APP_URL')) {
    define('APP_URL', 'https://agencialed.com/validapro/');
}
if (!defined('APP_NAME')) {
    define('APP_NAME', 'ValidaPro');
}

// Configurações SMTP do Zoho (exclusivas do ValidaPro)
define('SMTP_HOST', 'smtp.zoho.com');
define('SMTP_USER', 'validapro@agencialed.com');
define('SMTP_PASS', 'Valida@2025');
define('SMTP_SECURE', 'tls');
define('SMTP_PORT', 587);

// Configurações do remetente
define('FROM_EMAIL', 'validapro@agencialed.com');
define('FROM_NAME', 'ValidaPro');

// Configurações de debug (ativar apenas para testes)
define('SMTP_DEBUG', false);

// Timeout de conexão
define('SMTP_TIMEOUT', 30);

// Configurações de segurança específicas do ValidaPro
define('SESSION_TIMEOUT', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutos
define('CSRF_TOKEN_TIMEOUT', 1800); // 30 minutos

// Configurações de pontuação do ValidaPro
define('MAX_POINTS', 10);
define('HIGH_POTENTIAL_MIN', 8);
define('MEDIUM_POTENTIAL_MIN', 5);

// Configurações de timezone
if (!function_exists('setValidaProTimezone')) {
    function setValidaProTimezone() {
        date_default_timezone_set('America/Sao_Paulo');
    }
    setValidaProTimezone();
} 