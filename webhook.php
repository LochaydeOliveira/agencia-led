<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Mailer.php';

function app_log($message) {
    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
}

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';

app_log("Webhook recebido - Signature: $signature");
app_log("Payload bruto: $payload");

if (empty($payload)) {
    app_log("Erro: Payload vazio");
    http_response_code(400);
    die('Payload vazio');
}

$data = json_decode($payload, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    app_log("Erro ao decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    die('JSON inválido');
}

$event = $data['event'] ?? null;
if (!$event) {
    app_log("Erro: Evento não encontrado no payload");
    http_response_code(400);
    die('Evento não encontrado');
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    if (in_array($event, ['order.created', 'order.paid'])) {
        $order = $data['resource'] ?? null;
        if (!$order) {
            app_log("Erro: Dados do pedido não encontrados");
            http_response_code(400);
            die('Dados do pedido não encontrados');
        }

        $orderId = $order['id'];
        $orderNumber = $order['number'];
        $customer = $order['customer']['data'];
        $name = $customer['name'];
        $email = $customer['email'];
        $whatsapp = $customer['phone']['full_number'] ?? '';
        $statusAlias = $order['status']['data']['alias'];
        $productId = $order['items']['data'][0]['product_id'] ?? null;

        $stmt = $conn->prepare("SELECT id FROM orders WHERE yampi_order_id = ?");
        $stmt->execute([$orderId]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event === 'order.created') {
            if (!$existingOrder) {
                $stmt = $conn->prepare("INSERT INTO orders (yampi_order_id, order_number, customer_name, customer_email, status, created_at, product_id) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                $stmt->execute([$orderId, $orderNumber, $name, $email, $statusAlias, $productId]);
                app_log("Novo pedido inserido: $orderNumber ($email)");
            } else {
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
                $stmt->execute([$statusAlias, $orderId]);
                app_log("Pedido existente atualizado: $orderNumber");
            }
        }

        if ($event === 'order.paid') {
            if (!$existingOrder) {
                app_log("Erro: Pedido não encontrado");
                http_response_code(404);
                die('Pedido não encontrado');
            }

            // Atualiza status
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
            $stmt->execute([$statusAlias, $orderId]);

            // Criação de usuário
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                $senhaVisivel = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $senhaHash = password_hash($senhaVisivel, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, whatsapp, senha) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $whatsapp, $senhaHash]);

                app_log("Usuário criado: $email");

                $mailer = new Mailer();
                $mailer->sendMemberAccess($email, $name, $senhaVisivel);
            } else {
                app_log("Usuário já existe: $email");
            }

            // Geração de token de download
            $stmt = $conn->prepare("SELECT id FROM download_tokens WHERE order_id = ?");
            $stmt->execute([$existingOrder['id']]);
            $existingToken = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingToken) {
                $token = bin2hex(random_bytes(16));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

                $stmt = $conn->prepare("INSERT INTO download_tokens (order_id, token, expires_at, product_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$existingOrder['id'], $token, $expiresAt, $productId]);

                app_log("Token de download gerado: $token");

                $mailer = new Mailer();
                $mailer->sendDownloadLink($email, $name, $orderNumber, $token);
            } else {
                app_log("Token já existe para este pedido.");
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        app_log("Evento ignorado: $event");
        http_response_code(200);
        echo json_encode(['status' => 'ignored']);
    }

} catch (Exception $e) {
    app_log("Erro no webhook: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error']);
}
