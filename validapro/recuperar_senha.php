<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $mensagem = 'Por favor, informe seu e-mail.';
    } elseif (!validateEmail($email)) {
        $mensagem = 'E-mail inválido.';
    } else {
        // Verificar se o e-mail existe
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Gerar token seguro
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 3600); // 1 hora
            // Salvar token no banco
            $pdo->prepare('UPDATE users SET reset_token = ?, reset_token_expira = ? WHERE id = ?')
                ->execute([$token, $expira, $user['id']]);
            // Enviar e-mail
            $link = APP_URL . 'redefinir_senha.php?token=' . $token;
            $assunto = 'Recuperação de Senha - ValidaPro';
            $corpo = "Olá!\n\nRecebemos uma solicitação para redefinir sua senha no ValidaPro.\nClique no link abaixo para criar uma nova senha:\n$link\n\nSe não foi você, ignore este e-mail.";
            // Função de envio de e-mail (ajuste conforme seu mailer)
            mail($email, $assunto, $corpo, 'From: ' . FROM_EMAIL);
            $mensagem = 'Enviamos um link de recuperação para seu e-mail.';
        } else {
            $mensagem = 'Se o e-mail estiver cadastrado, você receberá um link para redefinir a senha.';
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
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 text-center">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>E-mail
                </label>
                <input type="email" id="email" name="email" required class="input-modern w-full" placeholder="seu@email.com">
            </div>
            <button type="submit" class="w-full btn-cta py-3 px-4 rounded-lg text-lg flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i>Enviar link de recuperação
            </button>
        </form>
        <div class="mt-8 text-center">
            <a href="login.php" class="text-sm text-orange-600 hover:text-orange-800 font-semibold transition">Voltar ao login</a>
        </div>
    </div>
</body>
</html> 