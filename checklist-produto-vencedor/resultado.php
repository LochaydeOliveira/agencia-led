<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();
$user = getCurrentUser();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promessa_principal = $_POST['promessa_principal'] ?? '';
    $cliente_consciente = $_POST['cliente_consciente'] ?? '';
    $beneficios = $_POST['beneficios'] ?? '';
    $mecanismo_unico = $_POST['mecanismo_unico'] ?? '';
    $checklist = $_POST['checklist'] ?? [];
    
    // Calcular pontos
    $pontos = count($checklist);
    
    // Determinar nota final e mensagem
    if ($pontos >= 8) {
        $nota_final = $pontos;
        $mensagem = "Produto com alto potencial!";
        $cor_classe = "text-green-600";
        $bg_classe = "bg-green-100";
        $icon = "fas fa-trophy";
    } elseif ($pontos >= 5) {
        $nota_final = $pontos;
        $mensagem = "Produto razoável, com potencial";
        $cor_classe = "text-yellow-600";
        $bg_classe = "bg-yellow-100";
        $icon = "fas fa-star";
    } else {
        $nota_final = $pontos;
        $mensagem = "Produto fraco, repense a escolha";
        $cor_classe = "text-red-600";
        $bg_classe = "bg-red-100";
        $icon = "fas fa-exclamation-triangle";
    }
    
    // Salvar no banco de dados
    try {
        $stmt = $pdo->prepare("
            INSERT INTO results (user_id, promessa_principal, cliente_consciente, beneficios, mecanismo_unico, pontos, nota_final, mensagem) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['id'],
            $promessa_principal,
            $cliente_consciente,
            $beneficios,
            $mecanismo_unico,
            $pontos,
            $nota_final,
            $mensagem
        ]);
    } catch (PDOException $e) {
        error_log("Erro ao salvar resultado: " . $e->getMessage());
    }
} else {
    // Redirecionar se não for POST
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Checklist do Produto Lucrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">Checklist do Produto Lucrativo</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-user mr-1"></i>
                        <?php echo htmlspecialchars($user['name']); ?>
                    </span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Resultado Principal -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Resultado da Análise</h2>
                
                <!-- Nota Final -->
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-4xl font-bold mb-4">
                        <?php echo $nota_final; ?>/10
                    </div>
                    <p class="text-lg text-gray-600">Sua pontuação final</p>
                </div>
                
                <!-- Mensagem -->
                <div class="<?php echo $bg_classe; ?> rounded-xl p-6 mb-6">
                    <div class="flex items-center justify-center">
                        <i class="<?php echo $icon; ?> text-3xl <?php echo $cor_classe; ?> mr-4"></i>
                        <h3 class="text-2xl font-bold <?php echo $cor_classe; ?>"><?php echo $mensagem; ?></h3>
                    </div>
                </div>
                
                <!-- Itens Marcados -->
                <div class="bg-blue-50 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4">Itens marcados (<?php echo $pontos; ?>/10)</h4>
                    <div class="grid md:grid-cols-2 gap-3 text-sm">
                        <?php
                        $itens = [
                            'vida_mais_facil' => 'Deixa a vida do cliente mais fácil',
                            'criativos_dinamicos' => 'Criativos são dinâmicos e de qualidade',
                            'buscas_google' => 'Possui buscas no Google',
                            'vendido_lojas' => 'Já está sendo vendido em lojas',
                            'economiza_dinheiro' => 'Economiza dinheiro',
                            'economiza_tempo' => 'Economiza tempo',
                            'nao_nicho_sensivel' => 'Não é nicho sensível',
                            'menos_50_dolares' => 'Custa menos de 50 dólares',
                            'so_internet' => 'Só encontra na internet',
                            'nao_commodity' => 'Produto não é commodity'
                        ];
                        
                        foreach ($itens as $key => $item) {
                            $marcado = in_array($key, $checklist);
                            $icon = $marcado ? 'fas fa-check-circle text-green-600' : 'fas fa-times-circle text-gray-400';
                            $text_class = $marcado ? 'text-gray-800' : 'text-gray-500';
                            ?>
                            <div class="flex items-center">
                                <i class="<?php echo $icon; ?> mr-2"></i>
                                <span class="<?php echo $text_class; ?>"><?php echo $item; ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Respostas das Perguntas -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>
                Suas Respostas
            </h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Promessa Principal</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-800">
                        <?php echo nl2br(htmlspecialchars($promessa_principal)); ?>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Cliente Consciente</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-800">
                        <?php echo nl2br(htmlspecialchars($cliente_consciente)); ?>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Benefícios</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-800">
                        <?php echo nl2br(htmlspecialchars($beneficios)); ?>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Mecanismo Único</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-800">
                        <?php echo nl2br(htmlspecialchars($mecanismo_unico)); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="text-center mt-8 space-x-4">
            <a href="dashboard.php" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus mr-2"></i>
                Nova Análise
            </a>
            
            <button onclick="window.print()" 
                    class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-print mr-2"></i>
                Imprimir Resultado
            </button>
        </div>
    </div>

    <style>
        @media print {
            header, .space-x-4 { display: none; }
            body { background: white; }
            .bg-white { box-shadow: none; }
        }
    </style>
</body>
</html> 