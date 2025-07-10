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

// Buscar treinos organizados por dia
$dias_semana = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
$treinos_por_dia = [];

for ($i = 0; $i < 7; $i++) {
    $stmt = $pdo->prepare("SELECT * FROM treinos WHERE usuario_id = ? AND dia_semana = ? AND ativo = 1 ORDER BY ordem");
    $stmt->execute([$user_id, $i]);
    $treinos_por_dia[$i] = $stmt->fetchAll();
}

// Processar formulário de adicionar treino
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'adicionar') {
        $dia_semana = (int)$_POST['dia_semana'];
        $exercicio = sanitize($_POST['exercicio']);
        $series = (int)$_POST['series'];
        $repeticoes = (int)$_POST['repeticoes'];
        $carga_sugerida = !empty($_POST['carga_sugerida']) ? (float)$_POST['carga_sugerida'] : null;
        $descanso = sanitize($_POST['descanso']);
        $observacoes = sanitize($_POST['observacoes']);
        
        // Buscar próxima ordem
        $stmt = $pdo->prepare("SELECT MAX(ordem) as max_ordem FROM treinos WHERE usuario_id = ? AND dia_semana = ?");
        $stmt->execute([$user_id, $dia_semana]);
        $result = $stmt->fetch();
        $ordem = ($result['max_ordem'] ?? 0) + 1;
        
        $stmt = $pdo->prepare("INSERT INTO treinos (usuario_id, dia_semana, exercicio, series, repeticoes, carga_sugerida, descanso, observacoes, ordem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $dia_semana, $exercicio, $series, $repeticoes, $carga_sugerida, $descanso, $observacoes, $ordem]);
        
        header('Location: treinos.php?success=1');
        exit();
    } elseif ($_POST['acao'] === 'excluir' && isset($_POST['treino_id'])) {
        $treino_id = (int)$_POST['treino_id'];
        $stmt = $pdo->prepare("UPDATE treinos SET ativo = 0 WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$treino_id, $user_id]);
        
        header('Location: treinos.php?deleted=1');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Treinos - My Training</title>
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
                        <h1 class="text-xl font-bold">Meus Treinos</h1>
                        <p class="text-sm opacity-90">Gerencie seus exercícios</p>
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
                Treino adicionado com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="container mx-auto px-4 py-4">
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i>
                Treino removido com sucesso!
            </div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo -->
    <div class="container mx-auto px-4 py-6">
        <?php foreach ($dias_semana as $index => $dia): ?>
            <div class="bg-white rounded-lg card-shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                        <?php echo $dia; ?>
                    </h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        <?php echo count($treinos_por_dia[$index]); ?> exercícios
                    </span>
                </div>
                
                <?php if (empty($treinos_por_dia[$index])): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-dumbbell text-4xl mb-3 opacity-50"></i>
                        <p>Nenhum treino programado para <?php echo $dia; ?></p>
                        <button onclick="adicionarTreinoDia(<?php echo $index; ?>)" class="text-blue-500 mt-2 hover:text-blue-700">
                            Adicionar treino
                        </button>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($treinos_por_dia[$index] as $treino): ?>
                            <div class="border rounded-lg p-4 bg-gray-50 border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($treino['exercicio']); ?></h4>
                                        <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-600">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                <?php echo $treino['series']; ?> séries
                                            </span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                                <?php echo $treino['repeticoes']; ?> reps
                                            </span>
                                            <?php if ($treino['carga_sugerida']): ?>
                                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                                    <?php echo $treino['carga_sugerida']; ?>kg
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($treino['descanso']): ?>
                                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">
                                                    <?php echo $treino['descanso']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($treino['observacoes']): ?>
                                            <p class="text-sm text-gray-500 mt-2 italic">
                                                "<?php echo htmlspecialchars($treino['observacoes']); ?>"
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4 flex flex-col space-y-2">
                                        <button onclick="editarTreino(<?php echo $treino['id']; ?>)" class="p-2 text-blue-600 hover:bg-blue-100 rounded">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="excluirTreino(<?php echo $treino['id']; ?>)" class="p-2 text-red-600 hover:bg-red-100 rounded">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal Adicionar Treino -->
    <div id="modalAdicionar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-lg font-bold text-gray-800">Adicionar Treino</h3>
                    <button onclick="fecharModalAdicionar()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="acao" value="adicionar">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dia da Semana</label>
                        <select name="dia_semana" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione o dia</option>
                            <?php foreach ($dias_semana as $index => $dia): ?>
                                <option value="<?php echo $index; ?>"><?php echo $dia; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Exercício</label>
                        <input type="text" name="exercicio" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Supino Reto">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Séries</label>
                            <input type="number" name="series" required min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Repetições</label>
                            <input type="number" name="repeticoes" required min="1" max="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="12">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Carga (kg)</label>
                            <input type="number" name="carga_sugerida" min="0" step="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Opcional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descanso</label>
                            <input type="text" name="descanso" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 90s">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea name="observacoes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Dicas, observações..."></textarea>
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="fecharModalAdicionar()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
        
        function adicionarTreinoDia(diaIndex) {
            document.querySelector('select[name="dia_semana"]').value = diaIndex;
            abrirModalAdicionar();
        }
        
        function editarTreino(treinoId) {
            // Implementar edição futuramente
            alert('Funcionalidade de edição será implementada em breve!');
        }
        
        function excluirTreino(treinoId) {
            if (confirm('Tem certeza que deseja excluir este treino?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="treino_id" value="${treinoId}">
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