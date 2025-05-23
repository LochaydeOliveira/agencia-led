<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['usuario'];
$nome = htmlspecialchars($_SESSION['nome']);

// Buscar cliente
$stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ? AND status = 'ativo'");
$stmt->execute([$email]);
$cliente = $stmt->fetch();

$listas_com_acesso = [];
$todas_listas = [];

if ($cliente) {
    $cliente_id = $cliente['id'];

    // Buscar IDs das listas liberadas para esse cliente
    $stmt = $pdo->prepare("SELECT lista_id FROM clientes_listas WHERE cliente_id = ? AND status = 'ativo'");
    $stmt->execute([$cliente_id]);
    $listas_com_acesso = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Buscar todas as listas do sistema
    $stmt = $pdo->query("SELECT id, nome, descricao, conteudo_html FROM listas");
    $todas_listas = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Cliente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .card-title { padding: 1rem; background: #007bff; color: white; margin-bottom: 0; }
        .bloqueado { position: relative; background: #f9f9f9; }
        .bloqueio-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.9);
            color: #dc3545;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 1.1em;
            text-align: center;
            padding: 1rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="mb-4">Bem-vindo, <?php echo $nome; ?>!</h2>

    <div class="row">
        <?php foreach ($todas_listas as $lista): ?>
            <?php $liberado = in_array($lista['id'], $listas_com_acesso); ?>
            <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="<?php echo htmlspecialchars($lista['nome']); ?>" data-lista-id="<?php echo $lista['id']; ?>">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title"><?php echo htmlspecialchars($lista['nome']); ?></h5>
                    <div class="card-body <?php echo $liberado ? '' : 'bloqueado'; ?>">
                        <div class="conteudo-lista">
                            <?php echo $liberado ? $lista['conteudo_html'] : ''; ?>
                        </div>

                        <?php if (!$liberado): ?>
                            <div class="bloqueio-overlay">
                                🚫 <strong>Lista bloqueada!</strong><br>
                                Libere agora mesmo realizando o pagamento via Pix.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
