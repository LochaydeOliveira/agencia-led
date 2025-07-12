<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Checklist do Produto Lucrativo</title>
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
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Análise do Seu Produto</h2>
                <p class="text-gray-600">Preencha as informações abaixo para obter sua pontuação</p>
            </div>

            <form id="checklistForm" method="POST" action="resultado.php" class="space-y-8">
                <!-- Bloco 1: Perguntas Abertas -->
                <div class="bg-blue-50 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-question-circle mr-3"></i>
                        Perguntas de Qualificação
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="promessa_principal" class="block text-sm font-medium text-gray-700 mb-2">
                                Qual a promessa principal do produto?
                            </label>
                            <textarea id="promessa_principal" name="promessa_principal" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Descreva a promessa principal..."></textarea>
                        </div>
                        
                        <div>
                            <label for="cliente_consciente" class="block text-sm font-medium text-gray-700 mb-2">
                                O cliente está consciente da necessidade?
                            </label>
                            <textarea id="cliente_consciente" name="cliente_consciente" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Explique o nível de consciência..."></textarea>
                        </div>
                        
                        <div>
                            <label for="beneficios" class="block text-sm font-medium text-gray-700 mb-2">
                                Quais benefícios esse produto oferece?
                            </label>
                            <textarea id="beneficios" name="beneficios" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Liste os principais benefícios..."></textarea>
                        </div>
                        
                        <div>
                            <label for="mecanismo_unico" class="block text-sm font-medium text-gray-700 mb-2">
                                Qual é o mecanismo único?
                            </label>
                            <textarea id="mecanismo_unico" name="mecanismo_unico" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Descreva o mecanismo único..."></textarea>
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

                <div class="text-center">
                    <button type="submit" 
                            class="bg-gradient-to-r from-green-500 to-blue-600 text-white font-semibold py-4 px-8 rounded-lg hover:from-green-600 hover:to-blue-700 transition duration-200 transform hover:scale-105 text-lg">
                        <i class="fas fa-calculator mr-2"></i>
                        Calcular Resultado
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Contador de pontos em tempo real
        const checkboxes = document.querySelectorAll('input[name="checklist[]"]');
        const contador = document.getElementById('pontosContador');
        
        function atualizarContador() {
            const marcados = document.querySelectorAll('input[name="checklist[]"]:checked').length;
            contador.textContent = marcados;
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', atualizarContador);
        });
        
        // Validação do formulário
        document.getElementById('checklistForm').addEventListener('submit', function(e) {
            const requiredFields = ['promessa_principal', 'cliente_consciente', 'beneficios', 'mecanismo_unico'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element.value.trim()) {
                    element.classList.add('border-red-500');
                    isValid = false;
                } else {
                    element.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todas as perguntas obrigatórias.');
            }
        });
    </script>
</body>
</html> 