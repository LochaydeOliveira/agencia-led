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

// Buscar dados do dia atual
$hoje = date('Y-m-d');
$dia_semana = date('w'); // 0 = Domingo, 1 = Segunda, etc.

// Buscar treinos do dia
$stmt = $pdo->prepare("SELECT * FROM treinos WHERE usuario_id = ? AND dia_semana = ? ORDER BY ordem");
$stmt->execute([$user_id, $dia_semana]);
$treinos_hoje = $stmt->fetchAll();

// Buscar refeições do dia
$stmt = $pdo->prepare("SELECT * FROM alimentacao WHERE usuario_id = ? ORDER BY horario");
$stmt->execute([$user_id]);
$refeicoes = $stmt->fetchAll();

// Verificar o que já foi feito hoje
$stmt = $pdo->prepare("SELECT * FROM checklist WHERE usuario_id = ? AND data = ?");
$stmt->execute([$user_id, $hoje]);
$checklist_hoje = $stmt->fetch();

$treinos_feitos = $checklist_hoje ? json_decode($checklist_hoje['treino_feito'], true) : [];
$refeicoes_realizadas = $checklist_hoje ? json_decode($checklist_hoje['refeicoes_realizadas'], true) : [];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Training - Seu Plano de Treino e Alimentação</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#3B82F6">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">My Training</h1>
                    <p class="text-sm opacity-90">Olá, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="toggleTheme()" class="p-2 rounded-full bg-white bg-opacity-20">
                        <i class="fas fa-moon"></i>
                    </button>
                    <a href="logout.php" class="p-2 rounded-full bg-white bg-opacity-20">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Data e Progresso -->
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <?php echo date('d/m/Y'); ?>
                    </h2>
                    <p class="text-gray-600">
                        <?php 
                        $dias = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                        echo $dias[$dia_semana];
                        ?>
                    </p>
                </div>
                <div class="relative">
                    <svg class="w-16 h-16 progress-ring">
                        <circle
                            class="progress-ring-circle"
                            stroke="#e5e7eb"
                            stroke-width="4"
                            fill="transparent"
                            r="26"
                            cx="32"
                            cy="32"
                        />
                        <circle
                            class="progress-ring-circle"
                            stroke="#3B82F6"
                            stroke-width="4"
                            fill="transparent"
                            r="26"
                            cx="32"
                            cy="32"
                            stroke-dasharray="163.36281798666926"
                            stroke-dashoffset="<?php 
                                $total = count($treinos_hoje) + count($refeicoes);
                                $feitos = count(array_filter($treinos_feitos)) + count(array_filter($refeicoes_realizadas));
                                $progresso = $total > 0 ? (($total - $feitos) / $total) * 163.36281798666926 : 163.36281798666926;
                                echo $progresso;
                            ?>"
                        />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold text-gray-700">
                            <?php 
                            $total = count($treinos_hoje) + count($refeicoes);
                            $feitos = count(array_filter($treinos_feitos)) + count(array_filter($refeicoes_realizadas));
                            echo $total > 0 ? round(($feitos / $total) * 100) : 0;
                            ?>%
                        </span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="text-2xl font-bold text-blue-600">
                        <?php echo count(array_filter($treinos_feitos)); ?>/<?php echo count($treinos_hoje); ?>
                    </div>
                    <div class="text-sm text-gray-600">Treinos</div>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <div class="text-2xl font-bold text-green-600">
                        <?php echo count(array_filter($refeicoes_realizadas)); ?>/<?php echo count($refeicoes); ?>
                    </div>
                    <div class="text-sm text-gray-600">Refeições</div>
                </div>
            </div>
        </div>

        <!-- Treinos do Dia -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-dumbbell text-blue-500 mr-2"></i>
                    Treinos de Hoje
                </h3>
                <a href="treinos.php" class="text-blue-500 text-sm">Ver todos</a>
            </div>
            
            <?php if (empty($treinos_hoje)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>Nenhum treino programado para hoje</p>
                    <a href="treinos.php" class="text-blue-500 mt-2 inline-block">Adicionar treino</a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($treinos_hoje as $treino): ?>
                        <div class="border rounded-lg p-4 <?php echo isset($treinos_feitos[$treino['id']]) && $treinos_feitos[$treino['id']] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'; ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($treino['exercicio']); ?></h4>
                                    <p class="text-sm text-gray-600">
                                        <?php echo $treino['series']; ?> séries x <?php echo $treino['repeticoes']; ?> reps
                                        <?php if ($treino['carga_sugerida']): ?>
                                            • <?php echo $treino['carga_sugerida']; ?>kg
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <button 
                                    onclick="toggleTreino(<?php echo $treino['id']; ?>)"
                                    class="ml-3 p-2 rounded-full <?php echo isset($treinos_feitos[$treino['id']]) && $treinos_feitos[$treino['id']] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600'; ?>"
                                >
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Refeições do Dia -->
        <div class="bg-white rounded-lg card-shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-utensils text-green-500 mr-2"></i>
                    Plano Alimentar
                </h3>
                <a href="alimentacao.php" class="text-green-500 text-sm">Ver todos</a>
            </div>
            
            <?php if (empty($refeicoes)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-apple-alt text-4xl mb-3"></i>
                    <p>Nenhuma refeição programada</p>
                    <a href="alimentacao.php" class="text-green-500 mt-2 inline-block">Adicionar refeição</a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($refeicoes as $refeicao): ?>
                        <div class="border rounded-lg p-4 <?php echo isset($refeicoes_realizadas[$refeicao['id']]) && $refeicoes_realizadas[$refeicao['id']] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'; ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($refeicao['alimento']); ?></h4>
                                    <p class="text-sm text-gray-600">
                                        <?php echo $refeicao['horario']; ?> • <?php echo $refeicao['quantidade_gramas']; ?>g
                                    </p>
                                </div>
                                <button 
                                    onclick="toggleRefeicao(<?php echo $refeicao['id']; ?>)"
                                    class="ml-3 p-2 rounded-full <?php echo isset($refeicoes_realizadas[$refeicao['id']]) && $refeicoes_realizadas[$refeicao['id']] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600'; ?>"
                                >
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Menu de Navegação -->
        <div class="bg-white rounded-lg card-shadow p-4">
            <div class="grid grid-cols-4 gap-4">
                <a href="treinos.php" class="text-center p-3 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                    <i class="fas fa-dumbbell text-blue-500 text-xl mb-2"></i>
                    <div class="text-xs text-gray-700">Treinos</div>
                </a>
                <a href="alimentacao.php" class="text-center p-3 rounded-lg bg-green-50 hover:bg-green-100 transition-colors">
                    <i class="fas fa-utensils text-green-500 text-xl mb-2"></i>
                    <div class="text-xs text-gray-700">Alimentação</div>
                </a>
                <a href="progresso.php" class="text-center p-3 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors">
                    <i class="fas fa-chart-line text-purple-500 text-xl mb-2"></i>
                    <div class="text-xs text-gray-700">Progresso</div>
                </a>
                <a href="perfil.php" class="text-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user text-gray-500 text-xl mb-2"></i>
                    <div class="text-xs text-gray-700">Perfil</div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle tema escuro
        function toggleTheme() {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        }

        // Carregar tema salvo
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
        }

        // Toggle treino
        function toggleTreino(treinoId) {
            fetch('api/toggle_treino.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    treino_id: treinoId,
                    data: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Toggle refeição
        function toggleRefeicao(refeicaoId) {
            fetch('api/toggle_refeicao.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    refeicao_id: refeicaoId,
                    data: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Registrar service worker para PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(registration => console.log('SW registered'))
                .catch(error => console.log('SW registration failed'));
        }
    </script>
</body>
</html> 