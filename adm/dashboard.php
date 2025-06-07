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
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets-admin/admin.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="assets-agencia-led/img/icone-favorito-led.png" type="image/png">
    <link rel="apple-touch-icon" href="assets-agencia-led/img/icone-favorito-led.png">
</head>

<body>

<?php include 'partials/header.php'; ?>

  <div class="d-flex">
      <?php include 'partials/sidebar.php'; ?>

      <main class="flex-grow-1 p-4 main-adm-content">
        <div class="row text-center">
          <div class="col-md-4">
            <div class="card bg-light p-4 mb-4">
              <h5>Total de Clientes</h5>
              <p class="fs-3 text-primary fw-bold"><?php echo $totalClientes; ?></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light p-4 mb-4">
              <h5>Total de Listas</h5>
              <p class="fs-3 text-success fw-bold"><?php echo $totalListas; ?></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light p-4 mb-4">
              <h5>Total de Pedidos</h5>
              <p class="fs-3 text-danger fw-bold"><?php echo $totalPedidos; ?></p>
            </div>
          </div>
        </div>

        <div class="text-center mt-4">
          <a href="clientes.php" class="btn btn-outline-primary mx-2">Gerenciar Clientes</a>
          <a href="listas.php" class="btn btn-outline-success mx-2">Gerenciar Listas</a>
          <a href="pedidos.php" class="btn btn-outline-dark mx-2">Visualizar Pedidos</a>
          <a href="log.php" class="btn btn-outline-warning mx-2">Ver Log do Webhook</a>
        </div>
      </main>

  </div>

<?php include 'partials/footer.php'; ?>


</body>
</html>
