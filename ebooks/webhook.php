<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Mailer.php';

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método não permitido');
}

// Obtém o payload do webhook
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';

// Verifica a assinatura do webhook
if (!verifyWebhookSignature($payload, $signature)) {
    http_response_code(401);
    die('Assinatura inválida');
}

// Decodifica o payload
$data = json_decode($payload, true);
if (!$data) {
    http_response_code(400);
    die('Payload inválido');
}

// Processa o evento
$event = $data['event'] ?? '';
$resource = $data['resource'] ?? [];

switch ($event) {
    case 'order.created':
        handleOrderCreated($resource);
        break;
    case 'order.paid':
        handleOrderPaid($resource);
        break;
    case 'order.status.updated':
        handleOrderStatusUpdated($resource);
        break;
    default:
        http_response_code(400);
        die('Evento não suportado');
}

// Função para verificar a assinatura do webhook
function verifyWebhookSignature($payload, $signature) {
    $expectedSignature = hash_hmac('sha256', $payload, YAMPI_WEBHOOK_SECRET);
    return hash_equals($expectedSignature, $signature);
}

// Função para processar pedido criado
function handleOrderCreated($order) {
    $db = Database::getInstance()->getConnection();
    
    try {
        $stmt = $db->prepare("INSERT INTO orders (yampi_order_id, order_number, customer_email, customer_name, product_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $order['id'],
            $order['number'],
            $order['customer']['data']['email'],
            $order['customer']['data']['name'],
            $order['items']['data'][0]['product_id'],
            $order['status']['data']['alias']
        ]);
        
        // Registra o log
        $orderId = $db->lastInsertId();
        logEvent($orderId, 'order.created', json_encode($order));
        
        http_response_code(200);
        echo 'Pedido registrado com sucesso';
    } catch (PDOException $e) {
        error_log("Erro ao registrar pedido: " . $e->getMessage());
        http_response_code(500);
        die('Erro ao processar pedido');
    }
}

// Função para processar pedido pago
function handleOrderPaid($order) {
    $db = Database::getInstance()->getConnection();
    
    try {
        // Atualiza o status do pedido
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
        $stmt->execute([$order['status']['data']['alias'], $order['id']]);
        
        // Gera o token de download
        $downloadToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Registra o download
        $stmt = $db->prepare("INSERT INTO downloads (order_id, download_token, download_url, expires_at) SELECT id, ?, ?, ? FROM orders WHERE yampi_order_id = ?");
        $downloadUrl = SITE_URL . '/download.php?token=' . $downloadToken;
        $stmt->execute([$downloadToken, $downloadUrl, $expiresAt, $order['id']]);
        
        // Envia o email
        $mailer = new Mailer();
        $mailer->sendDownloadLink(
            $order['customer']['data']['email'],
            $order['customer']['data']['name'],
            $order['number'],
            $downloadUrl
        );
        
        // Registra o log
        $stmt = $db->prepare("SELECT id FROM orders WHERE yampi_order_id = ?");
        $stmt->execute([$order['id']]);
        $orderId = $stmt->fetchColumn();
        logEvent($orderId, 'order.paid', json_encode($order));
        
        http_response_code(200);
        echo 'Pedido processado com sucesso';
    } catch (Exception $e) {
        error_log("Erro ao processar pedido pago: " . $e->getMessage());
        http_response_code(500);
        die('Erro ao processar pedido');
    }
}

// Função para processar atualização de status
function handleOrderStatusUpdated($order) {
    $db = Database::getInstance()->getConnection();
    
    try {
        // Atualiza o status do pedido
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
        $stmt->execute([$order['status']['data']['alias'], $order['id']]);
        
        // Se o pedido foi cancelado, invalida o download
        if ($order['status']['data']['alias'] === 'cancelled') {
            $stmt = $db->prepare("UPDATE downloads SET expires_at = NOW() WHERE order_id = (SELECT id FROM orders WHERE yampi_order_id = ?)");
            $stmt->execute([$order['id']]);
        }
        
        // Registra o log
        $stmt = $db->prepare("SELECT id FROM orders WHERE yampi_order_id = ?");
        $stmt->execute([$order['id']]);
        $orderId = $stmt->fetchColumn();
        logEvent($orderId, 'order.status.updated', json_encode($order));
        
        http_response_code(200);
        echo 'Status atualizado com sucesso';
    } catch (PDOException $e) {
        error_log("Erro ao atualizar status: " . $e->getMessage());
        http_response_code(500);
        die('Erro ao atualizar status');
    }
}

// Função para registrar logs
function logEvent($orderId, $eventType, $eventData) {
    $db = Database::getInstance()->getConnection();
    
    try {
        $stmt = $db->prepare("INSERT INTO logs (order_id, event_type, event_data) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $eventType, $eventData]);
    } catch (PDOException $e) {
        error_log("Erro ao registrar log: " . $e->getMessage());
    }
} 