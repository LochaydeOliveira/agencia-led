<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
$usuario = htmlspecialchars($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área de Membros</title>
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
        .area-container {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .btn-logout {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
<div class="area-container animate__animated animate__fadeIn">
    <h2>Bem-vindo à Área de Membros</h2>
    <p>Olá, <strong><?php echo $usuario; ?></strong>! Você está logado.</p>
    <a href="logout.php" class="btn-logout">Sair</a>
</div>
</body>
</html>