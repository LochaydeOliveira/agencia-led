<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$logFile = __DIR__ . '/../logs/yampi_webhook.log';

// Configurações (ideal pegar de variáveis ambiente)
define('ENV', getenv('APP_ENV') ?: 'test');
$YAMPI_SECRET = getenv('YAMPI_SECRET') ?: 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'paymen58_lista_decoracao';
$dbUser = getenv('DB_USER') ?: 'paymen58';
$dbPass = getenv('DB_PASS') ?: 'u4q7+B6ly)obP_gxN9sNe';
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

$SMTP_HOST = getenv('SMTP_HOST') ?: 'smtp.zoho.com';
$SMTP_PORT = getenv('SMTP_PORT') ?: 587;
$SMTP_USER = getenv('SMTP_USER') ?: 'contato@agencialed.com';
$SMTP_PASS = getenv('SMTP_PASS') ?: 'Lochayde@154719';

// Verifica método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit;
}

// Função para validar assinatura
function validarAssinatura(string $payload, string $assinaturaRecebida, string $secret): bool {
    // Yampi usa assinatura base64 HMAC SHA256 no header X-Yampi-Hmac-SHA256
    $assinaturaCalculada = base64_encode(hash_hmac('sha256', $payload, $secret, true));
    return hash_equals($assinaturaCalculada, $assinaturaRecebida);
}

$body = file_get_contents('php://input');
$assinaturaRecebida = $_SERVER['HTTP_X_YAMPI_HMAC_SHA256'] ?? '';

file_put_contents($logFile, date('Y-m-d H:i:s') . " - Recebido webhook\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Assinatura recebida: $assinaturaRecebida\n", FILE_APPEND);

if (!validarAssinatura($body, $assinaturaRecebida, $YAMPI_SECRET)) {
    http_response_code(403);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Assinatura inválida.\n", FILE_APPEND);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Assinatura inválida.']);
    exit;
}

$payload = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - JSON inválido no payload.\n", FILE_APPEND);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Payload JSON inválido.']);
    exit;
}

file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payload recebido: " . print_r($payload, true) . "\n", FILE_APPEND);

$event = $payload['event'] ?? '';
$eventAceitos = ['order.created', 'order.paid', 'order.approved'];

if (!in_array($event, $eventAceitos, true)) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Evento ignorado: $event\n", FILE_APPEND);
    echo json_encode(['status' => 'ignorado', 'mensagem' => "Evento '$event' não processado."]);
    exit;
}

$order = $payload['data'] ?? [];

// Extrai dados com segurança e fallback
$codigo = trim($order['reference'] ?? '');
$email = filter_var($order['customer']['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
$status = $order['status']['alias'] ?? '';
$valor = isset($order['value_total']) ? (float)$order['value_total'] : 0.0;
$createdAtRaw = $order['created_at'] ?? '';
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
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Dados incompletos: código=$codigo, email=$email, sku=$skuStr, createdAt=$createdAt\n", FILE_APPEND);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos ou inválidos.']);
    exit;
}

// Gravar no banco
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
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Pedido já existe: $codigo\n", FILE_APPEND);
    }
} catch (PDOException $e) {
    error_log('DB error webhook: ' . $e->getMessage());
    http_response_code(500);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erro DB: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco de dados.']);
    exit;
}

// Enviar e-mail apenas se pedido está com status 'paid'
$enviarEmail = ($status === 'paid');

file_put_contents($logFile, date('Y-m-d H:i:s') . " - Status pedido: $status\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Enviar email? " . ($enviarEmail ? 'SIM' : 'NÃO') . "\n", FILE_APPEND);

if ($enviarEmail) {
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/src/PHPMailer.php';
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/src/SMTP.php';
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

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
exit;
