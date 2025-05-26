<?php

require 'protect.php';
require '../conexao.php';

    $totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    $totalListas = $pdo->query("SELECT COUNT(*) FROM listas")->fetchColumn();
    $totalPedidos = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    // Gráfico de classificação
    $stmt = $pdo->query("SELECT classificacao, COUNT(*) as total FROM clientes GROUP BY classificacao");
    $classificacaoData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Gráfico de pedidos por dia (7 dias)
    $stmt = $pdo->query("SELECT DATE(created_at) as dia, COUNT(*) as total FROM orders GROUP BY dia ORDER BY dia DESC LIMIT 7");
    $pedidosData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pedidosData = array_reverse($pedidosData);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Admin - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex"> 
    <?php include 'partials/sidebar.php'; ?>    

    <main class="flex-grow-1 p-4" style="margin-top: 56px;">
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

      <div class="row">
        <div class="col-md-6">
          <div class="card shadow-sm p-3 mb-4">
            <h6>Classificação dos Clientes</h6>
            <canvas id="graficoClassificacao"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm p-3 mb-4">
            <h6>Pedidos por Dia</h6>
            <canvas id="graficoPedidos"></canvas>
          </div>
        </div>
      </div>
    </main>
</div>


<?php include 'partials/footer.php'; ?>

</body>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const ctxClassificacao = document.getElementById('graficoClassificacao');
    if (ctxClassificacao) {
      new Chart(ctxClassificacao, {
        type: 'doughnut',
        data: {
          labels: <?php echo json_encode(array_keys($classificacaoData)); ?>,
          datasets: [{
            label: 'Classificação',
            data: <?php echo json_encode(array_values($classificacaoData)); ?>,
            backgroundColor: ['#007bff', '#ffc107', '#28a745']
          }]
        }
      });
    }

    const ctxPedidos = document.getElementById('graficoPedidos');
    if (ctxPedidos) {
      new Chart(ctxPedidos, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode(array_column($pedidosData, 'dia')); ?>,
          datasets: [{
            label: 'Pedidos',
            data: <?php echo json_encode(array_column($pedidosData, 'total')); ?>,
            backgroundColor: '#6610f2'
          }]
        }
      });
    }
  });
</script>


</html>
