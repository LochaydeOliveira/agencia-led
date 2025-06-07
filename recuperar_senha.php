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
    <h2 class="form-title">Recuperar Senha</h2>
    <p class="form-subtitle">Digite seu e-mail para recuperar sua senha.</p>
    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> animate__animated animate__fadeIn">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
             <label for="email" class="form-label" style="text-align: left; display: block;">E-mail</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="" required>
        </div>
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-custom">Enviar Link de Recuperação</button>
        </div>
    </form>
     <div class="link-back-login">
        <a href="login.php">Voltar para o Login</a>
    </div>
</div>
</body>
</html> 