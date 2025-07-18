<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carregar sistema completo do ValidaPro
require_once 'includes/init.php';

// Configurar headers de segurança
setupValidaProSecurityHeaders();

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $mensagem = 'Por favor, informe seu e-mail.';
        $tipo_mensagem = 'error';
    } elseif (!validateEmail($email)) {
        $mensagem = 'E-mail inválido.';
        $tipo_mensagem = 'error';
    } else {
        // Buscar usuário ativo
        $stmt = $pdo->prepare('SELECT id, name, usuario FROM users WHERE email = ? AND active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Gerar token seguro
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600); // 1 hora
            
            // Salvar token e expiração na tabela users
            $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_token_expira = ? WHERE id = ?');
            $stmt->execute([$token, $expira, $user['id']]);
            
            // Enviar e-mail usando a nova função
            $enviado = sendPasswordRecoveryEmail($email, $user['name'], $token);
            
            if ($enviado) {
                $mensagem = 'Enviamos um link de recuperação para seu e-mail. Confira também a caixa de spam.';
                $tipo_mensagem = 'success';
            } else {
                $mensagem = 'Não foi possível enviar o e-mail de recuperação. Tente novamente mais tarde.';
                $tipo_mensagem = 'error';
            }
        } else {
            // Por segurança, não informamos se o email existe ou não
            $mensagem = 'Se o e-mail estiver cadastrado, você receberá um link para redefinir a senha.';
            $tipo_mensagem = 'info';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - ValidaPro</title>
    <link href="assets/css/custom.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center">
    <div class="bg-white bg-opacity-90 rounded-2xl shadow-2xl p-8 w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <img src="assets/img/logo-validapro-checklist.svg" alt="ValidaPro Logo" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold title-gradient mb-2">Recuperar Senha</h1>
            <p class="text-gray-700">Informe seu e-mail para receber o link de redefinição.</p>
        </div>
        
        <?php if ($mensagem): ?>
            <div class="px-4 py-3 rounded mb-4 text-center <?php 
                echo $tipo_mensagem === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 
                    ($tipo_mensagem === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
                    'bg-blue-100 border border-blue-400 text-blue-700'); 
            ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>E-mail
                </label>
                <input type="email" id="email" name="email" required 
                       class="input-modern w-full" 
                       placeholder="seu@email.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="w-full btn-cta py-3 px-4 rounded-lg text-lg flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i>Enviar link de recuperação
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <a href="login.php" class="text-sm text-orange-600 hover:text-orange-800 font-semibold transition">
                ← Voltar ao login
            </a>
        </div>
    </div>
</body>
</html> 