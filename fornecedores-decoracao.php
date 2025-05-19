<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Verificação de Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 50px;
            text-align: center;
        }
        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }
        input[type="submit"] {
            padding: 10px 25px;
            background-color: #007BFF;
            color: white;
            border: none;
            margin-top: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Baixar material adquirido</h2>
    <p>Insira o número do seu pedido:</p>
    <form action="verificar-codigo.php" method="POST">
        <input type="text" name="numero_pedido" placeholder="Ex: 339833756503811" required>
        <br>
        <input type="submit" value="Verificar e Baixar">
    </form>
</body>
</html>
