<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$mensagem = '';
$tipo_mensagem = '';
$token_valido = false;
$user_id = null;

// Gerar CSRF token se não existir
if (!isset($_SESSION)) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verifica se o token é válido e não expirou
    $stmt = $pdo->prepare("SELECT user_id FROM recuperacao_senha WHERE token = ? AND expira > NOW() AND usado = 0");
    $stmt->execute([$token]);
    $res = $stmt->fetch();

    if ($res) {
        $token_valido = true;
        $user_id = $res['user_id'];
    } else {
        $mensagem = "Link inválido ou expirado. Por favor, solicite um novo link de recuperação.";
        $tipo_mensagem = "danger";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valido) {
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    $csrf_post = $_POST['csrf_token'] ?? '';

    if (empty($senha) || empty($confirmar)) {
        $mensagem = 'Preencha os dois campos de senha.';
        $tipo_mensagem = "danger";
    } elseif ($senha !== $confirmar) {
        $mensagem = 'As senhas não coincidem.';
        $tipo_mensagem = "danger";
    } elseif (strlen($senha) < 8) {
        $mensagem = 'A senha deve ter pelo menos 8 caracteres.';
        $tipo_mensagem = "danger";
    } elseif (empty($csrf_post) || !hash_equals($_SESSION['csrf_token'], $csrf_post)) {
        $mensagem = 'Token de segurança inválido. Recarregue a página.';
        $tipo_mensagem = "danger";
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $user_id]);

        // Marca o token como usado
        $stmt = $pdo->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE token = ?");
        $stmt->execute([$_GET['token']]);

        $mensagem = 'Senha redefinida com sucesso! <a href="login.php" class="text-orange-600 font-semibold">Clique aqui para entrar</a>';
        $tipo_mensagem = "success";
        $token_valido = false;
        unset($_SESSION['csrf_token']);
    }
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
        <?php if ($token_valido): ?>
        <form method="POST" class="space-y-6" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div>
                <label for="senha1" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Nova senha
                </label>
                <div class="relative">
                    <input type="password" id="senha1" name="senha" required class="input-modern w-full pr-12" placeholder="Digite a nova senha" oninput="checkStrength(this.value)" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('senha1')" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700"><i class="fas fa-eye"></i></button>
                </div>
                <div id="senha-strength" class="mt-2 text-xs font-semibold"></div>
            </div>
            <div>
                <label for="senha2" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Confirme a nova senha
                </label>
                <div class="relative">
                    <input type="password" id="senha2" name="confirmar_senha" required class="input-modern w-full pr-12" placeholder="Repita a nova senha" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('senha2')" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700"><i class="fas fa-eye"></i></button>
                </div>
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
    <script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
    function checkStrength(value) {
        const bar = document.getElementById('senha-strength');
        let strength = 0;
        if (value.length >= 8) strength++;
        if (/[A-Z]/.test(value)) strength++;
        if (/[a-z]/.test(value)) strength++;
        if (/[0-9]/.test(value)) strength++;
        if (/[^A-Za-z0-9]/.test(value)) strength++;
        let msg = '';
        let color = 'text-red-600';
        if (strength <= 2) { msg = 'Senha fraca'; color = 'text-red-600'; }
        else if (strength === 3) { msg = 'Senha razoável'; color = 'text-yellow-600'; }
        else if (strength >= 4) { msg = 'Senha forte'; color = 'text-green-600'; }
        bar.textContent = msg;
        bar.className = 'mt-2 text-xs font-semibold ' + color;
    }
    </script>
</body>
</html> 