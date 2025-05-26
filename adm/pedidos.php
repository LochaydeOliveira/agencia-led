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
</head>

<body>
    
<?php include 'adm/partials/sidebar.php'; ?>
<?php include 'adm/partials/header.php'; ?>

<header>
  <nav class="navbar navbar-expand-lg shadow-bg container">
    <div id="iconUser" class="content-user">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30">
        <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm-4,21.164v-.164c0-2.206,1.794-4,4-4s4,1.794,4,4v.164c-1.226.537-2.578.836-4,.836s-2.774-.299-4-.836Zm9.925-1.113c-.456-2.859-2.939-5.051-5.925-5.051s-5.468,2.192-5.925,5.051c-2.47-1.823-4.075-4.753-4.075-8.051C2,6.486,6.486,2,12,2s10,4.486,10,10c0,3.298-1.605,6.228-4.075,8.051Zm-5.925-15.051c-2.206,0-4,1.794-4,4s1.794,4,4,4,4-1.794,4-4-1.794-4-4-4Zm0,6c-1.103,0-2-.897-2-2s.897-2,2-2,2,.897,2,2-.897,2-2,2Z"/>
      </svg>
      <div>
        <p class="mb-0">Olá, <strong>Admin</strong>!</p>
      </div>
    </div>
  </nav>
</header>

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



</body>
</html>
