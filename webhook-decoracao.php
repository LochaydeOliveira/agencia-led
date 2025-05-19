<?php
/**
 * Webhook Yampi – versão com melhorias
 * Ambiente de teste usa o evento order.created; em produção só envia e‑mail se status === 'paid'.
 * Configure variáveis de ambiente em seu painel de hospedagem ou no .htaccess:
 *   SetEnv APP_ENV "prod"
 *   SetEnv YAMPI_SECRET "wh_xxx"
 *   SetEnv DB_PASS "<senha_mysql>"
 *   SetEnv SMTP_PASS "<senha_smtp>"
 */

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

define('ENV', getenv('APP_ENV') ?: 'test');       // 'test' | 'prod'
$YAMPI_SECRET = getenv('YAMPI_SECRET') ?: 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';

// Banco de dados - ideal passar também por variável de ambiente
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'paymen58_lista_decoracao';
$dbUser = getenv('DB_USER') ?: 'paymen58';
$dbPass = getenv('DB_PASS') ?: 'u4q7+B6ly)obP_gxN9sNe';
$dsn     = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

// SMTP
$SMTP_HOST = getenv('SMTP_HOST') ?: 'smtp.zoho.com';
$SMTP_PORT = getenv('SMTP_PORT') ?: 587;
$SMTP_USER = getenv('SMTP_USER') ?: 'contato@agencialed.com';
$SMTP_PASS = getenv('SMTP_PASS') ?: 'SENHA_EM_TESTE';

// Diretório de logs (fora da raiz pública, se possível)
$logFile = __DIR__ . '/../logs/yampi_webhook.log';

// ───────────────────────────────────────────────────────────
// 🚦 Valida método HTTP
// ───────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
    exit;
}

// ───────────────────────────────────────────────────────────
// 🔐 Validação da assinatura HMAC
// ───────────────────────────────────────────────────────────

$body               = file_get_contents('php://input');
$assinaturaRecebida  = $_SERVER['HTTP_X_YAMPI_SIGNATURE'] ?? '';
$assinaturaCalculada = hash_hmac('sha256', $body, $YAMPI_SECRET);

if (!hash_equals($assinaturaCalculada, $assinaturaRecebida)) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Assinatura inválida.']);
    exit;
}

// ───────────────────────────────────────────────────────────
// 📥 Decodifica payload
// ───────────────────────────────────────────────────────────

$payload = json_decode($body, true);

// Log só em ambiente de teste para evitar dados sensíveis em produção
if (ENV === 'test') {
    file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . print_r($payload, true) . "\n", FILE_APPEND);
}

if (!isset($payload['event']) || $payload['event'] !== 'order.created') {
    echo json_encode(['status' => 'ignorado', 'mensagem' => 'Evento não é order.created.']);
    exit;
}

// ───────────────────────────────────────────────────────────
// 🗃️ Extrai campos principais e valida
// ───────────────────────────────────────────────────────────

$order     = $payload['data'] ?? [];
$codigo    = trim($order['reference'] ?? '');
$email     = filter_var($order['customer']['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
$status    = $order['status']['alias'] ?? '';
$valor     = (float)($order['value_total'] ?? 0);
$createdAtRaw = $order['created_at'] ?? null;
$pagamento = $order['payments'][0]['method'] ?? '';
$skus      = array_column($order['products'] ?? [], 'sku');
$skuStr    = implode(',', array_map('trim', $skus));

// Valida data criada no formato ISO 8601 e converte para MySQL
$createdAt = null;
if ($createdAtRaw) {
    $dt = DateTime::createFromFormat(DateTime::ATOM, $createdAtRaw);
    if ($dt !== false) {
        $createdAt = $dt->format('Y-m-d H:i:s');
    }
}

if (!$codigo || !$email || !$skuStr || !$createdAt) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos ou inválidos.']);
    exit;
}

// ───────────────────────────────────────────────────────────
// 🏦 Conexão PDO & inserção / duplicidade
// ───────────────────────────────────────────────────────────

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Já existe? Se não, insere
    $chk = $pdo->prepare("SELECT id FROM pedidos WHERE codigo = ? LIMIT 1");
    $chk->execute([$codigo]);

    if ($chk->rowCount() === 0) {
        $ins = $pdo->prepare("INSERT INTO pedidos
            (codigo, email, sku, usado, criacao, status, valor_total, created_at, pagamento)
            VALUES
            (?, ?, ?, 0, NOW(), ?, ?, ?, ?)");
        $ins->execute([$codigo, $email, $skuStr, $status, $valor, $createdAt, $pagamento]);
    }

} catch (PDOException $e) {
    error_log('DB error webhook: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco de dados.']);
    exit;
}

// ───────────────────────────────────────────────────────────
// ✉️ Envio de e‑mail (apenas se permitido)
// ───────────────────────────────────────────────────────────

$enviarEmail = (ENV === 'test') || ($status === 'paid');

if ($enviarEmail) {
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/PHPMailer.php';
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/SMTP.php';
    require_once '/home1/paymen58/agencialed.com/email/PHPMailer/Exception.php';

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
        $mail->Body    = "<h2>Obrigado pela sua compra!</h2>
            <p>Seu código de pedido é: <strong>{$codigo}</strong></p>
            <p>Para baixar sua lista, clique no botão abaixo e informe o código quando solicitado:</p>
            <p><a href='https://agencialed.com/fornecedores-decoracao.php' target='_blank' style='padding: 10px 20px; background-color: #38b97e; color: white; text-decoration: none; border-radius: 5px;'>Acessar PDF da Lista</a></p>
            <p><em>Guarde seu código com segurança. Ele só poderá ser usado uma vez.</em></p>";

        $mail->send();

        // Opcional: marcar email_enviado=1 na tabela (se criar a coluna)
        // $pdo->prepare("UPDATE pedidos SET email_enviado = 1 WHERE codigo = ?")->execute([$codigo]);

    } catch (Exception $e) {
        error_log('Mailer error webhook: ' . $e->getMessage());
    }
}

http_response_code(200);
echo json_encode(['status' => 'sucesso', 'mensagem' => 'Processado com sucesso.']);
