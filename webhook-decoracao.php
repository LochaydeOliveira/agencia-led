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

if ($evento !== 'order.created') {
    http_response_code(200);
    echo json_encode(['mensagem' => 'Evento ignorado: ' . $evento]);
    exit;
}

// ─────────────── EXTRAÇÃO DOS DADOS ───────────────
$email = $dados['customer']['email'] ?? '';
$sku = $dados['items'][0]['sku'] ?? '';
$produto = $dados['items'][0]['name'] ?? '';
$status = $dados['status']['name'] ?? '';
$pagamento = $dados['payment_method']['name'] ?? '';
$nome = $dados['customer']['name'] ?? '';
$cpf = $dados['customer']['document'] ?? '';
$telefone = $dados['customer']['phone'] ?? '';
$valor = $dados['total'] ?? 0;
$created_at = $dados['created_at'] ?? date('Y-m-d H:i:s');
$pedido_id = $dados['id'] ?? null;
$numero_pedido = $dados['number'] ?? null;

if (!$email || !$sku || !$pedido_id || !$numero_pedido) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados essenciais ausentes']);
    exit;
}

// ─────────────── INSERE PEDIDO NO BANCO ───────────────
try {
    $stmt = $pdo->prepare("INSERT INTO pedidos (
        email, sku, produto, status, pagamento, nome, cpf, telefone, valor, created_at, pedido_id, numero_pedido
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $email,
        $sku,
        $produto,
        $status,
        $pagamento,
        $nome,
        $cpf,
        $telefone,
        $valor,
        $created_at,
        $pedido_id,
        $numero_pedido
    ]);
} catch (Exception $e) {
    error_log('Erro insert: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao registrar pedido']);
    exit;
}

// ─────────────── RESPOSTA FINAL ───────────────
http_response_code(200);
echo json_encode(['status' => 'sucesso', 'mensagem' => 'Pedido registrado com sucesso.']);

?>