<?php
require 'conexao.php';
$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    
    // Verificar se o email já existe (Opcional, mas recomendado)
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE email = ?");
    $stmt_check->execute([$email]);
    $email_exists = $stmt_check->fetchColumn();

    if ($email_exists) {
        $mensagem = "Este e-mail já está cadastrado.";
        $tipo_mensagem = "danger";
    } else {
         // Inserir na tabela clientes (assumindo que é onde os usuários da área de membros são salvos)
        // Adapte os campos conforme sua tabela `clientes`
        $stmt = $pdo->prepare("INSERT INTO clientes (email, senha, status, classificacao) VALUES (?, ?, ?, ?)");
        // Defina status e classificacao padrão para novos cadastros, se necessário
        $status_padrao = 'ativo'; // ou outro status inicial
        $classificacao_padrao = 'bronze'; // ou outra classificação inicial
        
        if ($stmt->execute([$email, $senha, $status_padrao, $classificacao_padrao])) {
            $mensagem = "Cadastro realizado com sucesso! Você já pode fazer login.";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "Erro ao cadastrar.";
            $tipo_mensagem = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Área de Clientes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
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
    <h2 class="form-title">Cadastro</h2>
    <p class="form-subtitle">Crie sua conta para acessar.</p>
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
        <div class="mb-3">
             <label for="senha" class="form-label" style="text-align: left; display: block;">Senha</label>
            <input type="password" id="senha" name="senha" class="form-control" placeholder="" required>
        </div>
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-custom">Cadastrar</button>
        </div>
    </form>
     <div class="link-back-login">
        Já tem uma conta? <a href="login.php">Fazer Login</a>
    </div>
</div>
</body>
</html>