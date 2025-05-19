<?php

file_put_contents(__DIR__ . '/log_webhook_yampi.txt', date('Y-m-d H:i:s') . ' - Webhook recebido: ' . file_get_contents('php://input') . "\n", FILE_APPEND);

// ─────────────── CONFIGURAÇÕES ───────────────
$webhookSecret = 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';
require '/home1/paymen58/agencialed.com/email/PHPMailer/src/PHPMailer.php';
require '/home1/paymen58/agencialed.com/email/PHPMailer/src/SMTP.php';
require '/home1/paymen58/agencialed.com/email/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// ─────────────── VALIDAÇÃO DO MÉTODO ───────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

// ─────────────── CAPTURA E VERIFICA ASSINATURA ───────────────
$bodyRaw = file_get_contents('php://input');
$assinaturaRecebida = $_SERVER['HTTP_X_YAMPI_HMAC_SHA256'] ?? '';
$assinaturaCalculada = base64_encode(hash_hmac('sha256', $bodyRaw, $webhookSecret, true));

if (!hash_equals($assinaturaCalculada, $assinaturaRecebida)) {
    http_response_code(401);
    echo json_encode(['erro' => 'Assinatura inválida']);
    exit;
}

// ─────────────── DECODIFICA O JSON ───────────────
$dados = json_decode($bodyRaw, true);
if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(['erro' => 'JSON inválido']);
    exit;
}

// ─────────────── VERIFICA SE O EVENTO É order.created ───────────────
$evento = $dados['event'] ?? '';
if ($evento !== 'order.created') {
    http_response_code(200);
    echo json_encode(['status' => 'ignorado', 'mensagem' => 'Evento não processado']);
    exit;
}

// ─────────────── CONEXÃO COM BANCO ───────────────
try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    error_log('Erro DB: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de conexão com o banco']);
    exit;
}

// ─────────────── EXTRAI OS DADOS DO PEDIDO ───────────────
$numero_pedido = $dados['number'] ?? '';
$email = $dados['customer']['email'] ?? '';
$nome = $dados['customer']['name'] ?? '';
$telefone = $dados['customer']['phone'] ?? '';
$cpf = $dados['cpf'] ?? '';
$sku = $dados['items'][0]['sku'] ?? '';
$produto = $dados['items'][0]['product'] ?? '';
$pagamento = $dados['payment_method'] ?? '';
$status = $dados['status'] ?? '';
$valor = $dados['value'] ?? 0;
$created_at = $dados['created_at'] ?? date('Y-m-d H:i:s');
$pedido_id = $dados['id'] ?? null;

if (!$email || !$numero_pedido || !$sku || !$pedido_id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos']);
    exit;
}

// ─────────────── INSERE NO BANCO ───────────────
try {
    $stmt = $pdo->prepare("INSERT INTO pedidos (
        email, sku, produto, status, pagamento, nome, cpf, telefone, valor, created_at, pedido_id, numero_pedido, usado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");

    $stmt->execute([
        $email, $sku, $produto, $status, $pagamento, $nome, $cpf, $telefone, $valor, $created_at, $pedido_id, $numero_pedido
    ]);
} catch (Exception $e) {
    error_log('Erro insert: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao salvar pedido']);
    exit;
}

// ─────────────── ENVIA O E-MAIL ───────────────
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.zoho.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'contato@agencialed.com';
    $mail->Password = 'Lochayde@154719';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('contato@agencialed.com', 'Agência LED');
    $mail->addAddress($email, $nome);
    $mail->Subject = 'Seu pedido foi confirmado!';
    $mail->Body = "Olá $nome,\n\nRecebemos seu pedido com sucesso!\n\nNúmero do pedido: $numero_pedido\n\nUse esse número para baixar seu material no link:\nhttps://agencialed.com/fornecedores-decoracao.php\n\nAgradecemos sua compra!";

    $mail->send();
} catch (Exception $e) {
    error_log('Erro ao enviar email: ' . $mail->ErrorInfo);
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao enviar e-mail']);
    exit;
}

http_response_code(200);
echo json_encode(['status' => 'ok', 'mensagem' => 'Pedido salvo e e-mail enviado com sucesso.']);
