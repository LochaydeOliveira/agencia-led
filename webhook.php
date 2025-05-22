<?php
// Ativa exibição de todos os erros (ótimo para testes)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Importa configurações e classes necessárias
require_once __DIR__ . '/config/config.php';     // Configurações do sistema
require_once __DIR__ . '/src/Database.php';      // Conexão com banco de dados
require_once __DIR__ . '/src/Mailer.php';        // Classe para envio de e-mails

// Função auxiliar para registrar logs no arquivo de log
function app_log($message) {
    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND); // Salva no caminho definido em config
}

// Lê o conteúdo bruto enviado via POST (json do webhook da Yampi)
$payload = file_get_contents('php://input');

// Captura a assinatura do cabeçalho enviada pela Yampi
$signature = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';

// Salva no log o recebimento da requisição e o conteúdo recebido
app_log("Webhook recebido - Signature: $signature");
app_log("Payload bruto: $payload");

// Se não houver payload, aborta e registra erro
if (empty($payload)) {
    app_log("Erro: Payload vazio");
    http_response_code(400);
    die('Payload vazio');
}

// Tenta decodificar o JSON enviado
$data = json_decode($payload, true);

// Se houver erro no JSON, registra e finaliza
if (json_last_error() !== JSON_ERROR_NONE) {
    app_log("Erro ao decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    die('JSON inválido');
}

// Verifica se o evento foi enviado
$event = $data['event'] ?? null;
if (!$event) {
    app_log("Erro: Evento não encontrado no payload");
    http_response_code(400);
    die('Evento não encontrado');
}

try {
    // Conecta ao banco de dados
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Processa apenas os eventos relevantes
    if (in_array($event, ['order.created', 'order.paid', 'order.status.updated'])) {
        $order = $data['resource'] ?? null;
        if (!$order) {
            app_log("Erro: Dados do pedido não encontrados");
            http_response_code(400);
            die('Dados do pedido não encontrados');
        }

        // Extrai dados importantes do pedido
        $orderId     = $order['id'];
        $orderNumber = $order['number'];
        $customer    = $order['customer']['data'];
        $name        = $customer['name'];
        $email       = $customer['email'];
        $whatsapp    = $customer['phone']['full_number'] ?? '';
        $statusAlias = $order['status']['data']['alias'];
        $productId   = $order['items']['data'][0]['product_id'] ?? null;

        // Verifica se esse pedido já existe no banco
        $stmt = $conn->prepare("SELECT id FROM orders WHERE yampi_order_id = ?");
        $stmt->execute([$orderId]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔹 EVENTO order.created: quando o cliente finaliza o pedido
        if ($event === 'order.created') {
            if (!$existingOrder) {
                // Insere novo pedido no banco
                $stmt = $conn->prepare("
                    INSERT INTO orders (yampi_order_id, order_number, customer_name, customer_email, status, created_at, product_id) 
                    VALUES (?, ?, ?, ?, ?, NOW(), ?)
                ");
                $stmt->execute([$orderId, $orderNumber, $name, $email, $statusAlias, $productId]);

                app_log("Novo pedido inserido: $orderNumber ($email)");
            } else {
                // Atualiza status se já existir
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
                $stmt->execute([$statusAlias, $orderId]);

                app_log("Pedido existente atualizado: $orderNumber");
            }
        }

        // 🔹 EVENTO order.paid: pagamento confirmado
        if ($event === 'order.paid') {
            if (!$existingOrder) {
                app_log("Erro: Pedido não encontrado");
                http_response_code(404);
                die('Pedido não encontrado');
            }

            // Atualiza status do pedido no banco
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE yampi_order_id = ?");
            $stmt->execute([$statusAlias, $orderId]);

            // Verifica se o usuário já existe na área de membros
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se ainda não existir, cria novo usuário com senha aleatória
            if (!$existingUser) {
                $senhaVisivel = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $senhaHash = password_hash($senhaVisivel, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, whatsapp, senha) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $whatsapp, $senhaHash]);

                app_log("Usuário criado: $email");

                // Envia dados de acesso ao cliente
                $mailer = new Mailer();
                $mailer->sendMemberAccess($email, $name, $senhaVisivel);
            } else {
                app_log("Usuário já existe: $email");
            }

            // Verifica se já existe um token para esse pedido
            $stmt = $conn->prepare("SELECT id FROM download_tokens WHERE order_id = ?");
            $stmt->execute([$existingOrder['id']]);
            $existingToken = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingToken) {
                // Gera token único e data de expiração
                $token = bin2hex(random_bytes(16));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

                // Insere token na tabela de download
                $stmt = $conn->prepare("INSERT INTO download_tokens (order_id, token, expires_at, product_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$existingOrder['id'], $token, $expiresAt, $productId]);

                app_log("Token de download gerado: $token");

                // Envia link de download para o cliente
                $mailer = new Mailer();
                $mailer->sendDownloadLink($email, $name, $orderNumber, $token);
            } else {
                app_log("Token já existe para este pedido.");
            }
        }

        // Tudo ocorreu bem
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        // Evento irrelevante, apenas ignora
        app_log("Evento ignorado: $event");
        http_response_code(200);
        echo json_encode(['status' => 'ignored']);
    }

} catch (Exception $e) {
    // Captura qualquer erro não tratado
    app_log("Erro no webhook: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error']);
}
