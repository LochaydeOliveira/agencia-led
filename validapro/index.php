<?php
// Configurações para exibir erros no navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão primeiro, antes de qualquer saída
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/sugestoes.php';

requireLogin();
$user = getCurrentUser();

// Gerar CSRF token se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obter nichos disponíveis
$nichos = getAllNichos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ValidaPro</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon-oficial-validapro.png">
    <link rel="apple-touch-icon" href="assets/img/favicon-oficial-validapro.png">
    
    <!-- Logo SVG -->
    <link rel="preload" as="image" href="assets/svg/logo-valida-pro-em-svg.svg">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="assets/css/logo.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="logo-produtovencedor logo-small">
                        <img src="assets/img/logo-validapro-checklist.svg" alt="ValidaPro Logo" class="h-12 md:h-16">
                    </div>
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

    <!-- Conteúdo Principal -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Título e Descrição -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">ValidaPro</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Analise produtos vencedores em dropshipping com nosso sistema de pontuação duplo. 
                Valide a viabilidade técnica e estratégica do seu produto.
            </p>
        </div>

        <!-- Formulário Principal -->
        <form method="POST" action="resultado.php" class="space-y-8">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Nome do Produto -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-box text-blue-500 mr-3"></i>
                    Informações do Produto
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="nome_produto" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Produto
                        </label>
                        <input type="text" id="nome_produto" name="nome_produto" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: Organizador de Cabos USB">
                    </div>
                    
                    <div>
                        <label for="nicho" class="block text-sm font-medium text-gray-700 mb-2">
                            Nicho do Produto
                        </label>
                        <select id="nicho" name="nicho" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Selecione um nicho</option>
                            <?php foreach ($nichos as $nicho): ?>
                                <option value="<?php echo htmlspecialchars($nicho['id']); ?>">
                                    <?php echo htmlspecialchars($nicho['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Qualificação do Produto -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-chart-line text-green-500 mr-3"></i>
                    Qualificação do Produto
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="promessa_principal" class="block text-sm font-medium text-gray-700 mb-2">
                            Qual é a promessa principal do produto?
                        </label>
                        <textarea id="promessa_principal" name="promessa_principal" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Ex: Organizar e proteger seus cabos USB de forma prática e elegante"></textarea>
                    </div>
                    
                    <div>
                        <label for="cliente_consciente" class="block text-sm font-medium text-gray-700 mb-2">
                            O cliente está consciente do problema?
                        </label>
                        <textarea id="cliente_consciente" name="cliente_consciente" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Ex: Sim, as pessoas sabem que cabos desorganizados são um problema"></textarea>
                    </div>
                    
                    <div>
                        <label for="beneficios" class="block text-sm font-medium text-gray-700 mb-2">
                            Quais são os benefícios principais?
                        </label>
                        <textarea id="beneficios" name="beneficios" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Ex: Organização, proteção dos cabos, aparência limpa"></textarea>
                    </div>
                    
                    <div>
                        <label for="mecanismo_unico" class="block text-sm font-medium text-gray-700 mb-2">
                            Qual é o mecanismo único do produto?
                        </label>
                        <textarea id="mecanismo_unico" name="mecanismo_unico" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Ex: Sistema de fixação magnética que mantém os cabos organizados"></textarea>
                    </div>
                </div>
            </div>

            <!-- Checklist de Critérios -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                    Checklist de Critérios
                </h2>
                
                <p class="text-gray-600 mb-6">
                    Marque os critérios que se aplicam ao seu produto:
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="vida_mais_facil" name="checklist[]" value="vida_mais_facil" class="mr-3">
                        <label for="vida_mais_facil" class="text-sm font-medium text-gray-700">
                            Deixa a vida mais fácil
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="criativos_dinamicos" name="checklist[]" value="criativos_dinamicos" class="mr-3">
                        <label for="criativos_dinamicos" class="text-sm font-medium text-gray-700">
                            Criativos dinâmicos
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="buscas_google" name="checklist[]" value="buscas_google" class="mr-3">
                        <label for="buscas_google" class="text-sm font-medium text-gray-700">
                            Buscas no Google
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="vendido_lojas" name="checklist[]" value="vendido_lojas" class="mr-3">
                        <label for="vendido_lojas" class="text-sm font-medium text-gray-700">
                            Já vendido em lojas
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="economiza_dinheiro" name="checklist[]" value="economiza_dinheiro" class="mr-3">
                        <label for="economiza_dinheiro" class="text-sm font-medium text-gray-700">
                            Economiza dinheiro
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="economiza_tempo" name="checklist[]" value="economiza_tempo" class="mr-3">
                        <label for="economiza_tempo" class="text-sm font-medium text-gray-700">
                            Economiza tempo
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="nao_nicho_sensivel" name="checklist[]" value="nao_nicho_sensivel" class="mr-3">
                        <label for="nao_nicho_sensivel" class="text-sm font-medium text-gray-700">
                            Não é nicho sensível
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="menos_50_dolares" name="checklist[]" value="menos_50_dolares" class="mr-3">
                        <label for="menos_50_dolares" class="text-sm font-medium text-gray-700">
                            Menos de $50
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="so_internet" name="checklist[]" value="so_internet" class="mr-3">
                        <label for="so_internet" class="text-sm font-medium text-gray-700">
                            Só na internet
                        </label>
                    </div>
                    
                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <input type="checkbox" id="nao_commodity" name="checklist[]" value="nao_commodity" class="mr-3">
                        <label for="nao_commodity" class="text-sm font-medium text-gray-700">
                            Não é commodity
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botão de Envio -->
            <div class="text-center">
                <button type="submit" 
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-indigo-700 transition duration-200 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-calculator mr-2"></i>
                    Calcular Pontuação
                </button>
            </div>
        </form>
    </div>

    <!-- Barra de Progresso Fixa -->
    <div id="progress-bar" class="fixed bottom-0 left-0 w-full h-1 bg-gray-200 z-50" style="display: none;">
        <div id="progress-fill" class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-300 ease-out" style="width: 0%"></div>
    </div>

    <script>
        // Mostrar barra de progresso ao enviar formulário
        document.querySelector('form').addEventListener('submit', function() {
            const progressBar = document.getElementById('progress-bar');
            const progressFill = document.getElementById('progress-fill');
            
            progressBar.style.display = 'block';
            
            // Animação da barra de progresso
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 90) {
                    clearInterval(interval);
                } else {
                    width += 2;
                    progressFill.style.width = width + '%';
                }
            }, 50);
        });
    </script>
</body>
</html> 