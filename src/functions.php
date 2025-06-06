<?php
/**
 * Função para registrar logs da aplicação
 * @param string $message Mensagem a ser registrada
 * @param string $level Nível do log (info, error, warning)
 */
function app_log($message, $level = 'info') {
    $log_dir = __DIR__ . '/../logs';
    
    // Cria o diretório de logs se não existir
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Registra o log
    file_put_contents($log_file, $log_message, FILE_APPEND);
} 