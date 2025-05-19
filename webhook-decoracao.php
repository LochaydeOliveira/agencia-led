<?php
/**
 * Webhook Yampi – versão com logs e suporte a eventos múltiplos
 */

// Logar todos os cabeçalhos recebidos (para debug)
$headers = getallheaders();
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Cabeçalhos recebidos:\n" . print_r($headers, true) . "\n", FILE_APPEND);


declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// ───────────────────────────────────────────────────────────
// ⚙️ Configurações de ambiente
// ───────────────────────────────────────────────────────────

define('ENV', getenv('APP_ENV') ?: 'test');
$YAMPI_SECRET = getenv('YAMPI_SECRET') ?: 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'paymen58_lista_decoracao';
$dbUser = getenv('DB_USER') ?: 'paymen58';
$dbPass = getenv('DB_PASS') ?: 'u4q7+B6ly)obP_gxN9sNe';
$dsn     = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

$SMTP_HOST = getenv('SMTP_HOST') ?: 'smtp.zoho.com';
$SMTP_PORT = getenv('SMTP_PORT') ?: 587;
$SMTP_USER = getenv('SMTP_USER') ?: 'contato@agencialed.com';
$SMTP_PASS = getenv('SMTP_PASS') ?: 'Lochayde@154719';

$logFile = __DIR__ . '/../logs/yampi_webhook.log';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit;
}

$body = file_get_contents('php://input');
$assinaturaRecebida = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';

// Calcula HMAC em hex (original)
$assinaturaCalculadaHex = hash_hmac('sha256', $body, $YAMPI_SECRET);
// Calcula HMAC em base64 (possível formato enviado)
$assinaturaCalculadaBase64 = base64_encode(hash_hmac('sha256', $body, $YAMPI_SECRET, true));

// Log para debug da assinatura
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Assinatura recebida: $assinaturaRecebida\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Assinatura calculada (hex): $assinaturaCalculadaHex\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Assinatura calculada (base64): $assinaturaCalculadaBase64\n", FILE_APPEND);

// Validação da assinatura: aceita hex ou base64
if (!hash_equals($assinaturaCalculadaHex, $assinaturaRecebida) && !hash_equals($assinaturaCalculadaBase64, $assinaturaRecebida)) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Assinatura inválida.']);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erro: assinatura inválida.\n", FILE_APPEND);
    exit;
}

$payload = json_decode($body, true);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payload recebido:\n" . print_r($payload, true) . "\n", FILE_APPEND);

// Aceitar vários eventos que interessam para processar
$event = $payload['event'] ?? '';
$eventAceitos = ['order.created', 'order.paid', 'order.approved'];

if (!in_array($event, $eventAceitos, true)) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Evento ignorado: $event\n", FILE_APPEND);
    echo json_encode(['status' => 'ignorado', 'mensagem' => "Evento '$event' não processado."]);
    exit;
}

$order = $payload['data'] ?? [];
$codigo = trim($order['reference'] ?? '');
$email = filter_var($order['customer']['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
$status = $order['status']['alias'] ?? '';
$valor = (float)($order['value_total'] ?? 0);
$createdAtRaw = $order['created_at'] ?? null;
$pagamento = $order['payments'][0]['method'] ?? '';
$skus = array_column($order['products'] ?? [], 'sku');
$skuStr = implode(',', array_map('trim', $skus));

$createdAt = null;
if ($createdAtRaw) {
    $dt = DateTime::createFromFormat(DateTime::ATOM, $createdAtRaw);
    if ($dt !== false) {
        $createdAt = $dt->format('Y-m-d H:i:s');
    }
}

if (!$codigo || !$email || !$skuStr || !$createdAt) {
    http_response_code(400);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Dados incompletos. Código: $codigo, Email: $email, SKU: $skuStr, CreatedAt: $createdAt\n", FILE_APPEND);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos ou inválidos.']);
    exit;
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $chk = $pdo->prepare("SELECT id FROM pedidos WHERE codigo = ? LIMIT 1");
    $chk->execute([$codigo]);

    if ($chk->rowCount() === 0) {
        $ins = $pdo->prepare("INSERT INTO pedidos
            (codigo, email, sku, usado, criacao, status, valor_total, created_at, pagamento)
            VALUES
            (?, ?, ?, 0, NOW(), ?, ?, ?, ?)");
        $ins->execute([$codigo, $email, $skuStr, $status, $valor, $createdAt, $pagamento]);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Pedido inserido: $codigo\n", FILE_APPEND);
    } else {
        // Opcional: atualizar status/valor em pedidos já existentes se necessário
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Pedido já existe: $codigo\n", FILE_APPEND);
    }

} catch (PDOException $e) {
    error_log('DB error webhook: ' . $e->getMessage());
    http_response_code(500);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erro DB: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco de dados.']);
    exit;
}

// Enviar e-mail apenas se ambiente for teste ou status for pago
$enviarEmail = (ENV === 'test') || ($status === 'paid');

file_put_contents($logFile, date('Y-m-d H:i:s') . " - Status pedido: $status\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Enviar email? " . ($enviarEmail ? 'SIM' : 'NÃO') . "\n", FILE_APPEND);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($enviarEmail) {
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/src/PHPMailer.php';
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/src/SMTP.php';
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/src/Exception.php';

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet   = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = $SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = $SMTP_USER;
        $mail->Password   = $SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $SMTP_PORT;

        $mail->setFrom($SMTP_USER, 'Agência LED');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Pronto! Baixe Sua Lista de Fornecedores Agora!';
        $mail->Body = "<h2>Obrigado pela sua compra!</h2>
            <p>Seu código de pedido é: <strong>{$codigo}</strong></p>
            <p>Para baixar sua lista, clique no botão abaixo e informe o código quando solicitado:</p>
            <p><a href='https://agencialed.com/fornecedores-decoracao.php' target='_blank' style='padding: 10px 20px; background-color: #38b97e; color: white; text-decoration: none; border-radius: 5px;'>Acessar PDF da Lista</a></p>
            <p><em>Guarde seu código com segurança. Ele só poderá ser usado uma vez.</em></p>";

        $mail->send();
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Email enviado para: $email\n", FILE_APPEND);

    } catch (Exception $e) {
        error_log('Mailer error webhook: ' . $e->getMessage());
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erro email: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

http_response_code(200);
echo json_encode(['status' => 'sucesso', 'mensagem' => 'Processado com sucesso.']);
