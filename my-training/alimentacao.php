<?php
session_start();
require_once 'config/database.php';

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar refeições organizadas por horário
$stmt = $pdo->prepare("SELECT * FROM alimentacao WHERE usuario_id = ? AND ativo = 1 ORDER BY horario");
$stmt->execute([$user_id]);
$refeicoes = $stmt->fetchAll();

// Agrupar refeições por horário
$refeicoes_por_horario = [];
foreach ($refeicoes as $refeicao) {
    $horario = $refeicao['horario'];
    if (!isset($refeicoes_por_horario[$horario])) {
        $refeicoes_por_horario[$horario] = [];
    }
    $refeicoes_por_horario[$horario][] = $refeicao;
}

// Processar formulário de adicionar refeição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'adicionar') {
        $horario = $_POST['horario'];
        $alimento = sanitize($_POST['alimento']);
        $quantidade_gramas = (float)$_POST['quantidade_gramas'];
        $calorias = !empty($_POST['calorias']) ? (float)$_POST['calorias'] : null;
        $proteinas = !empty($_POST['proteinas']) ? (float)$_POST['proteinas'] : null;
        $carboidratos = !empty($_POST['carboidratos']) ? (float)$_POST['carboidratos'] : null;
        $gorduras = !empty($_POST['gorduras']) ? (float)$_POST['gorduras'] : null;
        $observacoes = sanitize($_POST['observacoes']);
        
        $stmt = $pdo->prepare("INSERT INTO alimentacao (usuario_id, horario, alimento, quantidade_gramas, calorias, proteinas, carboidratos, gorduras, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $horario, $alimento, $quantidade_gramas, $calorias, $proteinas, $carboidratos, $gorduras, $observacoes]);
        
        header('Location: alimentacao.php?success=1');
        exit();
    } elseif ($_POST['acao'] === 'excluir' && isset($_POST['refeicao_id'])) {
        $refeicao_id = (int)$_POST['refeicao_id'];
        $stmt = $pdo->prepare("UPDATE alimentacao SET ativo = 0 WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$refeicao_id, $user_id]);
        
        header('Location: alimentacao.php?deleted=1');
        exit();
    }
}

// Calcular totais nutricionais
$totais = [
    'calorias' => 0,
    'proteinas' => 0,
    'carboidratos' => 0,
    'gorduras' => 0
];

foreach ($refeicoes as $refeicao) {
    if ($refeicao['calorias']) $totais['calorias'] += $refeicao['calorias'];
    if ($refeicao['proteinas']) $totais['proteinas'] += $refeicao['proteinas'];
    if ($refeicao['carboidratos']) $totais['carboidratos'] += $refeicao['carboidratos'];
    if ($refeicao['gorduras']) $totais['gorduras'] += $refeicao['gorduras'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano Alimentar - My Training</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="index.php" class="mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Plano Alimentar</h1>
                        <p class="text-sm opacity-90">Gerencie suas refeições</p>
                    </div>
                </div>
                <button onclick="abrirModalAdicionar()" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Adicionar
                </button>
            </div>
        </div>
    </header>

    <!-- Mensagens -->
    <?php if (isset($_GET['success'])): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Refeição adicionada com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i>
                Refeição removida com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <!-- Resumo Nutricional -->
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                Resumo Nutricional Diário
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600"><?php echo round($totais['calorias']); ?></div>
                    <div class="text-sm text-gray-600">Calorias</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600"><?php echo round($totais['proteinas'], 1); ?>g</div>
                    <div class="text-sm text-gray-600">Proteínas</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo round($totais['carboidratos'], 1); ?>g</div>
                    <div class="text-sm text-gray-600">Carboidratos</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600"><?php echo round($totais['gorduras'], 1); ?>g</div>
                    <div class="text-sm text-gray-600">Gorduras</div>
                </div>
            </div>
        </div>

        <!-- Refeições -->
        <?php if (empty($refeicoes_por_horario)): ?>
            <div class="bg-white rounded-lg card-shadow p-8 text-center">
                <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Nenhuma refeição cadastrada</h3>
                <p class="text-gray-500 mb-6">Comece adicionando suas refeições para criar seu plano alimentar</p>
                <button onclick="abrirModalAdicionar()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                    <i class="fas fa-plus mr-2"></i>Adicionar Primeira Refeição
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($refeicoes_por_horario as $horario => $refeicoes_horario): ?>
                <div class="bg-white rounded-lg card-shadow p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-clock text-green-500 mr-2"></i>
                            <?php echo date('H:i', strtotime($horario)); ?>
                        </h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?php echo count($refeicoes_horario); ?> item(s)
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <?php foreach ($refeicoes_horario as $refeicao): ?>
                            <div class="border rounded-lg p-4 bg-gray-50 border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($refeicao['alimento']); ?></h4>
                                        <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-600">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                                <?php echo $refeicao['quantidade_gramas']; ?>g
                                            </span>
                                            <?php if ($refeicao['calorias']): ?>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">
                                                    <?php echo round($refeicao['calorias']); ?> kcal
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($refeicao['proteinas']): ?>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                    P: <?php echo $refeicao['proteinas']; ?>g
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($refeicao['carboidratos']): ?>
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
                                                    C: <?php echo $refeicao['carboidratos']; ?>g
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($refeicao['gorduras']): ?>
                                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                                    G: <?php echo $refeicao['gorduras']; ?>g
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($refeicao['observacoes']): ?>
                                            <p class="text-sm text-gray-500 mt-2 italic">
                                                "<?php echo htmlspecialchars($refeicao['observacoes']); ?>"
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4 flex flex-col space-y-2">
                                        <button onclick="editarRefeicao(<?php echo $refeicao['id']; ?>)" class="p-2 text-blue-600 hover:bg-blue-100 rounded">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="excluirRefeicao(<?php echo $refeicao['id']; ?>)" class="p-2 text-red-600 hover:bg-red-100 rounded">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal Adicionar Refeição -->
    <div id="modalAdicionar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-bold text-gray-800">Adicionar Refeição</h3>
                    <button onclick="fecharModalAdicionar()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="acao" value="adicionar">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horário</label>
                        <input type="time" name="horario" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alimento</label>
                        <input type="text" name="alimento" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Ex: Frango Grelhado">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade (gramas)</label>
                        <input type="number" name="quantidade_gramas" required min="1" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="200">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Calorias (kcal)</label>
                            <input type="number" name="calorias" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Opcional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Proteínas (g)</label>
                            <input type="number" name="proteinas" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Opcional">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Carboidratos (g)</label>
                            <input type="number" name="carboidratos" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Opcional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gorduras (g)</label>
                            <input type="number" name="gorduras" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Opcional">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea name="observacoes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Dicas, observações..."></textarea>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="fecharModalAdicionar()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalAdicionar() {
            document.getElementById('modalAdicionar').classList.remove('hidden');
        }
        
        function fecharModalAdicionar() {
            document.getElementById('modalAdicionar').classList.add('hidden');
        }
        
        function editarRefeicao(refeicaoId) {
            // Implementar edição futuramente
            alert('Funcionalidade de edição será implementada em breve!');
        }
        
        function excluirRefeicao(refeicaoId) {
            if (confirm('Tem certeza que deseja excluir esta refeição?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="refeicao_id" value="${refeicaoId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('modalAdicionar').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalAdicionar();
            }
        });
    </script>
</body>
</html> 