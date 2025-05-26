<?php
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

require '../conexao.php';

$statusFiltro = $_GET['status'] ?? '';

$query = "SELECT order_number, customer_name, customer_email, product_id, status, created_at FROM orders";
$params = [];

if ($statusFiltro) {
    $query .= " WHERE status = ?";
    $params[] = $statusFiltro;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>   
  <meta charset="UTF-8">
  <title>Pedidos - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>
  <main class="container py-5 main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Pedidos</h2>
      <form method="get" class="d-flex gap-2">
        <select name="status" class="form-select">
          <option value="">Todos os Status</option>
          <option value="paid" <?= $statusFiltro === 'paid' ? 'selected' : '' ?>>Pago</option>
          <option value="pending" <?= $statusFiltro === 'pending' ? 'selected' : '' ?>>Pendente</option>
          <option value="cancelled" <?= $statusFiltro === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
          <option value="refused" <?= $statusFiltro === 'refused' ? 'selected' : '' ?>>Recusado</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Nº do Pedido</th>
            <th>Cliente</th>
            <th>Email</th>
            <th>Produto</th>
            <th>Status</th>
            <th>Data</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pedidos as $pedido): ?>
            <tr>
              <td><?= htmlspecialchars($pedido['order_number']) ?></td>
              <td><?= htmlspecialchars($pedido['customer_name']) ?></td>
              <td><?= htmlspecialchars($pedido['customer_email']) ?></td>
              <td><?= htmlspecialchars($pedido['product_id']) ?></td>
              <td><span class="badge bg-secondary"><?= $pedido['status'] ?></span></td>
              <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        </table>
      </div>
  </main>
</div>



</body>
</html>
