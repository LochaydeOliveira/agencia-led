<?php
// Iniciar sessão primeiro, antes de qualquer saída
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    if (!headers_sent()) {
        header('Location: dashboard.php');
        exit();
    } else {
        echo '<script>window.location.href = "dashboard.php";</script>';
        exit();
    }
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    require_once 'includes/auth.php';
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (authenticateUser($email, $password)) {
        if (!headers_sent()) {
            header('Location: dashboard.php');
            exit();
        } else {
            echo '<script>window.location.href = "dashboard.php";</script>';
            exit();
        }
    } else {
        $error = 'Email ou senha incorretos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ValidaPro - Login</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon-oficial-validapro.png">
    <link rel="apple-touch-icon" href="assets/img/favicon-oficial-validapro.png">

    <!-- Logo SVG -->
    <link rel="preload" as="image" href="assets/svg/logo-valida-pro-em-svg.svg">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <img src="assets/img/logo-validapro-checklist.svg" alt="ValidaPro Logo" class="h-16 mx-auto mb-4">
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
    </div>
</body>
</html> 