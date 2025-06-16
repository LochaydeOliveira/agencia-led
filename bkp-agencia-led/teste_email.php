<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/functions.php';  // Carrega as funções utilitárias primeiro
require_once __DIR__ . '/src/Mailer.php';

// Função para log
function app_log($message) {
    try {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message" . PHP_EOL;
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
    } catch (Exception $e) {
        error_log("Erro ao escrever no log: " . $e->getMessage());
    }
}

try {
    app_log("Iniciando teste de email");
    
    $mailer = new Mailer();
    
    // Teste 1: Email de confirmação
    app_log("Testando envio de email de confirmação");
    $result = $mailer->sendOrderConfirmation(
        'maletamacho@gmail.com',
        'Nome Teste',
        'TESTE-123',
        99.90
    );
    app_log("Resultado do teste 1: " . ($result ? "Sucesso" : "Falha"));
    
    // Teste 2: Email de acesso
    app_log("Testando envio de email de acesso");
    $result = $mailer->sendMemberAccess(
        'maletamacho@gmail.com',
        'Nome Teste',
        'senha123'
    );
    app_log("Resultado do teste 2: " . ($result ? "Sucesso" : "Falha"));
    
    echo "Testes concluídos. Verifique o arquivo de log para mais detalhes.";
    
} catch (Exception $e) {
    app_log("Erro durante os testes: " . $e->getMessage());
    app_log("Stack trace: " . $e->getTraceAsString());
    echo "Erro: " . $e->getMessage();
} 