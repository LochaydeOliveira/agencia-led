<?php
/**
 * Configurações do Checklist do Produto Lucrativo
 * 
 * Este arquivo contém todas as configurações principais da aplicação.
 * Modifique conforme necessário para seu ambiente.
 */

// Configurações do Banco de Dados
// (Agora centralizadas no .env)

// Configurações da Aplicação
define('APP_NAME', 'Valida Pro');
define('APP_URL', 'https://agencialed.com/validapro'); // URL da sua aplicação
define('APP_VERSION', '1.0.0');

// Configurações de Email (para envio automático de credenciais)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'validapro@agencialed.com');
define('SMTP_PASSWORD', 'ProValida@LED$2025');
define('FROM_EMAIL', 'validapro@agencialed.com');
define('FROM_NAME', 'Valida Pro');

// Configurações de Segurança
define('SESSION_TIMEOUT', 3600); // 1 hora em segundos
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutos em segundos

// Configurações de Pontuação
define('MAX_POINTS', 10);
define('HIGH_POTENTIAL_MIN', 8);
define('MEDIUM_POTENTIAL_MIN', 5);

// Mensagens de Resultado
define('MESSAGES', [
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
]);

// Itens do Checklist
define('CHECKLIST_ITEMS', [
    'vida_mais_facil' => 'Deixa a vida do cliente mais fácil',
    'criativos_dinamicos' => 'Criativos são dinâmicos e de qualidade',
    'buscas_google' => 'Possui buscas no Google',
    'vendido_lojas' => 'Já está sendo vendido em lojas',
    'economiza_dinheiro' => 'Economiza dinheiro',
    'economiza_tempo' => 'Economiza tempo',
    'nao_nicho_sensivel' => 'Não é nicho sensível',
    'menos_50_dolares' => 'Custa menos de 50 dólares',
    'so_internet' => 'Só encontra na internet',
    'nao_commodity' => 'Produto não é commodity'
]);

// Configurações de Debug (desative em produção)
define('DEBUG_MODE', false);
define('SHOW_ERRORS', false);

// Configurações de Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Idioma
setlocale(LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');

// Função para obter configuração
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Função para debug
function debug($message) {
    if (getConfig('DEBUG_MODE')) {
        error_log("[DEBUG] " . $message);
    }
}

// Função para obter mensagem baseada na pontuação
function getResultMessage($points) {
    $messages = getConfig('MESSAGES');
    
    if ($points >= getConfig('HIGH_POTENTIAL_MIN')) {
        return $messages['high'];
    } elseif ($points >= getConfig('MEDIUM_POTENTIAL_MIN')) {
        return $messages['medium'];
    } else {
        return $messages['low'];
    }
}
?> 