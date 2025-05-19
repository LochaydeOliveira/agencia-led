<?php

// Salvar o conteúdo bruto da requisição para log
file_put_contents('log_webhook_yampi.txt', file_get_contents('php://input'));

// ─────────────── CONFIGURAÇÕES ───────────────
$webhookSecret = 'wh_rweQPzt0jQ5lRY3ZbrNYZQFFdjc8ZjDWOguYm';
$logFile = __DIR__ . '/webhook-log.txt';

header('Content-Type: application/json');

// ─────────────── MÉTODO POST OBRIGATÓRIO ───────────────
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

// ─────────────── DECODIFICA O CORPO JSON ───────────────
$dados = json_decode($bodyRaw, true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(['erro' => 'JSON inválido']);
    exit;
}

// ─────────────── CONEXÃO COM O BANCO ───────────────
try {
    $pdo = new PDO('mysql:host=localhost;dbname=paymen58_lista_decoracao;charset=utf8mb4', 'paymen58', 'u4q7+B6ly)obP_gxN9sNe', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log('Erro DB: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de conexão com o banco']);
    exit;
}

// ─────────────── TRATAMENTO DO EVENTO ───────────────
$evento = $_SERVER['HTTP_X_YAMPI_EVENT'] ?? '';

if ($evento !== 'order.paid') {
    http_response_code(200);
    echo json_encode(['mensagem' => 'Evento ignorado: ' . $evento]);
    exit;
}

// ─────────────── EXTRAÇÃO DOS DADOS ───────────────
$numero = $dados['number'] ?? null;
$cliente = $dados['customer']['name'] ?? null;
$email = $dados['customer']['email'] ?? null;
$telefone = $dados['customer']['phone'] ?? null;
$sku = $dados['items'][0]['sku'] ?? null;

if (!$numero || !$email || !$sku) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos']);
    exit;
}

// ─────────────── INSERE PEDIDO NO BANCO ───────────────
try {
    $stmt = $pdo->prepare("INSERT INTO pedidos (numero, usado, sku, email, cliente, telefone, email_enviado) VALUES (?, 0, ?, ?, ?, ?, 0)");
    $stmt->execute([$numero, $sku, $email, $cliente, $telefone]);
} catch (Exception $e) {
    error_log('Erro insert: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao registrar pedido']);
    exit;
}

// ─────────────── ENVIO DE E-MAIL COM O NÚMERO ───────────────
$assunto = 'Seu código de acesso à lista de fornecedores de decoração';
$mensagem = "Olá $cliente,\n\nSeu pedido foi aprovado com sucesso!\n\nAqui está seu código de acesso: $numero\n\nUse esse código em:\nhttps://agencialed.com/fornecedores-decoracao.php\n\nAtenciosamente,\nEquipe Agência LED";

$headers = 'From: contato@agencialed.com' . "\r\n" .
           'Reply-To: contato@agencialed.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($email, $assunto, $mensagem, $headers)) {
    // Atualiza status de e-mail enviado
    $pdo->prepare("UPDATE pedidos SET email_enviado = 1 WHERE numero = ?")->execute([$numero]);
}

// ─────────────── RESPOSTA FINAL ───────────────
http_response_code(200);
echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido processado com sucesso.']);
?>
