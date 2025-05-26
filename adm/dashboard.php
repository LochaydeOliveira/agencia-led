<?php
// session_start();
// // Login simples para admin - pode ser substituído por verificação de sessão depois
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

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
</head>

<body>

<?php include 'partials/sidebar.php'; ?>
<?php include 'partials/header.php'; ?>

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


</body>
</html>
