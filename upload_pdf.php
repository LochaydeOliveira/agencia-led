<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id_ymp = $_POST['product_id_ymp'] ?? '';
    $nome_arquivo = $_FILES['pdf_file']['name'] ?? '';

    if (empty($product_id_ymp) || empty($nome_arquivo)) {
        $error = "Preencha todos os campos.";
    } else {
        $upload_dir = __DIR__ . '/files/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $caminho_relativo = '/files/' . basename($nome_arquivo);
        $caminho_completo = $upload_dir . basename($nome_arquivo);

        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $caminho_completo)) {
            try {
                $db = Database::getInstance();
                $conn = $db->getConnection();

                $sql = "INSERT INTO arquivos_pdf (product_id_ymp, nome, caminho) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$product_id_ymp, $nome_arquivo, $caminho_relativo]);

                $success = "Arquivo cadastrado com sucesso!";
            } catch (Exception $e) {
                $error = "Erro ao cadastrar arquivo: " . $e->getMessage();
            }
        } else {
            $error = "Erro ao enviar arquivo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Arquivo PDF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="assets-agencia-led/img/icone-favorito-agencia-led.png" type="image/png">

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
            box-shadow: 0 10px 30px rgb(0 0 0 / .1);
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            margin: 2rem;
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgb(0 0 0 / .15);
        }

        .form-control {
            border-radius: 10px;
            padding: .8rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6c757d;
            box-shadow: 0 0 0 .2rem rgb(108 117 125 / .25);
        }

        .btn-primary {
            border-radius: 10px;
            padding: .8rem 2rem;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgb(0 0 0 / .2);
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="form-container animate__animated animate__fadeIn">

    <div class="text-center mb-4">
        <h2>Cadastro de Arquivo PDF</h2>
        <p class="text-muted">Associe um PDF ao produto da Yampi</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__shakeX">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success animate__animated animate__fadeIn">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="animate__animated animate__fadeIn">

        <div class="mb-3">
            <label for="product_id_ymp" class="form-label">Product ID (Yampi)</label>
            <input type="text" name="product_id_ymp" class="form-control" id="product_id_ymp" required>
        </div>

        <div class="mb-3">
            <label for="pdf_file" class="form-label">Arquivo PDF</label>
            <input type="file" name="pdf_file" class="form-control" id="pdf_file" accept="application/pdf" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
