<?php
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit;
    }
    $usuario = htmlspecialchars($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área de Membros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>

    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
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
    }

    .content-logof {
        display: flex;
        align-items: center;
        gap: 2rem;
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
    .btn-logout {
        border: none;
        text-decoration: none;
        display: inline-block;
        color: #000;
        font-size: 14px;
        margin-left: 3px; 
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
    }
    .btn-link-custom:hover {
        border-radius: 0;
        color: #32035c;
        background-color: #5b278d1f;
        width: 100%;
        text-align: left;

    }
    #backToTop {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        display: none;
        background: #878787;
        border: none;
    }

    #backToTop:hover {
        background: #3a3a3a;

    }

    .card-title {
        padding: 10px 8px;
        margin-bottom: var(--bs-card-title-spacer-y);
        background: linear-gradient(135deg, #2461a1 0%, #ae70dd 100%);
        color: #fff;
        font-size: 16px!important;
        border-radius: 7px 7px 0 0;
    }
    .form-control { 
        border: none;
        padding: 10px;
    }

    .form-select {
        border: 0;
        padding: 8px;
        color: #0000008c;
    }
    header {
        padding: 20px 10px;
        text-align: left;
        background: #fff;
        color: #285d9f;
        width: 100%;
        position: fixed;
        top: 0;
        z-index: 9;
    }

    .main-content {
        margin: 8rem 0;
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
</style>
</head>
<body>


<header>
    <div class="container content-header">
        <div class="content-user">
            <h2>Área do Cliente</h2>
            <div>
                <p>Olá, <strong><?php echo $usuario; ?></strong>!</p>
            </div>
        </div>

        <div class="content-logof">

            <div class="">
                <input style="background: #ededed;" type="text" class="form-control" id="searchInput" placeholder="Buscar fornecedor...">
            </div>

            <div>
                <a href="logout.php" class="btn-logout">
                    Sair 
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                        <g id="_01_align_center" data-name="01 align center"><path d="M2,21V3A1,1,0,0,1,3,2H8V0H3A3,3,0,0,0,0,3V21a3,3,0,0,0,3,3H8V22H3A1,1,0,0,1,2,21Z"/><path d="M23.123,9.879,18.537,5.293,17.123,6.707l4.264,4.264L5,11l0,2,16.443-.029-4.322,4.322,1.414,1.414,4.586-4.586A3,3,0,0,0,23.123,9.879Z"/></g>
                    </svg>
                </a>
            </div>
        </div>
    </div> 
    <nav class="main-nav">
        <div class="container">
            <div class="row">             
                <div class="">       
                    <!-- Filtro -->
                    <div>
                        <select class="form-select" id="categoryFilter">
                        <option value="all">Todas as Categorias</option>
                        <option value="decoração">Decoração</option>
                        <option value="bebê">Bebê, Enxoval, Decoração e Pet</option>
                        <option value="brinquedos">Brinquedos</option>
                        <option value="joias">Joias, Folheados e Acessórios</option>
                        <option value="calçados">Calçados e Vestuário</option>
                        <option value="relógios">Relógios</option>
                        <option value="perfumes">Perfumes</option>
                        <option value="ferramentas">Ferramentas e Autopeças</option>
                        <option value="eletrônicos">Eletrônicos, Segurança, Informática</option>
                        <option value="diversas">Diversas Categorias</option>
                        <option value="plataformas">Plataformas de Drop Nacional</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </nav>       
</header>

    

<main class="container py-5 main-content">        
    <div class="main-fornecedores">
        <!-- Coluna principal: fornecedores -->
        <div class="col-md-9">
            <div class="tt-list">
                <h1 class="mb-4">Lista de Fornecedores Nacionais</h1>
                <p class="mb-5">Confira abaixo a lista organizada de fornecedores nacionais por categoria, com links diretos.</p>
            </div>
            <div class="row" id="fornecedores">

                <!-- Decoração -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="decoração">
                    <div class="card h-100 rounded-2 border-0">
                        <h5 class="card-title">Decoração</h5>
                        <div class="card-body">
                            <a href="https://moveistrovarelli.com.br/" target="_blank" class="btn btn-link-custom">Moveis Trovarelli</a><br>
                            <a href="https://www.decormoveis.com.br/" target="_blank" class="btn btn-link-custom">Decor Moveis</a><br>
                            <a href="https://www.gazinatacado.com.br/" target="_blank" class="btn btn-link-custom">Gazin Atacado</a><br>
                            <a href="https://www.lenobre.com.br/" target="_blank" class="btn btn-link-custom">Le Nobre</a><br>
                            <a href="https://www.printile.com.br/" target="_blank" class="btn btn-link-custom">Printile</a><br>
                            <a href="https://www.souflorir.com.br/" target="_blank" class="btn btn-link-custom">Sou Florir</a>
                        </div>
                    </div>
                </div>
    
                <!-- Bebê, Enxoval, Decoração e Pet -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="bebê">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Bebê, Enxoval, Decoração e Pet</h5>
                    <div class="card-body">
                    <a href="https://www.dropet.com.br/" target="_blank" class="btn btn-link-custom">Dropet</a><br>
                    <a href="https://www.maisquedistribuidora.com.br/" target="_blank" class="btn btn-link-custom">Mais que Distribuidora</a><br>
                    <a href="https://linkme.bio/deccoralle" target="_blank" class="btn btn-link-custom">Deccoralle Decor</a><br>
                    <a href="https://www.lp.gugadistribuidoraibitinga.com.br/" target="_blank" class="btn btn-link-custom">Guga Distribuidora</a><br>
                    <a href="https://www.gugadistribuidoraibitinga.com.br/" target="_blank" class="btn btn-link-custom">Guga Distribuidora - Catálogo</a><br>
                    <a href="https://www.molinaspet.com.br/" target="_blank" class="btn btn-link-custom">Molinas Pet</a>
                    </div>
                </div>
                </div>
    
                <!-- Brinquedos -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="brinquedos">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Brinquedos</h5>
                    <div class="card-body">
                    <a href="https://europio.catalogomobile.com.br/dashboard/products" target="_blank" class="btn btn-link-custom">Europio</a>
                    </div>
                </div>
                </div>
    
                <!-- Joias, Folheados e Acessórios -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="joias">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Joias, Folheados e Acessórios</h5>
                    <div class="card-body">                    
                        <a href="https://www.luxjoias.com/dropshipping-revenda-i-47.html" target="_blank" class="btn btn-link-custom">Lux Joias</a><br>
                        <a href="https://dropse.com.br/" target="_blank" class="btn btn-link-custom">Dropse</a><br>
                        <a href="https://www.florattajoias.com.br/" target="_blank" class="btn btn-link-custom">Floratta Joias</a><br>
                        <a href="https://www.sobellavariedades.com.br/" target="_blank" class="btn btn-link-custom">Sobella Variedades</a><br>
                        <a href="https://www.imagemfolheados.com.br/" target="_blank" class="btn btn-link-custom">Imagem Folheados</a>
                    </div>
                </div>
                </div>
    
                <!-- Calçados e Vestuário -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="calçados">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Calçados e Vestuário</h5>
                    <div class="card-body">                    
                        <a href="https://www.imperiodasrasteiras.com.br/" target="_blank" class="btn btn-link-custom">Império das Rasteiras</a><br>
                        <a href="https://www.atacadobarato.com/" target="_blank" class="btn btn-link-custom">Atacado Barato</a><br>
                        <a href="https://www.revendadecalcados.com.br/painel_acesso.php" target="_blank" class="btn btn-link-custom">Revenda de Calçados</a><br>
                        <a href="https://www.parishoes.com.br/pagina/revenda.html" target="_blank" class="btn btn-link-custom">Pari Shoes</a><br>
                        <a href="https://rickshoes.com.br/" target="_blank" class="btn btn-link-custom">Rick Shoes</a><br>
                        <a href="https://www.bmshopdrop.com.br/pagina/dropshipping-manual-do-lojista-reveendedor.html" target="_blank" class="btn btn-link-custom">BM Shop Drop</a><br>
                        <a href="https://cftdropshipping.com.br/" target="_blank" class="btn btn-link-custom">CFT Dropshipping</a><br>
                        <a href="https://www.francasapatos.com.br/" target="_blank" class="btn btn-link-custom">Franca Sapatos</a><br>
                        <a href="https://www.dropaaqui.com.br/" target="_blank" class="btn btn-link-custom">Dropa Aqui</a><br>
                        <a href="https://suafabrica.com.br/blogs/como-funciona/quais-as-vantagens-de-fazer-dropshipping" target="_blank" class="btn btn-link-custom">Sua Fábrica</a><br>
                        <a href="https://www.kaisan.com.br/" target="_blank" class="btn btn-link-custom">Kaisan</a><br>
                        <a href="https://www.atacadaodaroupa.com/" target="_blank" class="btn btn-link-custom">Atacadão da Roupa</a><br>
                    </div>
                </div>
                </div>
    
                <!-- Relógios -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="relógios">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Relógios</h5>
                    <div class="card-body">
                    <a href="https://www.relogiosnoatacado.com/m/dropshipping/" target="_blank" class="btn btn-link-custom">Relógios no Atacado</a>
                    </div>
                </div>
                </div>
    
                <!-- Perfumes -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="perfumes">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Perfumes</h5>
                    <div class="card-body">
                    <a href="https://www.bmshopdrop.com.br/pagina/dropshipping-manual-do-lojista-reveendedor.html" target="_blank" class="btn btn-link-custom">BM Shop Drop</a>
                    </div>
                </div>
                </div>
    
                <!-- Ferramentas e Autopeças -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="ferramentas">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Ferramentas e Autopeças</h5>
                    <div class="card-body">
                    <a href="https://www.laquila.com.br/seja-um-revendedor" target="_blank" class="btn btn-link-custom">Laquila</a><br>
                    <a href="https://www.gb.com.br/dropshipping/" target="_blank" class="btn btn-link-custom">GB</a><br>
                    <a href="https://www.shoppecas.com.br/" target="_blank" class="btn btn-link-custom">Shop Peças</a>
                    </div>
                </div>
                </div>
    
                <!-- Eletrônicos -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="eletrônicos">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Eletrônicos, Segurança, Informática</h5>
                    <div class="card-body">
                    <a href="https://hayamax.com.br/dropshipping" target="_blank" class="btn btn-link-custom">Hayamax</a><br>
                    <a href="https://www.hayonik.com.br/" target="_blank" class="btn btn-link-custom">Hayonik</a><br>
                    <a href="https://www.uwebdistribuidora.com.br/" target="_blank" class="btn btn-link-custom">Uweb Distribuidora</a><br>
                    <a href="https://cemstoretec.com.br/" target="_blank" class="btn btn-link-custom">Cemstoretec</a>
                    </div>
                </div>
                </div>
    
                <!-- Diversas Categorias -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="diversas">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Diversas Categorias</h5>
                    <div class="card-body">
                    <a href="https://fornecedornacional.com.br/" target="_blank" class="btn btn-link-custom">Fornecedor Nacional</a><br>
                    <a href="https://atacaly.com/" target="_blank" class="btn btn-link-custom">Atacaly</a><br>
                    <a href="https://dinka.com.br/categoria-produto/fornecedor-dropshipping/todos-produtos/" target="_blank" class="btn btn-link-custom">Dinka</a>
                    </div>
                </div>
                </div>
    
                <!-- Plataformas -->
                <div class="col-md-6 col-lg-4 mb-4 fornecedor fade-in" data-category="plataformas">
                <div class="card h-100 rounded-2 border-0">
                    <h5 class="card-title">Plataformas de Drop Nacional</h5>
                    <div class="card-body">
                    <a href="https://primodrop.online/" target="_blank" class="btn btn-link-custom">Primodrop</a><br>
                    <a href="https://www.updrop.com.br/" target="_blank" class="btn btn-link-custom">Updrop</a><br>
                    <a href="https://updrop.online/" target="_blank" class="btn btn-link-custom">Catálogo Updrop</a>
                    </div>
                </div>
                </div>
    
            </div>
        </div>
    </div>
</main>


    <!-- Botão Voltar ao Topo -->
    <button id="backToTop" class="btn btn-primary">↑ Topo</button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Filtro
    const filter = document.getElementById('categoryFilter');
    const searchInput = document.getElementById('searchInput');
    const fornecedores = document.querySelectorAll('.fornecedor');

    filter.addEventListener('change', () => {
        const value = filter.value;
        fornecedores.forEach(card => {
        if (value === 'all' || card.dataset.category === value) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
        });
    });

    // Busca
    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        fornecedores.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(term) ? 'block' : 'none';
        });
    });

    // Voltar ao topo
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 200 ? 'block' : 'none';
    });
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    </script>

</body>
</html>