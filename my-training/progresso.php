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

// Buscar dados de progresso dos últimos 30 dias
$stmt = $pdo->prepare("SELECT * FROM progresso WHERE usuario_id = ? ORDER BY data DESC LIMIT 30");
$stmt->execute([$user_id]);
$progresso = $stmt->fetchAll();

// Buscar dados de checklist dos últimos 7 dias
$stmt = $pdo->prepare("SELECT * FROM checklist WHERE usuario_id = ? AND data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY data DESC");
$stmt->execute([$user_id]);
$checklist_recente = $stmt->fetchAll();

// Calcular estatísticas
$total_dias = count($checklist_recente);
$dias_completos = 0;
$treinos_completos = 0;
$refeicoes_completas = 0;

foreach ($checklist_recente as $check) {
    $treinos_feitos = json_decode($check['treino_feito'], true) ?: [];
    $refeicoes_realizadas = json_decode($check['refeicoes_realizadas'], true) ?: [];
    
    if (!empty($treinos_feitos) || !empty($refeicoes_realizadas)) {
        $dias_completos++;
    }
    
    $treinos_completos += count($treinos_feitos);
    $refeicoes_completas += count($refeicoes_realizadas);
}

// Processar formulário de adicionar progresso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'adicionar') {
        $data = $_POST['data'];
        $peso = !empty($_POST['peso']) ? (float)$_POST['peso'] : null;
        $altura = !empty($_POST['altura']) ? (float)$_POST['altura'] : null;
        $braco_esquerdo = !empty($_POST['braco_esquerdo']) ? (float)$_POST['braco_esquerdo'] : null;
        $braco_direito = !empty($_POST['braco_direito']) ? (float)$_POST['braco_direito'] : null;
        $cintura = !empty($_POST['cintura']) ? (float)$_POST['cintura'] : null;
        $quadril = !empty($_POST['quadril']) ? (float)$_POST['quadril'] : null;
        $coxa_esquerda = !empty($_POST['coxa_esquerda']) ? (float)$_POST['coxa_esquerda'] : null;
        $coxa_direita = !empty($_POST['coxa_direita']) ? (float)$_POST['coxa_direita'] : null;
        $observacoes = sanitize($_POST['observacoes']);
        
        // Verificar se já existe registro para esta data
        $stmt = $pdo->prepare("SELECT id FROM progresso WHERE usuario_id = ? AND data = ?");
        $stmt->execute([$user_id, $data]);
        
        if ($stmt->fetch()) {
            // Atualizar registro existente
            $stmt = $pdo->prepare("UPDATE progresso SET peso = ?, altura = ?, braco_esquerdo = ?, braco_direito = ?, cintura = ?, quadril = ?, coxa_esquerda = ?, coxa_direita = ?, observacoes = ? WHERE usuario_id = ? AND data = ?");
            $stmt->execute([$peso, $altura, $braco_esquerdo, $braco_direito, $cintura, $quadril, $coxa_esquerda, $coxa_direita, $observacoes, $user_id, $data]);
        } else {
            // Criar novo registro
            $stmt = $pdo->prepare("INSERT INTO progresso (usuario_id, data, peso, altura, braco_esquerdo, braco_direito, cintura, quadril, coxa_esquerda, coxa_direita, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $data, $peso, $altura, $braco_esquerdo, $braco_direito, $cintura, $quadril, $coxa_esquerda, $coxa_direita, $observacoes]);
        }
        
        header('Location: progresso.php?success=1');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Progresso - My Training</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <h1 class="text-xl font-bold">Meu Progresso</h1>
                        <p class="text-sm opacity-90">Acompanhe sua evolução</p>
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
                Progresso registrado com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo -->
    <div class="container mx-auto px-4 py-6">
        <!-- Estatísticas Gerais -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-purple-500 mr-2"></i>
                Estatísticas dos Últimos 7 Dias
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600"><?php echo $dias_completos; ?>/<?php echo $total_dias; ?></div>
                    <div class="text-sm text-gray-600">Dias Ativos</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600"><?php echo $treinos_completos; ?></div>
                    <div class="text-sm text-gray-600">Treinos Realizados</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600"><?php echo $refeicoes_completas; ?></div>
                    <div class="text-sm text-gray-600">Refeições Realizadas</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600"><?php echo $total_dias > 0 ? round(($dias_completos / $total_dias) * 100) : 0; ?>%</div>
                    <div class="text-sm text-gray-600">Consistência</div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Peso -->
        <?php if (!empty($progresso)): ?>
            <div class="bg-white rounded-lg card-shadow p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-weight text-red-500 mr-2"></i>
                    Evolução do Peso
                </h3>
                <canvas id="pesoChart" width="400" height="200"></canvas>
            </div>
        <?php endif; ?>

        <!-- Histórico de Progresso -->
        <div class="bg-white rounded-lg card-shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-history text-gray-500 mr-2"></i>
                Histórico de Medidas
            </h3>
            
            <?php if (empty($progresso)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Nenhum registro de progresso</h3>
                    <p class="text-gray-500 mb-6">Comece registrando suas medidas para acompanhar sua evolução</p>
                    <button onclick="abrirModalAdicionar()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold">
                        <i class="fas fa-plus mr-2"></i>Registrar Primeira Medida
                    </button>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-2">Data</th>
                                <th class="text-left py-3 px-2">Peso</th>
                                <th class="text-left py-3 px-2">Altura</th>
                                <th class="text-left py-3 px-2">Braços</th>
                                <th class="text-left py-3 px-2">Cintura</th>
                                <th class="text-left py-3 px-2">Quadril</th>
                                <th class="text-left py-3 px-2">Coxas</th>
                                <th class="text-left py-3 px-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($progresso as $registro): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-2"><?php echo date('d/m/Y', strtotime($registro['data'])); ?></td>
                                    <td class="py-3 px-2"><?php echo $registro['peso'] ? $registro['peso'] . 'kg' : '-'; ?></td>
                                    <td class="py-3 px-2"><?php echo $registro['altura'] ? $registro['altura'] . 'm' : '-'; ?></td>
                                    <td class="py-3 px-2">
                                        <?php 
                                        if ($registro['braco_esquerdo'] && $registro['braco_direito']) {
                                            echo $registro['braco_esquerdo'] . '/' . $registro['braco_direito'] . 'cm';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="py-3 px-2"><?php echo $registro['cintura'] ? $registro['cintura'] . 'cm' : '-'; ?></td>
                                    <td class="py-3 px-2"><?php echo $registro['quadril'] ? $registro['quadril'] . 'cm' : '-'; ?></td>
                                    <td class="py-3 px-2">
                                        <?php 
                                        if ($registro['coxa_esquerda'] && $registro['coxa_direita']) {
                                            echo $registro['coxa_esquerda'] . '/' . $registro['coxa_direita'] . 'cm';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="py-3 px-2">
                                        <button onclick="editarProgresso(<?php echo $registro['id']; ?>)" class="text-blue-600 hover:text-blue-800 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Adicionar Progresso -->
    <div id="modalAdicionar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-bold text-gray-800">Registrar Progresso</h3>
                    <button onclick="fecharModalAdicionar()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="acao" value="adicionar">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                        <input type="date" name="data" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Peso (kg)</label>
                            <input type="number" name="peso" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 75.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Altura (m)</label>
                            <input type="number" name="altura" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 1.75">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Braço Esquerdo (cm)</label>
                            <input type="number" name="braco_esquerdo" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 32.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Braço Direito (cm)</label>
                            <input type="number" name="braco_direito" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 32.5">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cintura (cm)</label>
                            <input type="number" name="cintura" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 85.0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quadril (cm)</label>
                            <input type="number" name="quadril" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 95.0">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coxa Esquerda (cm)</label>
                            <input type="number" name="coxa_esquerda" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 55.0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coxa Direita (cm)</label>
                            <input type="number" name="coxa_direita" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Ex: 55.0">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea name="observacoes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Como você está se sentindo, mudanças percebidas..."></textarea>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="fecharModalAdicionar()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Salvar
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
        
        function editarProgresso(progressoId) {
            // Implementar edição futuramente
            alert('Funcionalidade de edição será implementada em breve!');
        }
        
        // Fechar modal ao clicar fora
        document.getElementById('modalAdicionar').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalAdicionar();
            }
        });

        // Gráfico de peso
        <?php if (!empty($progresso)): ?>
        const ctx = document.getElementById('pesoChart').getContext('2d');
        const pesoData = <?php echo json_encode(array_reverse(array_map(function($p) { 
            return ['data' => date('d/m', strtotime($p['data'])), 'peso' => $p['peso']]; 
        }, array_filter($progresso, function($p) { return $p['peso'] !== null; })))); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: pesoData.map(item => item.data),
                datasets: [{
                    label: 'Peso (kg)',
                    data: pesoData.map(item => item.peso),
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html> 