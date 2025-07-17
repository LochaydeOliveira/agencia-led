<?php
// Sistema de Login ValidaPro - Versão 2.0
require_once 'includes/auth.php';

// Iniciar sessão
initSession();

// Se já estiver logado, redireciona para o dashboard
if (isLoggedIn()) {
    if (!headers_sent()) {
        header('Location: index.php');
        exit();
    } else {
        echo '<script>window.location.href = "index.php";</script>';
        exit();
    }
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/db.php';
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validação básica
    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos!';
    } else {
    if (authenticateUser($email, $password)) {
            // Login bem-sucedido
        if (!headers_sent()) {
            header('Location: index.php');
            exit();
        } else {
            echo '<script>window.location.href = "index.php";</script>';
            exit();
        }
    } else {
        $error = 'E-mail ou senha incorretos!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ValidaPro</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon-oficial-validapro.png">
    <link rel="apple-touch-icon" href="assets/img/favicon-oficial-validapro.png">

    <!-- Logo SVG -->
    <link rel="preload" as="image" href="assets/svg/logo-valida-pro-em-svg.svg">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center">
    <!-- Elementos decorativos do background -->
    <div class="absolute inset-0 opacity-10 pointer-events-none z-0">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full"></div>
        <div class="absolute top-32 right-20 w-16 h-16 bg-white rounded-full"></div>
        <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-white rounded-full"></div>
    </div>
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <img src="assets/img/logo-validapro-checklist.svg" alt="ValidaPro Logo" class="h-16 mx-auto mb-4">
            <p class="text-gray-700">Faça login para acessar sua análise</p>
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
                       class="input-modern w-full"
                       placeholder="seu@email.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Senha
                </label>
                <input type="password" id="password" name="password" required
                       class="input-modern w-full"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full btn-cta py-3 px-4 rounded-lg text-lg flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i>Entrar
            </button>
            <div class="mt-4 text-center">
                <a href="recuperar_senha.php" class="text-sm text-orange-600 hover:text-orange-800 font-semibold transition">Esqueci minha senha?</a>
            </div>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Acesso restrito - credenciais enviadas por email
            </p>
        </div>
        <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <h4 class="font-semibold text-gray-700 mb-2">Debug Info:</h4>
            <p class="text-xs text-gray-600">Session Status: <?php echo session_status(); ?></p>
            <p class="text-xs text-gray-600">Session ID: <?php echo session_id(); ?></p>
            <p class="text-xs text-gray-600">Headers Sent: <?php echo headers_sent() ? 'Sim' : 'Não'; ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 