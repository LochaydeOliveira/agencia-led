<?php
require 'conexao.php';
require 'src/Mailer.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Verificar se o email existe
    $stmt = $pdo->prepare("SELECT id, nome FROM clientes WHERE email = ? AND status = 'ativo'");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        // Gerar token único
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Salvar token no banco
        $stmt = $pdo->prepare("INSERT INTO recuperacao_senha (cliente_id, token, expira) VALUES (?, ?, ?)");
        $stmt->execute([$cliente['id'], $token, $expira]);
        
        // Enviar email
        $mailer = new Mailer();
        $link = "https://agencialed.com/redefinir_senha.php?token=" . $token;
        
        try {
            if ($mailer->sendPasswordReset($email, $cliente['nome'], $link)) {
                $mensagem = "Enviamos um email com instruções para redefinir sua senha.";
                $tipo_mensagem = "success";
            } else {
                $mensagem = "Erro ao enviar email. Por favor, tente novamente.";
                $tipo_mensagem = "danger";
            }
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar email: " . $e->getMessage();
            $tipo_mensagem = "danger";
            app_log("Erro ao enviar email de recuperação: " . $e->getMessage(), 'error');
        }
    } else {
        $mensagem = "Email não encontrado ou conta inativa.";
        $tipo_mensagem = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - Área de Clientes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
        }
        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="form-container animate__animated animate__fadeIn">
    <h2 class="mb-4 text-center">Recuperar Senha</h2>
    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> animate__animated animate__fadeIn">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Digite seu e-mail" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-custom">Enviar Link de Recuperação</button>
        </div>
    </form>
    <div class="back-to-login">
        <a href="login.php" class="text-decoration-none">Voltar para o Login</a>
    </div>
</div>
</body>
</html> 