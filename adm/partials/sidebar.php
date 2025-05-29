<style>
  .link-sidebar {
    color: #7b8190;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
  }
</style>

<aside class="bg-light border-end p-3" style="width: 250px; min-height: 100vh;">
  <h4 class="mb-4">Painel Admin</h4>
  <nav class="nav flex-column">
    <a href="index.php" class="nav-link mb-2 link-sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
        <path d="M8 3.293l6 6V15h-4v-4H6v4H2v-5.707l6-6z"/>
        <path d="M7 13h2v2H7v-2z"/>
      </svg>
      Dashboard
    </a>

    <a href="clientes.php" class="nav-link mb-2 link-sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z"/>
        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
      </svg>
      Clientes
    </a>

    <a href="listas.php" class="nav-link mb-2 link-sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 2.5 4h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 4.5z"/>
      </svg>
      Listas
    </a>

    <a href="pedidos.php" class="nav-link mb-2 link-sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
        <path d="M0 1a1 1 0 0 1 1-1h1.27a.5.5 0 0 1 .485.379L3.89 4H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.49-.402L1.61 1.607 1.27 1H1a1 1 0 0 1-1-1zm3.14 5l1.25 5.5H13l1.2-5.5H3.14z"/>
      </svg>
      Pedidos
    </a>

    <a href="log.php" class="nav-link mb-2 link-sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
        <path d="M3 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V6.5L9.5 2H3z"/>
        <path d="M9 2v4h4"/>
      </svg>
      Log
    </a>

    <hr>

    <a href="partials/logout.php" class="nav-link text-danger link-sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-danger" viewBox="0 0 16 16">
        <path d="M6 2a1 1 0 0 0-1 1v3h1V3h7v10H6v-3H5v3a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H6z"/>
        <path d="M0 8a.5.5 0 0 1 .5-.5H9.793l-2.147-2.146a.5.5 0 0 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 1 1-.708-.708L9.793 8.5H.5A.5.5 0 0 1 0 8z"/>
      </svg>
      Sair
    </a>
  </nav>
</aside>
