<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Mailer.php';

// Função para log personalizado
function app_log($message) {
    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
}

// Simula os dados que viriam da Yampi
$orderData = [
    'order' => [
        'id' => 'TEST-' . time(),
        'number' => 'TEST-' . time(),
        'status' => 'created',
        'customer' => [
            'name' => 'Lochayde Teste Webhook',
            'email' => 'lochaydeguerreiro2@gmail.com' // Substitua pelo seu email
        ]
    ]
];

try {
    app_log("Iniciando teste de webhook com dados: " . json_encode($orderData));
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Insere o pedido no banco
    $sql = "INSERT INTO orders (yampi_order_id, order_number, customer_name, customer_email, status, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $orderData['order']['id'],
        $orderData['order']['number'],
        $orderData['order']['customer']['name'],
        $orderData['order']['customer']['email'],
        $orderData['order']['status']
    ]);
    
    $orderId = $conn->lastInsertId();
    app_log("Pedido criado com ID: $orderId");
    
    // Gera o token de download
    $token = bin2hex(random_bytes(16));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $sql = "INSERT INTO download_tokens (order_id, token, expires_at) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$orderId, $token, $expiresAt]);
    
    app_log("Token gerado: $token");
    
    // Envia o email
    $mailer = new Mailer();
    $result = $mailer->sendDownloadLink(
        $orderData['order']['customer']['email'],
        $orderData['order']['customer']['name'],
        $orderData['order']['number'],
        $token
    );
    
    if ($result) {
        app_log("Email enviado com sucesso");
        echo "Teste concluído com sucesso! Verifique seu email e os logs em " . LOG_FILE;
    } else {
        app_log("Falha ao enviar email");
        echo "Falha ao enviar email. Verifique os logs em " . LOG_FILE;
    }
    
} catch (Exception $e) {
    app_log("Erro no teste: " . $e->getMessage());
    app_log("Stack trace: " . $e->getTraceAsString());
    echo "Erro no teste: " . $e->getMessage();
} 