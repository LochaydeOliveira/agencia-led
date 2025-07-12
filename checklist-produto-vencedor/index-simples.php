<?php
session_start();

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard-simples.php');
    exit();
}

// Usuários de teste (sem banco de dados)
$usuarios_teste = [
    'admin@exemplo.com' => ['senha' => '123456', 'nome' => 'Administrador'],
    'teste@teste.com' => ['senha' => '123456', 'nome' => 'Usuário Teste'],
    'user@user.com' => ['senha' => '123456', 'nome' => 'Usuário Padrão']
];

// Processar login (versão simplificada)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Verificar se o usuário existe
    if (isset($usuarios_teste[$email]) && $usuarios_teste[$email]['senha'] === $password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $usuarios_teste[$email]['nome'];
        header('Location: dashboard-simples.php');
        exit();
    } else {
        $error = 'Email ou senha incorretos! Use um dos usuários de teste abaixo.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist do Produto Lucrativo - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Checklist do Produto</h1>
            <p class="text-gray-600">Faça login para acessar sua análise</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input type="email" id="email" name="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="seu@email.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Senha
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition duration-200 transform hover:scale-105">
                <i class="fas fa-sign-in-alt mr-2"></i>Entrar
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Acesso restrito - credenciais enviadas por email
            </p>
        </div>
        
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-xs text-blue-800 font-semibold mb-2">
                <i class="fas fa-users mr-1"></i>Usuários de Teste:
            </p>
            <div class="text-xs text-blue-700 space-y-1">
                <div><strong>admin@exemplo.com</strong> / 123456</div>
                <div><strong>teste@teste.com</strong> / 123456</div>
                <div><strong>user@user.com</strong> / 123456</div>
            </div>
        </div>
    </div>
</body>
</html> 