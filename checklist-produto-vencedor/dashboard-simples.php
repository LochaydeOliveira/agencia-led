<?php
session_start();

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: index-simples.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Checklist do Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">Checklist do Produto</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Olá, <?php echo htmlspecialchars($user_name); ?></span>
                    <a href="logout.php" class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tachometer-alt mr-2 text-blue-600"></i>
                Dashboard
            </h2>
            <p class="text-gray-600 mb-4">
                Bem-vindo ao sistema de análise de produtos! Aqui você pode avaliar se seu produto tem potencial de sucesso.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check text-blue-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-blue-800">Análise Completa</h3>
                            <p class="text-sm text-blue-600">Avalie seu produto</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar text-green-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-green-800">Resultados</h3>
                            <p class="text-sm text-green-600">Veja suas análises</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-user-cog text-purple-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-purple-800">Perfil</h3>
                            <p class="text-sm text-purple-600">Gerencie sua conta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Usuário -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2 text-gray-600"></i>
                Informações da Conta
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome:</label>
                    <p class="text-gray-900"><?php echo htmlspecialchars($user_name); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email:</label>
                    <p class="text-gray-900"><?php echo htmlspecialchars($user_email); ?></p>
                </div>
            </div>
        </div>

        <!-- Botão para Dashboard Original -->
        <div class="mt-6 text-center">
            <a href="dashboard.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-arrow-right mr-2"></i>
                Ir para Dashboard Completo
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-sm text-gray-500">
                © 2024 Checklist do Produto Vencedor - Versão de Teste
            </p>
        </div>
    </footer>
</body>
</html> 