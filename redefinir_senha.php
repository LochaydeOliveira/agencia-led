<?php
require 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';
$token_valido = false;
$cliente_id = null;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verificar se o token é válido e não expirou
    $stmt = $pdo->prepare("SELECT cliente_id FROM recuperacao_senha WHERE token = ? AND expira > NOW() AND usado = 0");
    $stmt->execute([$token]);
    $resultado = $stmt->fetch();
    
    if ($resultado) {
        $token_valido = true;
        $cliente_id = $resultado['cliente_id'];
    } else {
        $mensagem = "Link inválido ou expirado. Por favor, solicite um novo link de recuperação.";
        $tipo_mensagem = "danger";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valido) {
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($senha === $confirmar_senha) {
        if (strlen($senha) >= 6) {
            // Atualizar senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE clientes SET senha = ? WHERE id = ?");
            $stmt->execute([$senha_hash, $cliente_id]);
            
            // Marcar token como usado
            $stmt = $pdo->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            $mensagem = "Senha atualizada com sucesso! Você já pode fazer login com sua nova senha.";
            $tipo_mensagem = "success";
            $token_valido = false; // Desabilita o formulário após sucesso
        } else {
            $mensagem = "A senha deve ter pelo menos 6 caracteres.";
            $tipo_mensagem = "danger";
        }
    } else {
        $mensagem = "As senhas não coincidem.";
        $tipo_mensagem = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - Área de Clientes</title>
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
    <h2 class="mb-4 text-center">Redefinir Senha</h2>
    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> animate__animated animate__fadeIn">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($token_valido): ?>
        <form method="post">
            <div class="mb-3">
                <input type="password" name="senha" class="form-control" placeholder="Nova senha" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar nova senha" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-custom">Atualizar Senha</button>
            </div>
        </form>
    <?php endif; ?>
    
    <div class="back-to-login">
        <a href="login.php" class="text-decoration-none">Voltar para o Login</a>
    </div>
</div>
</body>
</html> 