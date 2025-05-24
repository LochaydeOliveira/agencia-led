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

    if (in_array($event, ['order.created', 'order.paid', 'order.status.updated'])) {

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

            $mailer = new Mailer();
            $mailer->sendOrderConfirmation($email, $name, $orderNumber, $order['value_total']);
            app_log("Email de pagamento pendente enviado para $email");
        }

        if ($event === 'order.paid') {
            if (!$existingOrder) {
                app_log("Erro: Pedido não encontrado");
                http_response_code(404);
                die('Pedido não encontrado');
            }

            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
            $stmt->execute([$statusAlias, $orderId]);

            $classificacao = 'prata';
            if ($productId == 40741683) {
                $classificacao = 'ouro';
            } elseif ($productId == 40741672) {
                $classificacao = 'diamante';
            }

            $stmt = $conn->prepare("SELECT id, classificacao FROM clientes WHERE email = ?");
            $stmt->execute([$email]);
            $existingClient = $stmt->fetch(PDO::FETCH_ASSOC);

            $mailer = new Mailer();

            if ($existingClient) {
                $cliente_id = $existingClient['id'];
                $currentClass = $existingClient['classificacao'];
                $hierarquia = ['prata' => 1, 'ouro' => 2, 'diamante' => 3];

                if ($hierarquia[$classificacao] > $hierarquia[$currentClass]) {
                    $stmt = $conn->prepare("UPDATE clientes SET classificacao = ?, atualizado_em = NOW() WHERE email = ?");
                    $stmt->execute([$classificacao, $email]);
                    app_log("Classificação atualizada: $email → $classificacao");
                }
            } else {
                $senhaVisivel = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $senhaHash = password_hash($senhaVisivel, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO clientes (nome, email, whatsapp, senha, classificacao, criado_em) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $whatsapp, $senhaHash, $classificacao]);

                $cliente_id = $conn->lastInsertId();
                app_log("Novo cliente: $email como $classificacao");

                $mailer->sendMemberAccess($email, $name, $senhaVisivel);
            }

            if ($classificacao === 'prata') {
                $stmt = $conn->prepare("SELECT id, nome FROM listas WHERE product_id = ?");
                $stmt->execute([$productId]);
                $lista = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($lista) {
                    $listaId = $lista['id'];
                    $nomeLista = $lista['nome'];

                    $stmt = $conn->prepare("SELECT id FROM clientes_listas WHERE cliente_id = ? AND lista_id = ?");
                    $stmt->execute([$cliente_id, $listaId]);
                    $exists = $stmt->fetch();

                    if (!$exists) {
                        $stmt = $conn->prepare("INSERT INTO clientes_listas (cliente_id, cliente, lista_id, nome, status) VALUES (?, ?, ?, ?, 'ativo')");
                        $stmt->execute([$cliente_id, $name, $listaId, $nomeLista]);
                        app_log("Lista $listaId associada ao cliente $cliente_id");
                    } else {
                        app_log("Cliente $cliente_id já possui a lista $listaId");
                    }
                } else {
                    app_log("Erro: Lista com product_id $productId não encontrada");
                }
            }

            http_response_code(200);
            echo json_encode(['status' => 'success']);
        }

        if ($event === 'order.status.updated') {
            if (!$existingOrder) {
                app_log("Erro: Pedido não encontrado para atualização");
                http_response_code(404);
                die('Pedido não encontrado');
            }

            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
            $stmt->execute([$statusAlias, $orderId]);

            if (in_array($statusAlias, ['cancelled', 'refused'])) {
                $stmt = $conn->prepare("UPDATE clientes SET status = 'suspenso', atualizado_em = NOW() WHERE email = ?");
                $stmt->execute([$email]);
                app_log("Cliente $email suspenso por status: $statusAlias");
            }

            http_response_code(200);
            echo json_encode(['status' => 'success']);
        }
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
?>
