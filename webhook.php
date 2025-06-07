<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Mailer.php';

// Log inicial para debug
$debug_log = "[" . date('Y-m-d H:i:s') . "] Webhook iniciado\n";
$debug_log .= "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
$debug_log .= "CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'não definido') . "\n";
$debug_log .= "REMOTE_ADDR: " . ($_SERVER['REMOTE_ADDR'] ?? 'não definido') . "\n";
$debug_log .= "HTTP_USER_AGENT: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'não definido') . "\n";

// Tenta escrever no log
try {
    if (!file_exists(dirname(LOG_FILE))) {
        mkdir(dirname(LOG_FILE), 0777, true);
    }
    file_put_contents(LOG_FILE, $debug_log, FILE_APPEND);
} catch (Exception $e) {
    error_log("Erro ao escrever no log: " . $e->getMessage());
}

function app_log($message) {
    try {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message" . PHP_EOL;
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
    } catch (Exception $e) {
        error_log("Erro ao escrever no log: " . $e->getMessage());
    }
}

// Log inicial para debug
app_log("Webhook iniciado - " . date('Y-m-d H:i:s'));
app_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
app_log("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'não definido'));

// ✅ Função para traduzir status da Yampi para Português
function traduzirStatus($statusAlias) {
    $map = [
        'paid' => 'Pago',
        'cancelled' => 'Cancelado',
        'refused' => 'Recusado',
        'waiting_payment' => 'Aguardando Pagamento',
        'under_analysis' => 'Em Análise',
        'pending_refund' => 'Reembolso Pendente',
        'partially_refunded' => 'Parcialmente Reembolsado',
        'refunded' => 'Reembolsado'
    ];
    return $map[$statusAlias] ?? ucfirst($statusAlias);
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

    if (in_array($event, ['order.created', 'order.paid', 'order.status.updated', 'order.updated'])) {

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
        $statusPt = traduzirStatus($statusAlias); // ✅ traduz para PT
        $productId = $order['items']['data'][0]['product_id'] ?? null;

        $stmt = $conn->prepare("SELECT id FROM orders WHERE yampi_order_id = ?");
        $stmt->execute([$orderId]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event === 'order.created') {
            if (!$existingOrder) {
                $stmt = $conn->prepare("INSERT INTO orders (yampi_order_id, order_number, customer_name, customer_email, status, created_at, product_id) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                $stmt->execute([$orderId, $orderNumber, $name, $email, $statusPt, $productId]);
                app_log("Novo pedido inserido: $orderNumber ($email)");
            } else {
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
                $stmt->execute([$statusPt, $orderId]);
                app_log("Pedido existente atualizado: $orderNumber");
            }

            $mailer = new Mailer();
            $mailer->sendOrderConfirmation($email, $name, $orderNumber, $order['value_total']);
            app_log("Email de pagamento pendente enviado para $email");
        }

        if ($event === 'order.paid') {
            if (!$existingOrder) {
                app_log("Erro: Pedido não encontrado para o ID: $orderId");
                http_response_code(404);
                die('Pedido não encontrado');
            }

            // Verifica se o status atual é realmente "Pago"
            if ($statusAlias !== 'paid') {
                app_log("Atenção: Status do pedido $orderNumber não é 'paid' (status atual: $statusAlias)");
                http_response_code(400);
                die('Status inválido');
            }

            $stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE yampi_order_id = ?");
            $stmt->execute([$statusPt, $orderId]);
            app_log("Status do pedido $orderNumber atualizado para: $statusPt");

            $classificacao = 'prata';
            if ($productId == 40741683) {
                $classificacao = 'ouro';
            } elseif ($productId == 40741672) {
                $classificacao = 'diamante';
            }
            app_log("Classificação definida para o produto $productId: $classificacao");

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
                    app_log("Classificação atualizada para $email: $currentClass → $classificacao");
                } else {
                    app_log("Classificação mantida para $email: $currentClass (não inferior a $classificacao)");
                }
            } else {
                $senhaVisivel = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $senhaHash = password_hash($senhaVisivel, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO clientes (nome, email, whatsapp, senha, classificacao, status, criado_em) VALUES (?, ?, ?, ?, ?, 'ativo', NOW())");
                $stmt->execute([$name, $email, $whatsapp, $senhaHash, $classificacao]);

                $cliente_id = $conn->lastInsertId();
                app_log("Novo cliente criado: ID=$cliente_id, Email=$email, Classificação=$classificacao");

                try {
                    $mailer->sendMemberAccess($email, $name, $senhaVisivel);
                    app_log("Email de acesso enviado com sucesso para $email");
                } catch (Exception $e) {
                    app_log("Erro ao enviar email de acesso para $email: " . $e->getMessage());
                    // Não interrompe o fluxo, apenas registra o erro
                }
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
                        $stmt = $conn->prepare("INSERT INTO clientes_listas (cliente_id, cliente, lista_id, nome_lista, status) VALUES (?, ?, ?, ?, 'ativo')");
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

        if (in_array($event, ['order.status.updated', 'order.updated'])) {
            if (!$existingOrder) {
                app_log("Erro: Pedido não encontrado para atualização");
                http_response_code(404);
                die('Pedido não encontrado');
            }

            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
            $stmt->execute([$statusPt, $orderId]);

            if (in_array($statusAlias, ['cancelled', 'refused'])) {
                $stmt = $conn->prepare("UPDATE clientes SET status = 'suspenso', atualizado_em = NOW() WHERE email = ?");
                $stmt->execute([$email]);
            
                if ($stmt->rowCount() > 0) {
                    app_log("Cliente $email suspenso com sucesso por status: $statusAlias");
                } else {
                    app_log("Atenção: Cliente $email não encontrado para suspensão.");
                }
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
