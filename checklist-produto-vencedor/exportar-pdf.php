<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();
$user = getCurrentUser();

// Verificar se há dados para exportar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$resultado_id = $_GET['id'];

// Buscar resultado no banco
try {
    $stmt = $pdo->prepare("
        SELECT * FROM results 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$resultado_id, $user['id']]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado) {
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar resultado: " . $e->getMessage());
    header('Location: dashboard.php');
    exit();
}

// Gerar HTML para PDF
$html = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Análise de Produto - Checklist</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; }
        .score { font-size: 48px; font-weight: bold; color: #3b82f6; margin: 20px 0; }
        .message { font-size: 24px; margin: 20px 0; padding: 15px; border-radius: 8px; }
        .high { background-color: #dcfce7; color: #166534; }
        .medium { background-color: #fef3c7; color: #92400e; }
        .low { background-color: #fee2e2; color: #991b1b; }
        .section { margin: 30px 0; }
        .section h3 { color: #374151; border-bottom: 1px solid #d1d5db; padding-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
        .item { padding: 10px; border: 1px solid #e5e7eb; border-radius: 5px; }
        .checklist { margin: 20px 0; }
        .checklist-item { padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
        .checked { color: #059669; font-weight: bold; }
        .unchecked { color: #6b7280; }
        .footer { margin-top: 40px; text-align: center; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Checklist do Produto Lucrativo</h1>
        <p>Análise de Potencial de Produto</p>
        <div class="score">' . $resultado['nota_final'] . '/10</div>
    </div>
    
    <div class="message ' . ($resultado['nota_final'] >= 8 ? 'high' : ($resultado['nota_final'] >= 5 ? 'medium' : 'low')) . '">
        ' . $resultado['mensagem'] . '
    </div>
    
    <div class="section">
        <h3>Respostas da Análise</h3>
        <div class="grid">
            <div class="item">
                <strong>Promessa Principal:</strong><br>
                ' . htmlspecialchars($resultado['promessa_principal']) . '
            </div>
            <div class="item">
                <strong>Cliente Consciente:</strong><br>
                ' . htmlspecialchars($resultado['cliente_consciente']) . '
            </div>
            <div class="item">
                <strong>Benefícios:</strong><br>
                ' . htmlspecialchars($resultado['beneficios']) . '
            </div>
            <div class="item">
                <strong>Mecanismo Único:</strong><br>
                ' . htmlspecialchars($resultado['mecanismo_unico']) . '
            </div>
        </div>
    </div>
    
    <div class="section">
        <h3>Checklist de Critérios</h3>
        <div class="checklist">
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'vida_mais_facil') !== false ? 'checked' : 'unchecked') . '">
                ✓ Deixa a vida do cliente mais fácil
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'criativos_dinamicos') !== false ? 'checked' : 'unchecked') . '">
                ✓ Criativos são dinâmicos e de qualidade
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'buscas_google') !== false ? 'checked' : 'unchecked') . '">
                ✓ Possui buscas no Google
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'vendido_lojas') !== false ? 'checked' : 'unchecked') . '">
                ✓ Já está sendo vendido em lojas
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'economiza_dinheiro') !== false ? 'checked' : 'unchecked') . '">
                ✓ Economiza dinheiro
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'economiza_tempo') !== false ? 'checked' : 'unchecked') . '">
                ✓ Economiza tempo
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'nao_nicho_sensivel') !== false ? 'checked' : 'unchecked') . '">
                ✓ Não é nicho sensível
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'menos_50_dolares') !== false ? 'checked' : 'unchecked') . '">
                ✓ Custa menos de 50 dólares
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'so_internet') !== false ? 'checked' : 'unchecked') . '">
                ✓ Só encontra na internet
            </div>
            <div class="checklist-item ' . (strpos($resultado['checklist'], 'nao_commodity') !== false ? 'checked' : 'unchecked') . '">
                ✓ Produto não é commodity
            </div>
        </div>
    </div>
    
    <div class="section">
        <h3>Recomendações</h3>
        <p>Baseado na sua pontuação de ' . $resultado['nota_final'] . '/10, recomendamos:</p>
        <ul>
            <li>Focar nos critérios não atendidos</li>
            <li>Refinar o posicionamento do produto</li>
            <li>Testar diferentes abordagens de marketing</li>
        </ul>
    </div>
    
    <div class="footer">
        <p>Análise gerada em ' . date('d/m/Y H:i') . ' por ' . htmlspecialchars($user['name']) . '</p>
        <p>Checklist do Produto Lucrativo - Versão 2.0</p>
    </div>
</body>
</html>';

// Configurar headers para download
header('Content-Type: text/html');
header('Content-Disposition: inline; filename="analise-produto-' . $resultado_id . '.html"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

echo $html;
?> 