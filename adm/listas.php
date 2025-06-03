<?php

require 'protect.php';
require '../conexao.php';

// Processar exclusão
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $pdo->prepare("DELETE FROM listas WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: listas.php?msg=deleted');
    exit;
}

// Processar edição
if (isset($_POST['edit']) && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_STRING);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $link_de_compra = filter_input(INPUT_POST, 'link_de_compra', FILTER_SANITIZE_URL);

    $stmt = $pdo->prepare("UPDATE listas SET product_id = ?, nome = ?, descricao = ?, preco = ?, link_de_compra = ? WHERE id = ?");
    $stmt->execute([$product_id, $nome, $descricao, $preco, $link_de_compra, $id]);
    header('Location: listas.php?msg=updated');
    exit;
}

// Busca
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Construir query base
$query = "SELECT id, product_id, nome, descricao, preco, link_de_compra FROM listas WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (nome LIKE ? OR product_id LIKE ? OR link_de_compra LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

$query .= " ORDER BY id DESC";

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Total de registros
$count_query = str_replace("id, product_id, nome, descricao, preco, link_de_compra", "COUNT(*)", $query);
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

// Buscar registros paginados
$query .= " LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$listas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">


<head>
  <meta charset="UTF-8">
  <title>Listas - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets-admin/admin.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 main-adm-content"> 
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listas Cadastradas</h2>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addListaModal">
          Nova Lista
        </button>
      </div>

      <!-- Filtros -->
      <div class="card mb-4">
        <div class="card-body">
          <form method="GET" class="row g-3">
            <div class="col-md-10">
              <input type="text" class="form-control" name="search" placeholder="Buscar por nome, ID do produto ou link..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>ID Produto</th>
              <th>Nome</th>
              <th>Descrição</th>
              <th>Preço</th>
              <th>Link de Compra</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($listas as $lista): ?>
              <tr>
                <td><?= htmlspecialchars($lista['product_id']) ?></td>
                <td><?= htmlspecialchars($lista['nome']) ?></td>
                <td><?= htmlspecialchars($lista['descricao']) ?></td>
                <td>R$ <?= number_format($lista['preco'], 2, ',', '.') ?></td>
                <td>
                  <a href="<?= htmlspecialchars($lista['link_de_compra']) ?>" target="_blank" class="btn btn-sm btn-link">
                    Ver Link
                  </a>
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" onclick="editLista(<?= htmlspecialchars(json_encode($lista)) ?>)">
                    Editar
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="deleteLista(<?= $lista['id'] ?>)">
                    Excluir
                  </button>
                  <a href="ver-html.php?id=<?= $lista['id'] ?>" class="btn btn-sm btn-secondary">
                    Ver HTML
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </main>

</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editListaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Lista</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="mb-3">
            <label class="form-label">ID do Produto</label>
            <input type="text" class="form-control" name="product_id" id="edit_product_id" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" id="edit_nome" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" id="edit_descricao" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" name="preco" id="edit_preco" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Link de Compra</label>
            <input type="url" class="form-control" name="link_de_compra" id="edit_link_de_compra" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="edit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Adição -->
<div class="modal fade" id="addListaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nova Lista</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="add_lista.php">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">ID do Produto</label>
            <input type="text" class="form-control" name="product_id" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" name="preco" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Link de Compra</label>
            <input type="url" class="form-control" name="link_de_compra" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editLista(lista) {
  document.getElementById('edit_id').value = lista.id;
  document.getElementById('edit_product_id').value = lista.product_id;
  document.getElementById('edit_nome').value = lista.nome;
  document.getElementById('edit_descricao').value = lista.descricao;
  document.getElementById('edit_preco').value = lista.preco;
  document.getElementById('edit_link_de_compra').value = lista.link_de_compra;
  
  new bootstrap.Modal(document.getElementById('editListaModal')).show();
}

function deleteLista(id) {
  Swal.fire({
    title: 'Tem certeza?',
    text: "Esta ação não poderá ser revertida!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sim, excluir!',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.innerHTML = `
        <input type="hidden" name="delete" value="1">
        <input type="hidden" name="id" value="${id}">
      `;
      document.body.appendChild(form);
      form.submit();
    }
  });
}

// Mostrar mensagens de sucesso/erro
<?php if (isset($_GET['msg'])): ?>
  Swal.fire({
    icon: '<?= $_GET['msg'] === 'deleted' ? 'success' : ($_GET['msg'] === 'added' ? 'success' : 'success') ?>',
    title: '<?= $_GET['msg'] === 'deleted' ? 'Lista excluída com sucesso!' : ($_GET['msg'] === 'added' ? 'Lista adicionada com sucesso!' : 'Lista atualizada com sucesso!') ?>',
    showConfirmButton: false,
    timer: 1500
  });
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
  Swal.fire({
    icon: 'error',
    title: 'Erro!',
    text: 'Ocorreu um erro ao processar sua solicitação.',
    showConfirmButton: false,
    timer: 1500
  });
<?php endif; ?>
</script>

</body>
</html>

