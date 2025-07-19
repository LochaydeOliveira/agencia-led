<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

$erro = '';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $erro = "Por favor, insira um e-mail válido.";
    } else {
        // Gera o token e envia o e-mail
        if (gerarTokenSenha($email)) {
            $mensagem = "Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.";
        } else {
            // Mesmo se o e-mail não estiver cadastrado, mostra a mesma mensagem
            $mensagem = "Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - ValidaPro</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Recuperar Senha</h4>

                    <?php if ($mensagem): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
                    <?php elseif ($erro): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="recuperar_senha.php">
                        <div class="form-group">
                            <label for="email">Seu e-mail cadastrado</label>
                            <input type="email" name="email" class="form-control" id="email" required placeholder="exemplo@dominio.com">
                        </div>
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary w-100">Enviar link de recuperação</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php">Voltar ao login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
