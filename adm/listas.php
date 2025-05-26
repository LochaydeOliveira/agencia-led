<?php
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

require '../conexao.php';

$stmt = $pdo->query("SELECT id, nome, preco, link_de_compra, criado_em FROM listas ORDER BY criado_em DESC");
$listas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>

  <meta charset="UTF-8">
  <title>Listas - Painel Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets-agencia-led/style.css" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
    }

    /* Para celulares (largura entre 481px e 768px) */
    @media (max-width: 480px) {
        
        .nav-mobilecollapse {
            display: block;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-user h2 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
        }

        .content-user {
            color: #000000;
            font-size: 15px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
        .content-user p {
            margin: 0;
        }

        .content-busca {
            display: flex;
            align-items: center;
            gap: 2rem;
            width: 100%;
            margin: 1rem 0 0;
            justify-content: center;
        }

        .content-busca input {
            background: #ededed;
        }

        .form-control { 
            border: none;
            padding: 10px;
        }

        .form-select {
            border: 0;
            padding: 8px;
            color: #0000008c;
            width: 90%;
            cursor: pointer;
        }


        header {
            padding: 20px 20px 5px;
            text-align: left;
            background: #fff;
            color: #285d9f;
            width: 100%;
            /* position: fixed;
            top: 0;
            z-index: 9; */
        }

        .main-content {
            margin: 1em 0;
            padding: 8px 2rem;
        }

        .main-content h1 {
            margin: 0 !important;
            font-size: 30px;
            font-weight: 700;
            color: #2862a3;
        }

        .main-content p {
            margin: 0;
            font-size: 13px;
            color: #000;
            text-align: center;
            line-height: 14px;
        }
        .tt-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 7rem 0 0;
        }

        .main-fornecedores {
            display: flex;
            justify-content: center;
        }

        .style-categoris-btn {
            border: none;
            color: #8f8f8f;
            font-size: 15px;
            margin: 10px 0 0;
        }
        #menuHamburgerIcon {
            border: none!important;
        }
        .btn-logout-desktop {
            display: none!important;
        }

        #logoutMobile {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
            width: 100%;
            color: red;
            font-size: 12px;
            text-decoration: none;
        }

        .nav-desktop {
            display: none!important;
        }
        .nav-mobile {
            display: block;
            margin: 1rem 0 0;
        }

        .nav-mobile ul {
            margin-left: 0!important;
        }


    }    

    /* Para celulares (largura entre 481px e 768px) */
    @media (min-width: 481px) and (max-width: 768px) {
        #logoutMobile {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
            width: 100%;
            color: red;
            font-size: 12px;
            text-decoration: none;
        }

        .nav-mobile {
            display: block;
        }

        .nav-mobile ul {
            margin-left: 0!important;
        }
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-user h2 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
        }

        .content-user {
            color: #000000;
            font-size: 15px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
        .content-user p {
            margin: 0;
        }

        .content-busca {
            display: flex;
            align-items: center;
            gap: 2rem;
            width: 100%;
            margin: 1rem 0 0;
            justify-content: center;
        }

        .content-busca input {
            background: #ededed;
        }

        .form-control { 
            border: none;
            padding: 10px;
        }

        .form-select {
            border: 0;
            padding: 8px;
            color: #0000008c;
            width: 25%;
            cursor: pointer;
        }

        .main-content {
            margin: 1em 0;
            padding: 8px 2rem;
        }

        .main-content h1 {
            margin: 0 !important;
            font-size: 30px;
            font-weight: 700;
            color: #2862a3;
        }

        .main-content p {
            margin: 0;
            font-size: 15px;
            color: #9590ad;
        }
        .tt-list {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-fornecedores {
            display: flex;
            justify-content: center;
        }

        .style-categoris-btn {
            border: none;
            color: #8f8f8f;
            font-size: 15px;
            margin: 10px 0 0;
        }
        #menuHamburgerIcon {
            border: none!important;
        }
        .btn-logout-desktop {
            display: none!important;
        }
        .btn-logout-mobile {
            display: flex!important;
            color: #000;
            text-decoration: none;
            font-size: 15px;
        }
        .nav-desktop {
            display: none!important;
        }

    }

    /* Para tablets (largura entre 769px e 1024px) */
    @media (min-width: 769px) {
        #navbarNavMobile {
            display: none !important;
        }

        .collapse.navbar-collapse.nav-mobile {
            display: none!important;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-user h2 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
        }

        .content-user {
            color: #000000;
            font-size: 15px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
            width: 100%;
            max-width: 265px;
        }
        .content-user p {
            margin: 0;
        }

        .content-busca {
            display: flex;
            align-items: center;
            gap: 2rem;
            width: 100%;
            margin: 0 25px 0 0;
            justify-content: flex-end;
        }

        .content-busca input {
            background: #ededed;
            max-width: 450px;
        }

        .form-control { 
            border: none;
            padding: 10px;
        }

        .form-select {
            border: 0;
            padding: 8px;
            color: #0000008c;
            width: 30%;
            cursor: pointer;s
        }


        header {
            padding: 20px 20px 5px;             
        }

        .main-content {
            margin: 6em 0;
            padding: 8px 2rem;
        }

        .main-content h1 {
            margin: 0 !important;
            font-size: 30px;
            font-weight: 700;
            color: #2862a3;
        }

        .main-content p {
            margin: 0;
            font-size: 13px;
            color: #7878788f;
            text-align: center;
            line-height: 14px;
        }
        .tt-list {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-fornecedores {
            display: flex;
            justify-content: center;
        }

        .style-categoris-btn {
            border: none;
            color: #8f8f8f;
            font-size: 15px;
            margin: 0;
        }
        #menuHamburgerIcon {
            border: none!important;
        }
        .btn-logout-desktop {
            display: inline-flex!important;
            color: #000;
            text-decoration: none;
            font-size: 15px;
        }
        .btn-logout-mobile {
            display: none!important;
        }
        .nav-desktop {
            display: flex!important;
        }
    }

  /* estilos gerais */

    .btn-comprar-lista {
        background: none;
        margin: 1rem 0 0;
        color: #000000;
        font-size: 14px;
        width: 100%;
        padding: 0.75rem 0;
        text-transform: uppercase;
        transition: background-color 0.3s ease, transform 0.3s ease;
        font-weight: 600;
        text-decoration: underline;
        border: 1px solid;
    }    

    .btn-comprar-lista:hover {
        background: #198754;
        color: #fff;
        border: none;
        text-decoration: none;
    }  



        #navbarNavMobile {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.4s ease;
            display: block;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-family: 'Inter', sans-serif!important;
        }
        .area-container {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 30px rgb(0 0 0 / .1);
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-link-custom {
            transition: background-color 0.3s ease, transform 0.3s ease;
            font-size: 15px;
            border-radius: 0!important;
            color: #4364af!important;
            text-align: left;
        }
        .btn-link-custom:hover {
            border-radius: 0;
            color: #32035c;
            background-color: #5b278d1f;
            width: 100%;
            text-align: left;

        }

        .card-title {
            padding: 10px 8px;
            margin-bottom: 0;
            background: linear-gradient(135deg, #2461a1 0%, #ae70dd 100%);
            color: #fff;
            font-size: 16px!important;
            border-radius: 7px 7px 0 0;
        }

        #backToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
            background: #878787;
            border: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        #backToTop:hover {
            background: #3a3a3a;

        }

        .style-bloqueio {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #000;
        }

        .style-bloqueio-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .blur {
            filter: blur(1.2px);
            transition: filter 0.3s ease;
            height: 300px;
        }

        .svg-bloqueio {
            width: 60px;
            height: 60px;
            display: block;
        }

        .bloqueado {
            background: #ffffffad;
            position: absolute;
            top: 0;
            bottom: 0;
        }

        .card-disabled {
            position: relative;
            overflow: hidden;
        }

        .bloqueio-overlay {
            margin: 4rem 0 0;
        }

        header {
            text-align: left;
            background: #fff;
            color: #285d9f;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 9;
            box-shadow: 0px 7px 20px -10px #bbbbbb94;
        }


</style>

</head>
<body>

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
    <h2>Listas Cadastradas</h2>
    <a href="nova-lista.php" class="btn btn-success">Nova Lista</a>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Preço</th>
          <th>Link de Compra</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($listas as $lista): ?>
          <tr>
            <td><?= htmlspecialchars($lista['nome']) ?></td>
            <td>R$ <?= number_format($lista['preco'], 2, ',', '.') ?></td>
            <td><a href="<?= htmlspecialchars($lista['link_de_compra']) ?>" target="_blank">Ver Link</a></td>
            <td>
              <a href="editar-lista.php?id=<?= $lista['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
              <a href="excluir-lista.php?id=<?= $lista['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta lista?')">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>
