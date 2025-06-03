<?php
require 'protect.php';
require '../conexao.php';

$totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalListas = $pdo->query("SELECT COUNT(*) FROM listas")->fetchColumn();
$totalPedidos = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <div class="d-flex"> 
        <?php include 'partials/sidebar.php'; ?>    

        <main class="flex-grow-1 p-4">
            <h2 class="mb-4">Dashboard Administrativo</h2>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5>Total de Clientes</h5>
                        <p class="fs-3 fw-bold text-primary"><?php echo $totalClientes; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5>Total de Listas</h5>
                        <p class="fs-3 fw-bold text-success"><?php echo $totalListas; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5>Total de Pedidos</h5>
                        <p class="fs-3 fw-bold text-danger"><?php echo $totalPedidos; ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
