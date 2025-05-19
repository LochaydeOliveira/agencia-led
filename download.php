<?php
declare(strict_types=1);

ini_set('display_errors', '0'); // Não mostrar erros no frontend
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

// Configurações do banco (use mesmas variáveis do webhook para consistência)
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'paymen58_lista_decoracao';
$dbUser = getenv('DB_USER') ?: 'paymen58';
$dbPass = getenv('DB_PASS') ?: 'u4q7+B6ly)obP_gxN9sNe';
$dsn     = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

// Caminho do arquivo PDF a ser entregue (melhor fora da raiz pública)
$pdfPath = __DIR__ . '/arquivos/lista-fornecedores.pdf';

// Parâmetros para controle de validade
$validadeDias = 30; // Exemplo: 30 dias para uso do código após criação
$maxUsos      = 1;  // Só permite 1 uso

// Função para mostrar erro amigável e encerrar
function erro(string $msg): void {
    echo "<h2>Ops!</h2><p>{$msg}</p><p><a href='https://agencialed.com/'>Voltar ao site</a></p>";
    exit;
}

// Verifica se código foi passado via GET
$codigo = trim($_GET['codigo'] ?? '');
if (!$codigo) {
    erro('Código não informado.');
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Busca o pedido pelo código
    $stmt = $pdo->prepare("SELECT id, usado, criacao FROM pedidos WHERE codigo = ? LIMIT 1");
    $stmt->execute([$codigo]);
    $pedido = $stmt->fetch();

    if (!$pedido) {
        erro('Código inválido ou não encontrado.');
    }

    // Verifica se já foi usado
    if ((int)$pedido['usado'] >= $maxUsos) {
        erro('Este código já foi usado.');
    }

    // Verifica validade da data
    $criacao = DateTime::createFromFormat('Y-m-d H:i:s', $pedido['criacao']);
    if (!$criacao) {
        erro('Data inválida no sistema. Contate o suporte.');
    }
    $agora = new DateTime();
    $intervalo = $agora->diff($criacao);
    if ($intervalo->days > $validadeDias) {
        erro('O prazo para uso deste código expirou.');
    }

    // Tudo OK, atualiza como usado (incrementa 1)
    $update = $pdo->prepare("UPDATE pedidos SET usado = usado + 1 WHERE id = ?");
    $update->execute([$pedido['id']]);

    // Entrega o arquivo PDF
    if (!file_exists($pdfPath)) {
        erro('Arquivo não encontrado. Contate o suporte.');
    }

    // Envia headers para download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="lista-fornecedores.pdf"');
    header('Content-Length: ' . filesize($pdfPath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($pdfPath);
    exit;

} catch (PDOException $e) {
    erro('Erro no servidor. Tente novamente mais tarde.');
}
