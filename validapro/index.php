<?php
// Sistema ValidaPro - Versão 2.0
require_once 'includes/auth.php';

// Iniciar sessão e verificar login
initSession();
requireLogin();

// Verificar timeout da sessão
checkSessionTimeout();

// Renovar sessão
renewSession();

require_once 'includes/db.php';
require_once 'includes/sugestoes.php';

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
    <title>ValidaPro</title>
    
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
    <!-- Remover elementos decorativos do background -->
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

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Barra de Progresso Fixa -->
        <div id="progressoFixo" class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t z-50 transform transition-transform duration-300" style="transform: translateY(100%);">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                                <span id="progressoPontos">0</span>/10
                            </div>
                            <p class="text-xs text-gray-600 mt-1">Pontos</p>
                        </div>
                        <div class="text-center">
                            <div id="progressoStatus" class="text-lg font-semibold text-gray-600">Iniciando...</div>
                            <p class="text-xs text-gray-500">Status</p>
                        </div>
                        <div class="text-center">
                            <div class="progress-bar-container">
                                <div id="progressoBarra" class="progress-bar-fill" style="width: 0%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">Progresso</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="scrollToTop()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>Topo
                        </button>
                        <button type="button" onclick="hideProgressoFixo()" class="text-gray-500 hover:text-gray-700 text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview do Resultado em Tempo Real -->
        <div id="previewResultado" class="bg-white rounded-2xl shadow-lg p-6 mb-8 hidden">
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Preview do Resultado</h3>
                <div class="flex items-center justify-center space-x-8">
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold mb-2">
                            <span id="previewPontos">0</span>/10
                        </div>
                        <p class="text-sm text-gray-600">Pontuação Atual</p>
                    </div>
                    <div class="text-center">
                        <div id="previewStatus" class="text-lg font-semibold text-gray-600">Iniciando...</div>
                        <p class="text-sm text-gray-500">Status do Produto</p>
                    </div>
                    <div class="text-center">
                        <div id="previewProgress" class="progress-bar-container">
                            <div id="progressBar" class="progress-bar-fill" style="width: 0%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Progresso</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Análise do Seu Produto</h2>
                <p class="text-gray-600">Escolha um nicho ou clique nas sugestões para preencher automaticamente</p>
            </div>

            <!-- Nome do Produto -->
            <div class="mb-8">
                <label for="nome_produto" class="block text-lg font-semibold text-gray-700 mb-2">
                    Nome do Produto <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nome_produto" name="nome_produto" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg input-modern"
                    placeholder="Digite o nome do produto analisado">
            </div>

            <!-- Seletor de Nichos -->
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 mb-8">
                <h3 class="text-xl font-semibold text-purple-800 mb-4 flex items-center">
                    <i class="fas fa-tags mr-3"></i>
                    Escolha seu Nicho (Opcional)
                </h3>
                <p class="text-purple-700 mb-4">Selecione um nicho para carregar sugestões específicas automaticamente:</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <?php foreach ($nichos as $key => $nicho): ?>
                    <button type="button" 
                            class="nicho-btn p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 text-center"
                            data-nicho="<?php echo $key; ?>">
                        <div class="text-2xl mb-2">
                            <?php
                            $icons = [
                                'fitness' => 'fas fa-dumbbell',
                                'beleza' => 'fas fa-spa',
                                'casa' => 'fas fa-home',
                                'tecnologia' => 'fas fa-microchip',
                                'pet' => 'fas fa-paw'
                            ];
                            echo '<i class="' . ($icons[$key] ?? 'fas fa-tag') . ' text-purple-600"></i>';
                            ?>
                        </div>
                        <div class="text-sm font-medium text-gray-700"><?php echo $nicho['nome']; ?></div>
                    </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 text-center">
                    <button type="button" id="limparNicho" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        <i class="fas fa-times mr-1"></i>Limpar seleção
                    </button>
                </div>
            </div>

            <form id="checklistForm" class="space-y-8">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <!-- Bloco 1: Perguntas com Sugestões -->
                <div class="bg-blue-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-lightbulb mr-3"></i>
                        Perguntas de Qualificação (Clique nas sugestões!)
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Promessa Principal -->
                        <div>
                            <label for="promessa_principal" class="block text-sm font-medium text-gray-700 mb-3">
                                Qual a promessa principal do produto?
                            </label>
                            
                            <!-- Sugestões -->
                            <div id="sugestoes-promessa" class="mb-3 space-y-2">
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="promessa_principal" 
                                        data-value="Transformar a vida do cliente de forma rápida e eficaz">
                                    <i class="fas fa-magic mr-2 text-blue-500"></i>
                                    Transformar a vida do cliente de forma rápida e eficaz
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="promessa_principal" 
                                        data-value="Resolver um problema específico de forma definitiva">
                                    <i class="fas fa-target mr-2 text-blue-500"></i>
                                    Resolver um problema específico de forma definitiva
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="promessa_principal" 
                                        data-value="Economizar tempo e dinheiro do cliente">
                                    <i class="fas fa-piggy-bank mr-2 text-blue-500"></i>
                                    Economizar tempo e dinheiro do cliente
                                </button>
                            </div>
                            
                            <!-- Tags Selecionadas -->
                            <div id="tags-promessa_principal" class="mb-3 flex flex-wrap gap-2"></div>
                            
                            <textarea id="promessa_principal" name="promessa_principal" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none input-modern"
                                      placeholder="Ou digite sua própria promessa..."></textarea>
                        </div>
                        
                        <!-- Cliente Consciente -->
                        <div>
                            <label for="cliente_consciente" class="block text-sm font-medium text-gray-700 mb-3">
                                O cliente está consciente da necessidade?
                            </label>
                            
                            <!-- Sugestões -->
                            <div id="sugestoes-consciente" class="mb-3 space-y-2">
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="cliente_consciente" 
                                        data-value="Sim, o cliente já sabe que tem o problema e busca soluções">
                                    <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                    Sim, o cliente já sabe que tem o problema e busca soluções
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="cliente_consciente" 
                                        data-value="Parcialmente, o cliente sente o problema mas não sabe como resolver">
                                    <i class="fas fa-question-circle mr-2 text-yellow-500"></i>
                                    Parcialmente, o cliente sente o problema mas não sabe como resolver
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="cliente_consciente" 
                                        data-value="Não, preciso educar o cliente sobre o problema">
                                    <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                                    Não, preciso educar o cliente sobre o problema
                                </button>
                            </div>
                            
                            <!-- Tags Selecionadas -->
                            <div id="tags-cliente_consciente" class="mb-3 flex flex-wrap gap-2"></div>
                            
                            <textarea id="cliente_consciente" name="cliente_consciente" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none input-modern"
                                      placeholder="Ou digite sua própria resposta..."></textarea>
                        </div>
                        
                        <!-- Benefícios -->
                        <div>
                            <label for="beneficios" class="block text-sm font-medium text-gray-700 mb-3">
                                Quais benefícios esse produto oferece?
                            </label>
                            
                            <!-- Sugestões -->
                            <div id="sugestoes-beneficios" class="mb-3 space-y-2">
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="beneficios" 
                                        data-value="Economia de tempo, dinheiro e esforço">
                                    <i class="fas fa-clock mr-2 text-blue-500"></i>
                                    Economia de tempo, dinheiro e esforço
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="beneficios" 
                                        data-value="Melhora a qualidade de vida e bem-estar">
                                    <i class="fas fa-heart mr-2 text-red-500"></i>
                                    Melhora a qualidade de vida e bem-estar
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="beneficios" 
                                        data-value="Resolve problemas específicos de forma definitiva">
                                    <i class="fas fa-tools mr-2 text-green-500"></i>
                                    Resolve problemas específicos de forma definitiva
                                </button>
                            </div>
                            
                            <!-- Tags Selecionadas -->
                            <div id="tags-beneficios" class="mb-3 flex flex-wrap gap-2"></div>
                            
                            <textarea id="beneficios" name="beneficios" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none input-modern"
                                      placeholder="Ou digite seus próprios benefícios..."></textarea>
                        </div>
                        
                        <!-- Mecanismo Único -->
                        <div>
                            <label for="mecanismo_unico" class="block text-sm font-medium text-gray-700 mb-3">
                                Qual é o mecanismo único?
                            </label>
                            
                            <!-- Sugestões -->
                            <div id="sugestoes-mecanismo" class="mb-3 space-y-2">
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="mecanismo_unico" 
                                        data-value="Tecnologia exclusiva ou patenteada">
                                    <i class="fas fa-microchip mr-2 text-blue-500"></i>
                                    Tecnologia exclusiva ou patenteada
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="mecanismo_unico" 
                                        data-value="Método ou processo único">
                                    <i class="fas fa-cogs mr-2 text-green-500"></i>
                                    Método ou processo único
                                </button>
                                <button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                                        data-field="mecanismo_unico" 
                                        data-value="Combinação única de características">
                                    <i class="fas fa-puzzle-piece mr-2 text-purple-500"></i>
                                    Combinação única de características
                                </button>
                            </div>
                            
                            <!-- Tags Selecionadas -->
                            <div id="tags-mecanismo_unico" class="mb-3 flex flex-wrap gap-2"></div>
                            
                            <textarea id="mecanismo_unico" name="mecanismo_unico" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none input-modern"
                                      placeholder="Ou digite seu próprio mecanismo..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Bloco 2: Checklist de Pontuação -->
                <div class="bg-green-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-green-800 mb-6 flex items-center">
                        <i class="fas fa-check-square mr-3"></i>
                        Checklist de Pontuação (1 ponto por item)
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="vida_mais_facil" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Deixa a vida do cliente mais fácil</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="criativos_dinamicos" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Criativos são dinâmicos e de qualidade</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="buscas_google" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Possui buscas no Google</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="vendido_lojas" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Já está sendo vendido em lojas</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="economiza_dinheiro" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Economiza dinheiro</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="economiza_tempo" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Economiza tempo</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="nao_nicho_sensivel" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Não é nicho sensível</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="menos_50_dolares" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Custa menos de 50 dólares</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="so_internet" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Só encontra na internet</span>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition duration-200">
                            <input type="checkbox" name="checklist[]" value="nao_commodity" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-3 text-gray-700">Produto não é commodity</span>
                        </label>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-100 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-blue-800 font-medium">Pontos marcados: <span id="pontosContador">0</span>/10</span>
                            <span class="text-blue-600 text-sm">Cada item = 1 ponto</span>
                        </div>
                    </div>
                </div>

                <!-- Bloco 3: Análise Estratégica -->
                <div class="bg-orange-50 rounded-xl p-6 analise-estrategica">
                    <h3 class="text-xl font-semibold text-orange-800 mb-6 flex items-center">
                        <i class="fas fa-brain mr-3"></i>
                        Análise Estratégica do Produto
                    </h3>
                    
                    <div class="mb-4 p-4 bg-white rounded-lg border border-orange-200">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-orange-800">📊 Score Estratégico: <span id="score-estrategico">0 / 15</span></p>
                            <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="progressoEstrategico" class="h-full bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                        <div id="alerta-estrategico" class="mt-2 text-sm text-red-600 hidden">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <span id="alerta-texto">Alerta crítico detectado!</span>
                        </div>
                    </div>

                    <!-- Categoria 1: Entendimento do Produto -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-orange-200">
                        <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>
                            1. Entendimento do Produto (Fundamento)
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q1" value="3" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Sim, a promessa é clara e específica</span>
                                    <p class="text-sm text-gray-600 mt-1">O produto resolve um problema bem definido e a promessa é fácil de entender</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q1" value="2" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Parcialmente, precisa de esclarecimento</span>
                                    <p class="text-sm text-gray-600 mt-1">A promessa existe mas pode ser mais específica ou clara</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q1" value="1" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Não, a promessa é vaga ou confusa</span>
                                    <p class="text-sm text-gray-600 mt-1">Difícil entender o que o produto realmente entrega</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Categoria 2: Consciência e Desejo do Cliente -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-orange-200">
                        <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            2. Consciência e Desejo do Cliente
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q2" value="3" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Sim, já busca soluções ativamente</span>
                                    <p class="text-sm text-gray-600 mt-1">O público já procura por esse tipo de produto/solução</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q2" value="2" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Sim, mas precisa ser educado</span>
                                    <p class="text-sm text-gray-600 mt-1">Sente a dor mas não sabe que existe solução</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q2" value="1" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Não, ainda não está consciente</span>
                                    <p class="text-sm text-gray-600 mt-1">Precisa de muita educação sobre o problema</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Categoria 3: Concorrência e Busca -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-orange-200">
                        <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-search mr-2"></i>
                            3. Concorrência e Busca
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q3" value="3" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Existem buscas e concorrência moderada</span>
                                    <p class="text-sm text-gray-600 mt-1">Mercado ativo mas não saturado, com espaço para diferenciação</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q3" value="2" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Muitos concorrentes ou poucas buscas</span>
                                    <p class="text-sm text-gray-600 mt-1">Mercado saturado ou demanda baixa</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q3" value="1" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Sem buscas ou concorrência excessiva</span>
                                    <p class="text-sm text-gray-600 mt-1">Mercado inexistente ou impossível de competir</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Categoria 4: Oferta e Percepção de Valor -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-orange-200">
                        <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            4. Oferta e Percepção de Valor
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q4" value="3" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Margem de 3x ou mais, fácil de comunicar</span>
                                    <p class="text-sm text-gray-600 mt-1">Boa lucratividade e benefício claro</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q4" value="2" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Margem entre 2x e 3x, comunicação possível</span>
                                    <p class="text-sm text-gray-600 mt-1">Lucratividade aceitável com algum esforço</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q4" value="1" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Margem apertada ou difícil de comunicar</span>
                                    <p class="text-sm text-gray-600 mt-1">Baixa lucratividade ou benefício confuso</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Categoria 5: Logística e Fornecimento -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-orange-200">
                        <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-truck mr-2"></i>
                            5. Logística e Fornecimento
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q5" value="3" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Fornecedor confiável, entrega rápida</span>
                                    <p class="text-sm text-gray-600 mt-1">Logística nacional ou internacional confiável</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q5" value="2" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Logística possível mas com riscos</span>
                                    <p class="text-sm text-gray-600 mt-1">Entrega demorada ou fornecedor instável</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q5" value="0" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Logística inviável ou muito arriscada</span>
                                    <p class="text-sm text-gray-600 mt-1">Sem fornecedor confiável ou entrega impossível</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Categoria 6: Percepção Crítica Final -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-orange-200">
                        <h4 class="font-semibold text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-brain mr-2"></i>
                            6. Percepção Crítica Final
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q6" value="3" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Você compraria e confiaria anunciar</span>
                                    <p class="text-sm text-gray-600 mt-1">Produto sólido com potencial de crescimento</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q6" value="2" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Testaria com cautela</span>
                                    <p class="text-sm text-gray-600 mt-1">Potencial limitado ou riscos moderados</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-orange-50 cursor-pointer transition duration-200">
                                <input type="radio" name="q6" value="1" class="mt-1 mr-3 text-orange-600">
                                <div>
                                    <span class="font-medium text-gray-800">Não recomendaria</span>
                                    <p class="text-sm text-gray-600 mt-1">Muitos riscos ou baixo potencial</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" 
                            onclick="processarFormularioComPopup(document.getElementById('checklistForm'))"
                            class=" mg-bt-cst bg-gradient-to-r text-white font-semibold py-4 px-8 rounded-lg hover:from-green-600 hover:to-blue-700 transition duration-200 transform hover:scale-105 text-lg btn-cta">
                        <i class="fas fa-calculator mr-2"></i>
                        Calcular Resultado Final
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup Modal de Resultado -->
    <div id="resultadoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Header do Modal -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-gray-800">Resultado da Análise</h3>
                    <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Conteúdo do Modal -->
            <div id="modalContent" class="p-6">
                <!-- Loading -->
                <div id="modalLoading" class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-2xl mb-4">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Processando Análise...</h4>
                    <p class="text-gray-600">Calculando pontuação e gerando recomendações</p>
                </div>
                
                <!-- Resultado (será preenchido via JavaScript) -->
                <div id="modalResultado" class="hidden">
                    <!-- Conteúdo será inserido aqui -->
                </div>
            </div>
            
            <!-- Footer do Modal -->
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-2xl">
                <div class="flex flex-wrap justify-center gap-3">
                    <button onclick="fecharModal()" 
                            class="px-6 py-2 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Fechar
                    </button>
                    <button onclick="novaAnalise()" 
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Nova Análise
                    </button>
                    <button onclick="exportarResultado()" 
                            class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dados dos nichos (convertidos do PHP)
        const nichosData = <?php echo json_encode($nichos); ?>;
        
        // Sistema de seleção de nichos
        document.querySelectorAll('.nicho-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const nicho = this.getAttribute('data-nicho');
                const nichoInfo = nichosData[nicho];
                
                if (nichoInfo) {
                    // Atualizar sugestões baseadas no nicho
                    atualizarSugestoesNicho(nichoInfo);
                    
                    // Feedback visual
                    document.querySelectorAll('.nicho-btn').forEach(b => b.classList.remove('border-purple-500', 'bg-purple-100'));
                    this.classList.add('border-purple-500', 'bg-purple-100');
                    
                    // Mostrar notificação
                    mostrarNotificacao(`Sugestões carregadas para: ${nichoInfo.nome}`);
                }
            });
        });
        
        // Limpar seleção de nicho
        document.getElementById('limparNicho').addEventListener('click', function() {
            document.querySelectorAll('.nicho-btn').forEach(b => b.classList.remove('border-purple-500', 'bg-purple-100'));
            restaurarSugestoesPadrao();
            mostrarNotificacao('Sugestões restauradas para padrão');
        });
        
        // Atualizar sugestões baseadas no nicho
        function atualizarSugestoesNicho(nichoInfo) {
            // Promessa principal
            const promessaContainer = document.getElementById('sugestoes-promessa');
            promessaContainer.innerHTML = nichoInfo.promessas.map(promessa => 
                `<button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                        data-field="promessa_principal" data-value="${promessa}">
                    <i class="fas fa-magic mr-2 text-blue-500"></i>${promessa}
                </button>`
            ).join('');
            
            // Cliente Consciente
            const clienteContainer = document.getElementById('sugestoes-consciente');
            if (nichoInfo.cliente_consciente) {
                clienteContainer.innerHTML = nichoInfo.cliente_consciente.map(cliente => 
                    `<button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                            data-field="cliente_consciente" data-value="${cliente}">
                        <i class="fas fa-user-check mr-2 text-purple-500"></i>${cliente}
                    </button>`
                ).join('');
            }
            
            // Benefícios
            const beneficiosContainer = document.getElementById('sugestoes-beneficios');
            beneficiosContainer.innerHTML = nichoInfo.beneficios.map(beneficio => 
                `<button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                        data-field="beneficios" data-value="${beneficio}">
                    <i class="fas fa-heart mr-2 text-red-500"></i>${beneficio}
                </button>`
            ).join('');
            
            // Mecanismos
            const mecanismoContainer = document.getElementById('sugestoes-mecanismo');
            mecanismoContainer.innerHTML = nichoInfo.mecanismos.map(mecanismo => 
                `<button type="button" class="sugestao-btn w-full text-left p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-sm" 
                        data-field="mecanismo_unico" data-value="${mecanismo}">
                    <i class="fas fa-cogs mr-2 text-green-500"></i>${mecanismo}
                </button>`
            ).join('');
            
            // Reaplicar event listeners
            aplicarEventListenersSugestoes();
        }
        
        // Restaurar sugestões padrão
        function restaurarSugestoesPadrao() {
            // Recarregar a página para restaurar as sugestões originais
            location.reload();
        }
        
        // Mostrar notificação
        function mostrarNotificacao(mensagem) {
            const notificacao = document.createElement('div');
            notificacao.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
            notificacao.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${mensagem}</span>
                </div>
            `;
            document.body.appendChild(notificacao);
            
            setTimeout(() => {
                notificacao.style.transform = 'translateX(100%)';
                setTimeout(() => notificacao.remove(), 300);
            }, 2000);
        }
        
        // Sistema de sugestões clicáveis
        function aplicarEventListenersSugestoes() {
            document.querySelectorAll('.sugestao-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const field = this.getAttribute('data-field');
                    const value = this.getAttribute('data-value');
                    
                    // Verificar se já existe esta tag
                    const tagsContainer = document.getElementById(`tags-${field}`);
                    const existingTags = tagsContainer.querySelectorAll('.tag-item');
                    const tagExists = Array.from(existingTags).some(tag => tag.getAttribute('data-value') === value);
                    
                    if (tagExists) {
                        // Se já existe, remover a tag
                        const tagToRemove = tagsContainer.querySelector(`[data-value="${value}"]`);
                        tagToRemove.remove();
                        
                        // Remover destaque do botão
                        this.classList.remove('bg-green-100', 'border-green-400');
                        this.innerHTML = this.getAttribute('data-original-html') || this.innerHTML;
                    } else {
                        // Se não existe, adicionar a tag
                        const tag = document.createElement('div');
                        tag.className = 'tag-item inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium';
                        tag.setAttribute('data-value', value);
                        tag.innerHTML = `
                            <span>${value}</span>
                            <button type="button" onclick="removerTag('${field}', '${value.replace(/'/g, "\\'")}')" class="ml-2 text-green-600 hover:text-green-800">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        `;
                        tagsContainer.appendChild(tag);
                        
                        // Destacar o botão
                        this.classList.add('bg-green-100', 'border-green-400');
                        this.setAttribute('data-original-html', this.innerHTML);
                        this.innerHTML = '<i class="fas fa-check mr-2 text-green-600"></i>' + value;
                    }
                    
                    // Atualizar o textarea com todas as tags
                    atualizarTextareaComTags(field);
                    
                    // Atualizar preview
                    atualizarPreview();
                });
            });
        }
        
        // Função para remover tag
        function removerTag(field, value) {
            const tagsContainer = document.getElementById(`tags-${field}`);
            const tagToRemove = tagsContainer.querySelector(`[data-value="${value}"]`);
            if (tagToRemove) {
                tagToRemove.remove();
                
                // Remover destaque do botão correspondente
                const btn = document.querySelector(`[data-field="${field}"][data-value="${value}"]`);
                if (btn) {
                    btn.classList.remove('bg-green-100', 'border-green-400');
                    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
                }
                
                // Atualizar textarea
                atualizarTextareaComTags(field);
                
                // Atualizar preview
                atualizarPreview();
            }
        }
        
        // Função para atualizar textarea com tags
        function atualizarTextareaComTags(field) {
            const tagsContainer = document.getElementById(`tags-${field}`);
            const textarea = document.getElementById(field);
            const tags = tagsContainer.querySelectorAll('.tag-item span');
            
            if (tags.length > 0) {
                const values = Array.from(tags).map(tag => tag.textContent);
                textarea.value = values.join('\n• ');
                textarea.classList.add('border-green-500', 'bg-green-50');
            } else {
                textarea.classList.remove('border-green-500', 'bg-green-50');
            }
        }
        
        // Aplicar event listeners iniciais
        aplicarEventListenersSugestoes();

        // Sistema de Análise Estratégica
        function calcularScoreEstrategico() {
            let total = 0;
            const perguntas = ['q1', 'q2', 'q3', 'q4', 'q5', 'q6'];
            let alertas = [];
            
            perguntas.forEach(nome => {
                const checked = document.querySelector(`input[name="${nome}"]:checked`);
                if (checked) {
                    total += parseInt(checked.value);
                    
                    // Verificar alertas críticos
                    if (nome === 'q5' && checked.value === '0') {
                        alertas.push('⚠️ Logística inviável — repense o produto');
                    }
                    if (nome === 'q3' && checked.value === '1') {
                        alertas.push('⚠️ Mercado inexistente ou saturado');
                    }
                    if (nome === 'q6' && checked.value === '1') {
                        alertas.push('⚠️ Você mesmo não recomendaria o produto');
                    }
                }
            });

            // Atualizar score
            document.getElementById("score-estrategico").innerText = `${total} / 18`;
            
            // Atualizar barra de progresso
            const progressoEstrategico = document.getElementById("progressoEstrategico");
            const percentual = (total / 18) * 100;
            progressoEstrategico.style.width = `${percentual}%`;
            
            // Atualizar alertas
            atualizarAlertaEstrategico(alertas);
            
            // Atualizar preview geral
            atualizarPreview();
        }

        function atualizarAlertaEstrategico(alertas) {
            const alertaDiv = document.getElementById("alerta-estrategico");
            const alertaTexto = document.getElementById("alerta-texto");
            
            if (alertas.length > 0) {
                alertaTexto.textContent = alertas.join(' | ');
                alertaDiv.classList.remove("hidden");
            } else {
                alertaDiv.classList.add("hidden");
            }
        }

        // Event listeners para análise estratégica
        document.querySelectorAll('#analise-estrategica input[type="radio"]').forEach(input => {
            input.addEventListener('change', calcularScoreEstrategico);
        });

        // Contador de pontos em tempo real
        const checkboxes = document.querySelectorAll('input[name="checklist[]"]');
        const contador = document.getElementById('pontosContador');
        
        function atualizarContador() {
            const marcados = document.querySelectorAll('input[name="checklist[]"]:checked').length;
            contador.textContent = marcados;
            atualizarPreview();
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', atualizarContador);
        });

        // Preview em tempo real
        function atualizarPreview() {
            const pontos = document.querySelectorAll('input[name="checklist[]"]:checked').length;
            const previewDiv = document.getElementById('previewResultado');
            const previewPontos = document.getElementById('previewPontos');
            const previewStatus = document.getElementById('previewStatus');
            const progressBar = document.getElementById('progressBar');
            
            // Elementos da barra fixa
            const progressoPontos = document.getElementById('progressoPontos');
            const progressoStatus = document.getElementById('progressoStatus');
            const progressoBarra = document.getElementById('progressoBarra');
            
            // Calcular score estratégico
            let scoreEstrategico = 0;
            const perguntas = ['q1', 'q2', 'q3', 'q4', 'q5', 'q6'];
            perguntas.forEach(nome => {
                const checked = document.querySelector(`input[name="${nome}"]:checked`);
                if (checked) {
                    scoreEstrategico += parseInt(checked.value);
                }
            });
            
            // Mostrar preview se há pelo menos 1 ponto ou campos preenchidos
            const camposPreenchidos = ['promessa_principal', 'cliente_consciente', 'beneficios', 'mecanismo_unico']
                .some(field => document.getElementById(field).value.trim() !== '');
            
            if (pontos > 0 || camposPreenchidos || scoreEstrategico > 0) {
                previewDiv.classList.remove('hidden');
                previewPontos.textContent = pontos;
                progressoPontos.textContent = pontos;
                
                // Atualizar status e progresso
                let status, progressWidth, statusClass;
                
                if (pontos >= 8) {
                    status = "Alto Potencial! 🏆";
                    progressWidth = "90%";
                    statusClass = "text-green-600";
                } else if (pontos >= 5) {
                    status = "Potencial Razoável ⭐";
                    progressWidth = "60%";
                    statusClass = "text-yellow-600";
                } else if (pontos >= 3) {
                    status = "Potencial Baixo ⚠️";
                    progressWidth = "30%";
                    statusClass = "text-orange-600";
                } else {
                    status = "Precisa Melhorar 📈";
                    progressWidth = "10%";
                    statusClass = "text-red-600";
                }
                
                // Adicionar informação do score estratégico se disponível
                if (scoreEstrategico > 0) {
                    status += ` | Estratégico: ${scoreEstrategico}/18`;
                }
                
                previewStatus.textContent = status;
                previewStatus.className = `text-lg font-semibold ${statusClass}`;
                progressBar.style.width = progressWidth;
                
                // Atualizar barra fixa
                progressoStatus.textContent = status;
                progressoStatus.className = `text-lg font-semibold ${statusClass}`;
                progressoBarra.style.width = progressWidth;
                
                // Mostrar barra fixa
                showProgressoFixo();
            } else {
                previewDiv.classList.add('hidden');
                hideProgressoFixo();
            }
        }

        // Atualizar preview quando campos de texto mudam
        ['promessa_principal', 'cliente_consciente', 'beneficios', 'mecanismo_unico'].forEach(field => {
            document.getElementById(field).addEventListener('input', atualizarPreview);
        });
        
        // Validação do formulário
        document.getElementById('checklistForm').addEventListener('submit', function(e) {
            // PREVENT DEFAULT IMEDIATAMENTE
            e.preventDefault();
            e.stopPropagation();
            
            console.log('=== DEBUG FORMULÁRIO ===');
            console.log('Formulário submetido!');
            
            const requiredFields = ['promessa_principal', 'cliente_consciente', 'beneficios', 'mecanismo_unico'];
            let isValid = true;
            let emptyFields = [];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                const value = element.value.trim();
                
                console.log(`Campo ${field}: "${value}"`);
                
                if (!value) {
                    element.classList.add('border-red-500', 'bg-red-50');
                    isValid = false;
                    emptyFields.push(field);
                } else {
                    element.classList.remove('border-red-500', 'bg-red-50');
                }
            });
            
            // Verificar checkboxes
            const checkboxes = document.querySelectorAll('input[name="checklist[]"]:checked');
            console.log('Checkboxes marcados:', checkboxes.length);
            checkboxes.forEach(cb => console.log('Checkbox:', cb.value));
            
            if (!isValid) {
                const message = `Por favor, preencha os seguintes campos obrigatórios:\n\n${emptyFields.join('\n')}`;
                alert(message);
                console.log('Formulário inválido:', emptyFields);
                return false;
            }
            
            console.log('Formulário válido, processando...');
            
            // Adicionar loading no botão
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processando...';
            submitBtn.disabled = true;
            
            // Processar com popup
            processarFormularioComPopup(this);
            
            return false;
        });

        // Inicializar preview
        atualizarPreview();

        // Funções para barra de progresso fixa
        function showProgressoFixo() {
            document.getElementById('progressoFixo').style.transform = 'translateY(0)';
        }

        function hideProgressoFixo() {
            document.getElementById('progressoFixo').style.transform = 'translateY(100%)';
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Funções do Modal
        function mostrarModal() {
            const modal = document.getElementById('resultadoModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Animar entrada
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function fecharModal() {
            const modal = document.getElementById('resultadoModal');
            const modalContent = document.getElementById('modalContent');
            
            // Animar saída
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                
                // Resetar conteúdo do modal
                document.getElementById('modalLoading').classList.remove('hidden');
                document.getElementById('modalResultado').classList.add('hidden');
            }, 300);
        }

        function processarFormularioComPopup(form) {
            console.log('=== PROCESSANDO FORMULÁRIO ===');
            
            // Mostrar modal com loading
            mostrarModal();
            
            // Coletar dados do formulário
            const formData = new FormData(form);
            console.log('FormData criado:', formData);
            
            // Simular processamento (substituir por AJAX real)
            setTimeout(() => {
                console.log('=== CALCULANDO RESULTADO ===');
                
                const pontos = document.querySelectorAll('input[name="checklist[]"]:checked').length;
                const promessa = document.getElementById('promessa_principal').value;
                const cliente = document.getElementById('cliente_consciente').value;
                const beneficios = document.getElementById('beneficios').value;
                const mecanismo = document.getElementById('mecanismo_unico').value;
                const nomeProduto = document.getElementById('nome_produto').value;
                
                console.log('Dados coletados:', { pontos, promessa, cliente, beneficios, mecanismo, nomeProduto });
                
                // Calcular resultado
                const resultado = calcularResultado(pontos, promessa, cliente, beneficios, mecanismo);
                console.log('Resultado calculado:', resultado);
                
                // Mostrar resultado no modal
                mostrarResultadoNoModal(resultado);
                
                // Restaurar botão
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-calculator mr-2"></i>Calcular Resultado Final';
                submitBtn.disabled = false;
                
                console.log('=== PROCESSAMENTO CONCLUÍDO ===');
            }, 2000);
        }

        function calcularResultado(pontos, promessa, cliente, beneficios, mecanismo) {
            let status, cor, icon, recomendacao, proximosPassos;
            
            // Calcular score estratégico
            let scoreEstrategico = 0;
            const perguntas = ['q1', 'q2', 'q3', 'q4', 'q5', 'q6'];
            perguntas.forEach(nome => {
                const checked = document.querySelector(`input[name="${nome}"]:checked`);
                if (checked) {
                    scoreEstrategico += parseInt(checked.value);
                }
            });
            
            // Verificar alertas críticos
            let alertasCriticos = [];
            perguntas.forEach(nome => {
                const checked = document.querySelector(`input[name="${nome}"]:checked`);
                if (checked) {
                    if (nome === 'q5' && checked.value === '0') {
                        alertasCriticos.push('Logística inviável');
                    }
                    if (nome === 'q3' && checked.value === '1') {
                        alertasCriticos.push('Mercado inexistente/saturado');
                    }
                    if (nome === 'q6' && checked.value === '1') {
                        alertasCriticos.push('Baixa confiança no produto');
                    }
                }
            });
            
            // Determinar resultado baseado nos dois scores
            if (pontos >= 8 && scoreEstrategico >= 12) {
                status = "Produto com alto potencial! 🏆";
                cor = "text-green-600";
                icon = "fas fa-trophy";
                recomendacao = "Excelente! Seu produto tem tanto score técnico quanto estratégico alto. Foque em criar campanhas de marketing agressivas, ampliar canais de venda e investir em branding para consolidar sua marca.";
                proximosPassos = [
                    "Lance campanhas de Facebook Ads e Google Ads segmentadas para o público-alvo.",
                    "Implemente estratégias de remarketing para aumentar conversão.",
                    "Crie uma página de vendas otimizada com provas sociais (depoimentos, avaliações).",
                    "Invista em parcerias com influenciadores do nicho.",
                    "Monitore métricas como ROI, CAC e LTV semanalmente.",
                    "Considere expandir para marketplaces ou afiliados."
                ];
            } else if (pontos >= 5 && scoreEstrategico >= 8) {
                status = "Produto razoável, com potencial ⭐";
                cor = "text-yellow-600";
                icon = "fas fa-star";
                recomendacao = "Seu produto tem potencial, mas precisa de ajustes. Foque em identificar e melhorar os pontos fracos antes de escalar o investimento.";
                proximosPassos = [
                    "Analise os critérios não marcados e busque formas de aprimorá-los.",
                    "Realize testes A/B em criativos e páginas de venda.",
                    "Colete feedback de clientes e ajuste a oferta conforme necessário.",
                    "Ajuste o preço ou condições de frete para aumentar competitividade.",
                    "Invista em conteúdo para educar o público sobre o diferencial do produto."
                ];
            } else if (alertasCriticos.length > 0) {
                status = "Produto com problemas críticos! 🚨";
                cor = "text-red-600";
                icon = "fas fa-exclamation-triangle";
                recomendacao = `ATENÇÃO: Seu produto apresenta problemas críticos: ${alertasCriticos.join(', ')}. Recomendamos repensar a escolha ou resolver esses pontos antes de investir.`;
                proximosPassos = [
                    "Resolva os problemas críticos identificados antes de prosseguir.",
                    "Pesquise produtos alternativos com menos riscos.",
                    "Analise a viabilidade logística e de mercado.",
                    "Considere mudar o nicho ou público-alvo.",
                    "Reveja sua estratégia de marketing e proposta de valor."
                ];
            } else {
                status = "Produto fraco, repense a escolha 📈";
                cor = "text-red-600";
                icon = "fas fa-exclamation-triangle";
                recomendacao = "Este produto pode não ser a melhor escolha no momento. Reavalie o nicho, procure alternativas ou faça mudanças significativas na oferta.";
                proximosPassos = [
                    "Pesquise produtos alternativos com maior demanda ou menos concorrência.",
                    "Analise os principais concorrentes e identifique oportunidades de diferenciação.",
                    "Considere mudar o nicho ou público-alvo.",
                    "Participe de grupos e fóruns para identificar tendências emergentes.",
                    "Reveja sua estratégia de marketing e proposta de valor." 
                ];
            }
            
            return {
                pontos,
                scoreEstrategico,
                alertasCriticos,
                status,
                cor,
                icon,
                recomendacao,
                proximosPassos,
                promessa,
                cliente,
                beneficios,
                mecanismo,
                nomeProduto: document.getElementById('nome_produto').value
            };
        }

        function mostrarResultadoNoModal(resultado) {
            const modalLoading = document.getElementById('modalLoading');
            const modalResultado = document.getElementById('modalResultado');
            
            // Esconder loading
            modalLoading.classList.add('hidden');
            
            // Mostrar resultado
            modalResultado.classList.remove('hidden');
            
            // Gerar HTML do resultado
            modalResultado.innerHTML = `
                <!-- Pontuação Principal -->
                <div class="text-center mb-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-4xl font-bold mb-4">
                                ${resultado.pontos}/10
                            </div>
                            <p class="text-lg text-gray-600 mb-2">Score Técnico</p>
                            <p class="text-sm text-gray-500">Checklist de critérios</p>
                        </div>
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-r from-orange-500 to-red-600 text-white text-4xl font-bold mb-4">
                                ${resultado.scoreEstrategico}/18
                            </div>
                            <p class="text-lg text-gray-600 mb-2">Score Estratégico</p>
                            <p class="text-sm text-gray-500">Análise de viabilidade</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-100 rounded-xl p-6 mb-6">
                        <div class="flex items-center justify-center">
                            <i class="${resultado.icon} text-3xl ${resultado.cor} mr-4"></i>
                            <h3 class="text-2xl font-bold ${resultado.cor}">${resultado.status}</h3>
                        </div>
                    </div>
                    
                    ${resultado.alertasCriticos.length > 0 ? `
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <h4 class="text-lg font-semibold text-red-800 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Alertas Críticos Detectados
                        </h4>
                        <ul class="space-y-1">
                            ${resultado.alertasCriticos.map(alerta => `
                                <li class="flex items-start text-red-700">
                                    <i class="fas fa-times-circle mt-1 mr-2 text-sm"></i>
                                    <span>${alerta}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    ` : ''}
                </div>
                
                <!-- Recomendações -->
                <div class="bg-blue-50 rounded-xl p-6 mb-6">
                    <h4 class="text-lg font-semibold text-blue-800 mb-3">Análise do Especialista</h4>
                    <p class="text-blue-700 leading-relaxed">${resultado.recomendacao}</p>
                </div>
                
                <!-- Próximos Passos -->
                <div class="bg-green-50 rounded-xl p-6 mb-6">
                    <h4 class="text-lg font-semibold text-green-800 mb-3">Próximos Passos</h4>
                    <ul class="space-y-2">
                        ${resultado.proximosPassos.map(passo => `
                            <li class="flex items-start">
                                <i class="fas fa-arrow-right text-green-500 mt-1 mr-2 text-sm"></i>
                                <span class="text-green-700">${passo}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
                
                <!-- Respostas -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-700 mb-2">Nome do Produto</h5>
                        <p class="text-gray-800">${resultado.nomeProduto}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-700 mb-2">Promessa Principal</h5>
                        <p class="text-gray-800">${resultado.promessa}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-700 mb-2">Cliente Consciente</h5>
                        <p class="text-gray-800">${resultado.cliente}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-700 mb-2">Benefícios</h5>
                        <p class="text-gray-800">${resultado.beneficios}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-700 mb-2">Mecanismo Único</h5>
                        <p class="text-gray-800">${resultado.mecanismo}</p>
                    </div>
                </div>
            `;
        }

        function novaAnalise() {
            fecharModal();
            // Limpar formulário
            document.getElementById('checklistForm').reset();
            // Atualizar preview
            atualizarPreview();
            // Scroll para topo
            scrollToTop();
        }

        function exportarResultado() {
            // Coletar dados do resultado exibido no modal
            const modalResultado = document.getElementById('modalResultado');
            if (!modalResultado || modalResultado.classList.contains('hidden')) {
                alert('O resultado ainda não foi gerado!');
                return;
            }

            // Extrair dados do resultado (usando variáveis globais se possível)
            // Se não houver variáveis globais, extrair do DOM
            let pontos = document.querySelector('.modalResultado-pontos')?.textContent || document.getElementById('previewPontos')?.textContent || '0';
            pontos = pontos.replace(/\D/g, '') || '0';
            let status = document.querySelector('.modalResultado-status')?.textContent || '';
            let recomendacao = document.querySelector('.modalResultado-recomendacao')?.textContent || '';
            let proximosPassos = Array.from(document.querySelectorAll('.modalResultado-passos li')).map(li => li.textContent);
            let promessa = document.querySelector('.modalResultado-promessa')?.textContent || '';
            let cliente = document.querySelector('.modalResultado-cliente')?.textContent || '';
            let beneficios = document.querySelector('.modalResultado-beneficios')?.textContent || '';
            let mecanismo = document.querySelector('.modalResultado-mecanismo')?.textContent || '';
            let nomeProduto = document.querySelector('.modalResultado-nomeProduto')?.textContent || '';

            // Fallback: pegar dos campos do formulário se não encontrar no modal
            if (!nomeProduto) nomeProduto = document.getElementById('nome_produto')?.value || '';
            if (!promessa) promessa = document.getElementById('promessa_principal')?.value || '';
            if (!cliente) cliente = document.getElementById('cliente_consciente')?.value || '';
            if (!beneficios) beneficios = document.getElementById('beneficios')?.value || '';
            if (!mecanismo) mecanismo = document.getElementById('mecanismo_unico')?.value || '';

            // Se não encontrar status, pegar do preview
            if (!status) status = document.getElementById('previewStatus')?.textContent || '';

            // Se não encontrar recomendação, pegar do modal
            if (!recomendacao) {
                const recDiv = Array.from(document.querySelectorAll('#modalResultado .bg-blue-50'))[0];
                if (recDiv) recomendacao = recDiv.textContent.trim();
            }

            // Se não encontrar próximos passos, pegar do modal
            if (!proximosPassos.length) {
                const passosDiv = Array.from(document.querySelectorAll('#modalResultado .bg-green-50 ul li'));
                proximosPassos = passosDiv.map(li => li.textContent.trim());
            }

            // Montar form e enviar via POST para exportar_pdf.php
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'exportar_pdf.php';
            form.target = '_blank';

            const addField = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = typeof value === 'string' ? value : JSON.stringify(value);
                form.appendChild(input);
            };

            addField('pontos', pontos);
            addField('status', status);
            addField('recomendacao', recomendacao);
            addField('proximosPassos', proximosPassos);
            addField('promessa', promessa);
            addField('cliente', cliente);
            addField('beneficios', beneficios);
            addField('mecanismo', mecanismo);
            addField('nomeProduto', nomeProduto); // Adicionar nome do produto ao form

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        // Mostrar barra de progresso quando a página é carregada
        window.addEventListener('load', showProgressoFixo);
    </script>
</body>
</html> 