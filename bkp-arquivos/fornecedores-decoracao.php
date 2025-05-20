<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Verificação de Pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body, html {
            height: 100%;
            background: #f8f9fa;
        }

        .container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeIn 1s ease forwards;
        }

        .card {
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            background: #fff;
            max-width: 400px;
            width: 100%;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card text-center">
        <h2 class="mb-4">Baixar material adquirido</h2>
        <p class="mb-4">Insira o número do seu pedido:</p>
        <form action="verificar-codigo.php" method="POST" class="d-grid gap-3">
            <input 
                type="text" 
                name="numero_pedido" 
                placeholder="Ex: 339833756503811" 
                required 
                autofocus 
                pattern="\d{15}" 
                title="Digite exatamente 15 números"
                class="form-control form-control-lg"
            />
            <button type="submit" class="btn btn-primary btn-lg">Verificar e Baixar</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS (opcional, só se precisar de componentes JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
