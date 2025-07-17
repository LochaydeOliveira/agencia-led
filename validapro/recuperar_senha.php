<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/mailer.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $mensagem = 'Por favor, informe seu e-mail.';
    } elseif (!validateEmail($email)) {
        $mensagem = 'E-mail inválido.';
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
            // Enviar e-mail com PHPMailer
            $link = APP_URL . 'redefinir_senha.php?token=' . $token;
            $assunto = 'Recuperação de Senha - Valida Pro';
            $corpo = "<html><body style='font-family: Arial, sans-serif; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background: linear-gradient(135deg, #f97316 0%, #ec4899 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                        <h1 style='margin: 0; font-size: 28px;'>Valida Pro</h1>
                    </div>
                    <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                        <h2 style='color: #2c3e50; margin-top: 0;'>Recuperação de Senha</h2>
                        <p>Olá, <strong>{$user['name']}</strong>!</p>
                        <p>Recebemos uma solicitação para redefinir sua senha no <strong>Valida Pro</strong>.</p>
                        <p>Para criar uma nova senha, clique no botão abaixo:</p>
                        <div style='margin: 30px 0; text-align: center;'>
                            <a href='$link' style='background: linear-gradient(135deg, #f97316 0%, #ec4899 100%); color: white; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-size: 18px; font-weight: bold;'>Redefinir Senha</a>
                        </div>
                        <p>Ou copie e cole este link no navegador:<br><a href='$link'>$link</a></p>
                        <p style='color: #888; font-size: 13px;'>Se você não solicitou, apenas ignore este e-mail.</p>
                    </div>
                    <div style='text-align: center; margin-top: 20px; color: #6c757d; font-size: 12px;'>
                        <p>Este é um email automático. Não responda a esta mensagem.</p>
                    </div>
                </div>
            </body></html>";
            $enviado = sendEmailWithPHPMailer($email, $user['name'], $assunto, $corpo);
            if ($enviado) {
                $mensagem = 'Enviamos um link de recuperação para seu e-mail. Confira também a caixa de spam.';
            } else {
                $mensagem = 'Não foi possível enviar o e-mail de recuperação. Tente novamente mais tarde.';
            }
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