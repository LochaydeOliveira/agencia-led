<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$mensagem = '';
$token = $_GET['token'] ?? '';
$tokenValido = false;

if ($token) {
    $stmt = $pdo->prepare('SELECT id, reset_token_expira FROM users WHERE reset_token = ? AND reset_token IS NOT NULL');
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && strtotime($user['reset_token_expira']) > time()) {
        $tokenValido = true;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senha1 = $_POST['senha1'] ?? '';
            $senha2 = $_POST['senha2'] ?? '';
            if (empty($senha1) || empty($senha2)) {
                $mensagem = 'Preencha os dois campos de senha.';
            } elseif ($senha1 !== $senha2) {
                $mensagem = 'As senhas não coincidem.';
            } elseif (strlen($senha1) < 8) {
                $mensagem = 'A senha deve ter pelo menos 8 caracteres.';
            } else {
                $hash = password_hash($senha1, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_token_expira = NULL WHERE id = ?')
                    ->execute([$hash, $user['id']]);
                $mensagem = 'Senha redefinida com sucesso! <a href="login.php" class="text-orange-600 font-semibold">Clique aqui para entrar</a>';
                $tokenValido = false;
            }
        }
    } else {
        $mensagem = 'Token inválido ou expirado. Solicite uma nova recuperação de senha.';
    }
} else {
    $mensagem = 'Token de redefinição ausente.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - ValidaPro</title>
    <link href="assets/css/custom.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center">
    <div class="bg-white bg-opacity-90 rounded-2xl shadow-2xl p-8 w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <img src="assets/img/logo-validapro-checklist.svg" alt="ValidaPro Logo" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold title-gradient mb-2">Redefinir Senha</h1>
            <p class="text-gray-700">Escolha uma nova senha para sua conta.</p>
        </div>
        <?php if ($mensagem): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 text-center">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        <?php if ($tokenValido): ?>
        <form method="POST" class="space-y-6">
            <div>
                <label for="senha1" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Nova senha
                </label>
                <input type="password" id="senha1" name="senha1" required class="input-modern w-full" placeholder="Digite a nova senha">
            </div>
            <div>
                <label for="senha2" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Confirme a nova senha
                </label>
                <input type="password" id="senha2" name="senha2" required class="input-modern w-full" placeholder="Repita a nova senha">
            </div>
            <button type="submit" class="w-full btn-cta py-3 px-4 rounded-lg text-lg flex items-center justify-center gap-2">
                <i class="fas fa-key"></i>Redefinir senha
            </button>
        </form>
        <?php endif; ?>
        <div class="mt-8 text-center">
            <a href="login.php" class="text-sm text-orange-600 hover:text-orange-800 font-semibold transition">Voltar ao login</a>
        </div>
    </div>
</body>
</html> 