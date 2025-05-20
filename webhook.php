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

// Log de todos os headers recebidos
app_log("Headers recebidos: " . print_r(getallheaders(), true));

// Recebe o payload do webhook
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';

app_log("Webhook recebido - Signature: $signature");
app_log("Payload bruto: $payload");

// Verifica se o payload está vazio
if (empty($payload)) {
    app_log("Erro: Payload vazio");
    http_response_code(400);
    die('Payload vazio');
}

// Decodifica o payload
$data = json_decode($payload, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    app_log("Erro ao decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    die('JSON inválido');
}

app_log("Payload decodificado: " . print_r($data, true));

// Verifica se é um evento de pedido
if (!isset($data['event'])) {
    app_log("Erro: Evento não encontrado no payload");
    http_response_code(400);
    die('Evento não encontrado');
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $event = $data['event'];
    
    // Verifica o tipo de evento
    if ($event === 'order.created' || $event === 'order.paid') {
        // Extrai os dados do pedido do formato da Yampi
        $order = $data['resource'] ?? null;
        
        if (!$order) {
            app_log("Erro: Dados do pedido não encontrados no payload");
            http_response_code(400);
            die('Dados do pedido não encontrados');
        }
        
        app_log("Processando evento: $event para pedido #" . $order['number']);
        
        // Verifica se o pedido já existe
        $sql = "SELECT id FROM orders WHERE yampi_order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order['id']]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event === 'order.created') {
            // Se o pedido já existe, apenas atualiza o status
            if ($existingOrder) {
                $sql = "UPDATE orders SET status = ? WHERE yampi_order_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$order['status']['data']['alias'], $order['id']]);
                app_log("Pedido #" . $order['number'] . " atualizado");
            } else {
                // Cria novo pedido
                $sql = "INSERT INTO orders (yampi_order_id, order_number, customer_name, customer_email, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $order['id'],
                    $order['number'],
                    $order['customer']['data']['name'],
                    $order['customer']['data']['email'],
                    $order['status']['data']['alias']
                ]);
                
                app_log("Novo pedido #" . $order['number'] . " criado");
                
                // Extrai informações do PIX
                $pixCode = '';
                $pixExpiration = '';
                $orderItems = [];
                
                // Procura o código PIX nos dados do pedido
                if (isset($order['payments']['data'])) {
                    foreach ($order['payments']['data'] as $payment) {
                        if (isset($payment['pix_code'])) {
                            $pixCode = $payment['pix_code'];
                            if (isset($payment['expires_at'])) {
                                $pixExpiration = date('d/m/Y \à\s H:i', strtotime($payment['expires_at']));
                            }
                            break;
                        }
                    }
                }
                
                // Extrai informações dos itens do pedido
                if (isset($order['items']['data'])) {
                    foreach ($order['items']['data'] as $item) {
                        $orderItems[] = [
                            'name' => $item['name'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ];
                    }
                }
                
                // Envia email de confirmação do pedido
                $mailer = new Mailer();
                $result = $mailer->sendOrderConfirmation(
                    $order['customer']['data']['email'],
                    $order['customer']['data']['name'],
                    $order['number'],
                    $order['value_total']
                );
                
                if ($result) {
                    app_log("Email de confirmação enviado com sucesso para " . $order['customer']['data']['email']);
                } else {
                    app_log("Falha ao enviar email de confirmação para " . $order['customer']['data']['email']);
                }
            }
        }
        else if ($event === 'order.paid') {
            if (!$existingOrder) {
                app_log("Erro: Pedido #" . $order['number'] . " não encontrado");
                http_response_code(404);
                die('Pedido não encontrado');
            }
            
            // Atualiza o status do pedido
            $sql = "UPDATE orders SET status = ? WHERE yampi_order_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$order['status']['data']['alias'], $order['id']]);
            
            // Verifica se já existe um token para este pedido
            $sql = "SELECT id FROM download_tokens WHERE order_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$existingOrder['id']]);
            $existingToken = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existingToken) {
                // Gera token de download
                $token = bin2hex(random_bytes(16));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                $sql = "INSERT INTO download_tokens (order_id, token, expires_at) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$existingOrder['id'], $token, $expiresAt]);
                
                app_log("Token gerado para pedido #" . $order['number'] . ": $token");
                
                // Envia email com link de download
                $mailer = new Mailer();
                $result = $mailer->sendDownloadLink(
                    $order['customer']['data']['email'],
                    $order['customer']['data']['name'],
                    $order['number'],
                    $token
                );
                
                if ($result) {
                    app_log("Email enviado com sucesso para " . $order['customer']['data']['email']);
                } else {
                    app_log("Falha ao enviar email para " . $order['customer']['data']['email']);
                }
            } else {
                app_log("Token já existe para o pedido #" . $order['number']);
            }
        }
    } else {
        app_log("Evento não suportado: $event");
        http_response_code(400);
        die('Evento não suportado');
    }
    
    // Responde com sucesso
    http_response_code(200);
    echo json_encode(['status' => 'success']);
    
} catch (Exception $e) {
    app_log("Erro ao processar webhook: " . $e->getMessage());
    app_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor']);
} 