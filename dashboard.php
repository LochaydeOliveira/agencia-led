<?php
include '../conexao.php';
include '../header.html';

// Total de clientes
$clientes = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM clientes"));
$listas = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM listas"));
$pedidos = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM orders"));
?>
<div class="container mt-5">
  <h2 class="mb-4">Painel Administrativo</h2>

  <div class="row">
    <div class="col-md-4">
      <div class="card bg-light p-3 mb-4">
        <h5>Total de Clientes</h5>
        <p class="fs-3"><?= $clientes['total'] ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-light p-3 mb-4">
        <h5>Total de Listas</h5>
        <p class="fs-3"><?= $listas['total'] ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-light p-3 mb-4">
        <h5>Total de Pedidos</h5>
        <p class="fs-3"><?= $pedidos['total'] ?></p>
      </div>
    </div>
  </div>
</div>

<?php include '../footer.html'; ?>
