<?php
// Ativar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir arquivos necessários
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Verificar login
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teste - Checklist do Produto</title>
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
                    <h1 class="text-xl font-bold text-gray-800">Checklist do Produto - TESTE</h1>
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
                <h2 class="text-3xl font-bold text-gray-800 mb-2">✅ Dashboard Funcionando!</h2>
                <p class="text-gray-600">Se você está vendo esta página, o dashboard está funcionando</p>
            </div>

            <!-- Informações do Usuário -->
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <h3 class="text-xl font-semibold text-blue-800 mb-4">
                    <i class="fas fa-user mr-2"></i>
                    Informações do Usuário
                </h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome:</label>
                        <p class="text-gray-900 font-semibold"><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email:</label>
                        <p class="text-gray-900 font-semibold"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID do Usuário:</label>
                        <p class="text-gray-900 font-semibold"><?php echo htmlspecialchars($user['id']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sessão ID:</label>
                        <p class="text-gray-900 font-semibold"><?php echo session_id(); ?></p>
                    </div>
                </div>
            </div>

            <!-- Links de Teste -->
            <div class="grid md:grid-cols-2 gap-4">
                <a href="dashboard.php" class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Ir para Dashboard Original
                </a>
                <a href="teste-dashboard.php" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition duration-200">
                    <i class="fas fa-bug mr-2"></i>
                    Teste de Debug
                </a>
            </div>

            <!-- Status do Sistema -->
            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-green-800 font-semibold mb-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    Status do Sistema
                </h4>
                <ul class="text-green-700 text-sm space-y-1">
                    <li>✅ Conexão com banco de dados: OK</li>
                    <li>✅ Autenticação: OK</li>
                    <li>✅ Sessão: OK</li>
                    <li>✅ Usuário logado: OK</li>
                    <li>✅ Interface: OK</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html> 