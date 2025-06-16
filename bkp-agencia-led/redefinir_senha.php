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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="icon" href="assets-agencia-led/img/icone-favorito-led.png" type="image/png">
    <link rel="apple-touch-icon" href="assets-agencia-led/img/icone-favorito-led.png">
    
    
    <style>
        body {
            background: #f0f2f5; /* Fundo cinza claro similar ao da Yampi */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif; /* Usar a fonte Inter */
        }
        .form-container {
            background: #fff;
            border-radius: 8px; /* Bordas menos arredondadas */
            padding: 2rem; /* Ajusta o padding */
            box-shadow: 0 2px 10px rgba(0,0,0,0.08); /* Sombra mais suave */
            width: 100%;
            max-width: 380px; /* Largura máxima ajustada */
            text-align: center; /* Centraliza o conteúdo */
        }
        .logo-container {
            margin-bottom: 1.5rem;
        }
        .logo-container img {
            max-width: 85px; /* Ajuste o tamanho do logo se usar imagem */
            height: auto;
        }
        .form-title {
            font-size: 1.8rem; /* Tamanho do título */
            font-weight: 600; /* Peso da fonte */
            color: #333; /* Cor do texto */
            margin-bottom: 0.5rem; /* Espaço abaixo do título */
        }
        .form-subtitle {
            font-size: 1rem; /* Tamanho do subtítulo */
            color: #666; /* Cor do texto */
            margin-bottom: 2rem; /* Espaço abaixo do subtítulo */
        }
        .form-control {
            border-radius: 4px; /* Bordas arredondadas para inputs */
            padding: 0.75rem 1rem; /* Padding ajustado */
            border: 1px solid #ccc; /* Cor da borda */
        }
        .form-control:focus {
            border-color: #007bff; /* Cor da borda no foco */
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25); /* Sombra no foco */
        }
        .btn-custom {
            background-color: #000;
            border: none;
            color: white;
            padding: 9px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 4px;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-custom:hover {
            background-color: #333; /* Cor no hover */
            color: white;
        }
         .link-back-login {
            display: block;
            margin-top: 1.5rem; /* Espaço acima do link */
            font-size: 0.9rem; /* Tamanho da fonte */
            color: #666; /* Cor do texto */
            text-decoration: none; /* Sem sublinhado */
        }
        .link-back-login a {
             color: #007bff; /* Cor do link */
             text-decoration: none; /* Sem sublinhado */
             font-weight: 600; /* Peso da fonte */
        }
        .link-back-login a:hover {
             text-decoration: underline; /* Sublinhado no hover */
        }


    </style>
</head>
<body>
<div class="form-container animate__animated animate__fadeIn">
     <div class="logo-container">
        <img width="100" height="60" src="assets-agencia-led/img/logo-led-preta.png" alt="logo oficial led - formulários">
    </div>
    <h2 class="form-title">Redefinir Senha</h2>
    <p class="form-subtitle">Digite e confirme sua nova senha.</p>
    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> animate__animated animate__fadeIn">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($token_valido): ?>
        <form method="post">
            <div class="mb-3">
                <label for="senha" class="form-label" style="text-align: left; display: block;">Nova senha</label>
                <input type="password" id="senha" name="senha" class="form-control" placeholder="" required>
            </div>
            <div class="mb-3">
                 <label for="confirmar_senha" class="form-label" style="text-align: left; display: block;">Confirmar nova senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" placeholder="" required>
            </div>
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom">Atualizar Senha</button>
            </div>
        </form>
    <?php endif; ?>
    
    <div class="link-back-login">
        <a href="login.php">Voltar para o Login</a>
    </div>
</div>
</body>
</html> 