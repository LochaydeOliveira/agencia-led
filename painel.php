<?php
    session_start();
    require 'conexao.php';

    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit;
    }

    $email = $_SESSION['usuario'];
    $nome = htmlspecialchars($_SESSION['nome']);

    // Função para gerar slug
    function slugify($text) {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); // Remove acentos
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text); // Remove especiais
        $text = strtolower(trim($text));
        $text = preg_replace('/\s+/', '-', $text);
        return $text;
    }

    // Buscar cliente e classificação
    $stmt = $pdo->prepare("SELECT id, classificacao, status FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();

    $acesso_liberado = false;
    $listas_com_acesso = [];
    $todas_listas = [];

    if ($cliente) {
        $cliente_id = $cliente['id'];
        $classificacao = $cliente['classificacao'];
        $status_cliente = $cliente['status'];

        if ($status_cliente === 'ativo') {
            $acesso_liberado = true;

            // Buscar todas as listas
            $stmt = $pdo->query("SELECT id, nome, descricao, conteudo_html, link_de_compra FROM listas");
            $todas_listas = $stmt->fetchAll();

            if ($classificacao === 'prata') {
                $stmt = $pdo->prepare("SELECT lista_id FROM clientes_listas WHERE cliente_id = ? AND status = 'ativo'");
                $stmt->execute([$cliente_id]);
                $listas_com_acesso = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        }
    }
?>



<!DOCTYPE html>
<html lang="pt-BR">
    
<head>
    <meta charset="UTF-8">
    <title>Acesso as Listas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="icon" href="assets-agencia-led/img/icone-favorito-led.png" type="image/png">
    <link rel="apple-touch-icon" href="assets-agencia-led/img/icone-favorito-led.png">

    <style>

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #000;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }

        .accordion-button::after {
            background-size: 1rem;
        }

        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .conteudo-lista {
            padding: 1rem;
        }

        .conteudo-lista ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .conteudo-lista ul li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .conteudo-lista ul li:last-child {
            border-bottom: none;
        }

        .conteudo-lista ul li a {
            color: #000;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .conteudo-lista ul li a:hover {
            color: #0d6efd;
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
                color:rgb(0, 0, 0);
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
                color:rgb(255, 255, 255);
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
                color:rgb(0, 0, 0);
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
                color:rgb(255, 255, 255);
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
                color:rgb(0, 0, 0);
            }

            .main-content p {
                margin: 0;
                font-size: 13px;
                color:rgb(0, 0, 0);
                text-align: center;
                line-height: 14px;
                opacity: 0.6;
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
                color: #ffffff;
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
                background: #f1f2f3;
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
                border-radius: 0!important;
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
                color:rgb(0, 0, 0)!important;
                text-align: left;
                opacity: 0.7;
            }
            .btn-link-custom:hover {
                border-radius: 0;
                color: #32035c;
                background-color: #e2ddcf;
                width: 100%;
                text-align: left;
                font-weight: 700;
            }

            .instagram:hover {
                background: #e2ddcf!important;
                border-radius: 3px!important;
            }           

            .card-header {
                background: #e2ddcf!important;
            }

            .card-title {
                padding: 10px 25px;
                margin-bottom: 0;
                color: #000000;
                font-size: 16px!important;
                font-weight: 700;
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
                background: #ffffffad!important;
                position: absolute;
                top: 0;
                bottom: 0;
                width: 100%;
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
                background: #000000;
                color: #ffffff;
                width: 100%;
                position: fixed;
                top: 0;
                z-index: 9;
                box-shadow: 0px 7px 20px -10px #bbbbbb94;
                padding: 5px!important;
            }

            .conteudo-lista ul {
                list-style: none;
                padding: 0;
            }

            .conteudo-lista ul li {
                transition: background-color 0.3s ease, transform 0.3s ease;
                font-size: 15px;
                border-radius: 0!important;
                color:rgb(0, 0, 0)!important;
                text-align: left;
                font-weight: 300;
                margin: 0 0 1.5rem;
                opacity: 0.7;
            }

            .conteudo-lista ul li a {
                margin: 0 0 0 3px;
            }

            .card {
                max-height: max-content!important;
                box-shadow: 0 1px 4px 0 rgba(0,0,0,.15);
            }

            .card-body {
                background:rgb(255, 255, 255);
                border-radius: 0px!important;
            }

            .navbar-expand-lg {
                justify-content: space-between!important;
                padding: 10px 15px;
            }

            .area-adm-style {
                display: flex;
                gap: 25px;
            }

            .img-logo {
                width: 100px;
            }           

        .footer {
            background-color: #000000 !important;
            margin-top: 4rem !important;
            width: 100%;
            padding: 0 5rem;
            font-size: 13px;
            font-family: 'Inter', sans-serif!important;
        }

        .footer h5 {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .footer a {
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #e2ddcf!important;
        }

        .footer .bi {
            font-size: 1.1rem;
        }

        .footer p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .logo-footer {
            margin-left: -30px;
        }

        .container-logo-footer {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        @media (min-width: 768px) {
            .col-md-9 {
                width: 100%!important;
            }
        }


    </style>

</head>

<body>

<header>
  <nav class="navbar navbar-expand-lg shadow-bg">

    <button id="menuHamburgerIcon" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
      </svg>
    </button>

    <div id="iconUser" class="content-user">
        <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30">
            <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm-4,21.164v-.164c0-2.206,1.794-4,4-4s4,1.794,4,4v.164c-1.226.537-2.578.836-4,.836s-2.774-.299-4-.836Zm9.925-1.113c-.456-2.859-2.939-5.051-5.925-5.051s-5.468,2.192-5.925,5.051c-2.47-1.823-4.075-4.753-4.075-8.051C2,6.486,6.486,2,12,2s10,4.486,10,10c0,3.298-1.605,6.228-4.075,8.051Zm-5.925-15.051c-2.206,0-4,1.794-4,4s1.794,4,4,4,4-1.794,4-4-1.794-4-4-4Zm0,6c-1.103,0-2-.897-2-2s.897-2,2-2,2,.897,2,2-.897,2-2,2Z"/>
        </svg>
        <div>
            <p class="mb-0">Olá, <strong><?php echo $nome; ?></strong>!</p>
        </div>
    </div>


    <div class="collapse navbar-collapse nav-mobile" id="navbarNav">
        <a id="logoutMobile" href="logout.php" class="btn-logout-mobile align-items-center gap-1">
            <svg fill="red" width="14" height="14" id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                <path d="m23 2.5v19c0 .828-.672 1.5-1.5 1.5s-1.5-.672-1.5-1.5v-19c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5zm-6.5 8h-10.929c.95-1.022 2.029-1.946 3.25-2.744.693-.454.888-1.384.435-2.077-.453-.692-1.383-.886-2.076-.435-2.439 1.596-4.438 3.597-5.943 5.947-.315.493-.315 1.124 0 1.617 1.504 2.351 3.504 4.352 5.943 5.947.683.449 1.62.263 2.076-.435.454-.693.259-1.623-.435-2.077-1.221-.798-2.3-1.722-3.25-2.744h10.929c.828 0 1.5-.672 1.5-1.5s-.672-1.5-1.5-1.5z"/>
            </svg>
            Sair
        </a>
    </div>

    <div class="area-adm-style">
        <a style="color: #fffff" href="logout.php" class="btn-logout-desktop align-items-center gap-1">
            Sair
            <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15">
                <g>
                <path d="M2,21V3A1,1,0,0,1,3,2H8V0H3A3,3,0,0,0,0,3V21a3,3,0,0,0,3,3H8V22H3A1,1,0,0,1,2,21Z"/>
                <path d="M23.123,9.879,18.537,5.293,17.123,6.707l4.264,4.264L5,11l0,2,16.443-.029-4.322,4.322,1.414,1.414,4.586-4.586A3,3,0,0,0,23.123,9.879Z"/>
                </g>
            </svg>
        </a>
    </div>

  </nav>
</header>

<main class="container py-5 main-content">        
    <div class="main-fornecedores">
        <div class="col-md-9">



        <div class="tt-list">
            <?php if ($acesso_liberado): ?>
                <h1 class="mb-4">Lista de Fornecedores Nacionais</h1>
                <p class="mb-5">Confira abaixo a lista organizada de fornecedores nacionais por categoria, com links diretos.</p>
            <?php else: ?>
                <h1 style="color: red" class="mb-4">Seu Acesso Foi Suspenso!</h1>
                <p class="mb-5">Entre em contato com o suporte pelo e-mail: suporte@agencialed.com.</p>
            <?php endif; ?>
        </div>
        

        <?php if ($acesso_liberado): ?>
                <div class="mb-3">
                    <label for="categoryFilter" class="form-label">Filtrar por categoria:</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="all">Todas as Categorias</option>
                        <?php foreach ($todas_listas as $lista): ?>
                            <option value="<?php echo slugify($lista['nome']); ?>">
                                <?php echo htmlspecialchars($lista['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row" id="fornecedores">
                    <div class="row">
                        <?php foreach ($todas_listas as $lista): ?>
                            <?php 
                            $liberado = ($classificacao === 'ouro') ? true : in_array($lista['id'], $listas_com_acesso);
                            $slug = slugify($lista['nome']);
                            ?>
                            <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in"
                                data-category="<?php echo $slug; ?>"
                                data-lista-id="<?php echo $lista['id']; ?>">

                                <div class="card h-100 rounded-2 border-0 card-disabled">
                                    <div class="card-header bg-white border-0">
                                        <h5 class="card-title mb-0 <?php echo $liberado ? '' : 'blur'; ?>">
                                            <?php echo htmlspecialchars($lista['nome']); ?>
                                        </h5>
                                    </div>

                                    <div class="card-body <?php echo $liberado ? '' : 'bloqueado'; ?>">
                                        <?php if ($liberado): ?>
                                            <div class="accordion" id="accordion<?php echo $lista['id']; ?>">
                                                <div class="accordion-item border-0">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $lista['id']; ?>">
                                                            Ver Fornecedores
                                                        </button>
                                                    </h2>
                                                    <div id="collapse<?php echo $lista['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordion<?php echo $lista['id']; ?>">
                                                        <div class="accordion-body p-0">
                                                            <div class="conteudo-lista">
                                                                <?php echo $lista['conteudo_html']; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="bloqueio-overlay">
                                                <div class="style-bloqueio">
                                                    <svg class="svg-bloqueio" fill="#000" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path d="M19,8.424V7A7,7,0,0,0,5,7V8.424A5,5,0,0,0,2,13v6a5.006,5.006,0,0,0,5,5H17a5.006,5.006,0,0,0,5-5V13A5,5,0,0,0,19,8.424ZM7,7A5,5,0,0,1,17,7V8H7ZM20,19a3,3,0,0,1-3,3H7a3,3,0,0,1-3-3V13a3,3,0,0,1,3-3H17a3,3,0,0,1,3,3Z"/>
                                                        <path d="M12,14a1,1,0,0,0-1,1v2a1,1,0,0,0,2,0V15A1,1,0,0,0,12,14Z"/>
                                                    </svg>
                                                </div>
                                                <div class="style-bloqueio-btn">
                                                    <a href="<?php echo htmlspecialchars($lista['link_de_compra']); ?>"
                                                        target="_blank"
                                                        class="btn btn-comprar-lista">
                                                        Liberar Lista
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<footer class="footer mt-auto py-4 bg-dark text-white">
    <div>
        <div class="row">
            <div class="col-md-4 container-logo-footer">
                <img class="logo-footer" src="assets-agencia-led/img/logo-led.png" alt="Logo Pang Rodapé" width="100" height="50"><br>
                <p class="mb-0">© <?php echo date('Y'); ?> Agência LED. Todos os direitos reservados.</p>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="mb-3">Links Úteis</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="politica-privacidade.php" class="text-white text-decoration-none">Política de Privacidade</a></li>
                    <li class="mb-2"><a href="termos-uso.php" class="text-white text-decoration-none">Termos de Uso</a></li>
                    <li><a href="politica-cookies.php" class="text-white text-decoration-none">Política de Cookies</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="mb-3">Contato</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="https://wa.me/558599671024" target="_blank" class="text-white text-decoration-none">
                            <i class="bi bi-whatsapp me-2"></i>WhatsApp: (85) 99671-024
                        </a>
                    </li>
                    <li>
                        <a href="mailto:suporte@agencialed.com" class="text-white text-decoration-none">
                            <i class="bi bi-envelope me-2"></i>suporte@agencialed.com
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<button id="backToTop" class="btn btn-primary">↑</button>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filter = document.getElementById('categoryFilter');
        const searchInput = document.getElementById('searchInput');
        const fornecedores = document.querySelectorAll('.fornecedor');
        const backToTop = document.getElementById('backToTop');
        const dropdownBtn = document.getElementById('dropdownCategoriasBtn');
        const dropdownList = document.getElementById('dropdownList');
        const navMobile = document.getElementById('navbarNavMobile');
        const menuHamburger = document.getElementById('menuHamburgerIcon');

        if (filter) {
            filter.addEventListener('change', () => {
                const value = filter.value;
                fornecedores.forEach(card => {
                    card.style.display = (value === 'all' || card.dataset.category === value) ? 'block' : 'none';
                });
            });
        }

        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const value = item.dataset.value;
                if (dropdownBtn) dropdownBtn.textContent = item.textContent;
                fornecedores.forEach(card => {
                    card.style.display = (value === 'all' || card.dataset.category === value) ? 'block' : 'none';
                });
                if (dropdownList) dropdownList.classList.add('d-none'); // Fecha após seleção
            });
        });


        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const term = searchInput.value.toLowerCase();
                fornecedores.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(term) ? 'block' : 'none';
                });
            });
        }


        if (backToTop) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 200) {
                    backToTop.style.display = 'block';
                    backToTop.style.opacity = '1';
                } else {
                    backToTop.style.opacity = '0';
                    setTimeout(() => {
                        if (window.scrollY <= 200) backToTop.style.display = 'none';
                    }, 300);
                }
            });
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }


        if (menuHamburger && navMobile) {
            menuHamburger.addEventListener('click', () => {
                if (navMobile.style.maxHeight && navMobile.style.maxHeight !== '0px') {
                    navMobile.style.maxHeight = '0';
                } else {
                    navMobile.style.maxHeight = navMobile.scrollHeight + 'px';
                }
            });
        }

        if (dropdownBtn && dropdownList) {
            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownList.classList.toggle('d-none');
            });

            document.addEventListener('click', (event) => {
                if (!dropdownBtn.contains(event.target) && !dropdownList.contains(event.target)) {
                    dropdownList.classList.add('d-none');
                }
            });
        }
    });


</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const iconUser = document.getElementById('iconUser');
        const logoutMobile = document.getElementById('logoutMobile');

        if (iconUser && logoutMobile) {
            iconUser.addEventListener('click', () => {
                if (logoutMobile.style.maxHeight && logoutMobile.style.maxHeight !== '0px') {
                    logoutMobile.style.maxHeight = '0';
                } else {
                    const altura = logoutMobile.scrollHeight;
                    logoutMobile.style.maxHeight = altura + 'px';
                }
            });
        };
    });

</script>

<script>
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const value = this.value;
        document.querySelectorAll('.fornecedor').forEach(card => {
            card.style.display = (value === 'all' || card.dataset.category === value) ? 'block' : 'none';
        });
    });
</script>

</body>
</html>