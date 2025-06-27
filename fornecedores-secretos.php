<?php
// Configurações básicas
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações de URL amigável
$request_uri = $_SERVER['REQUEST_URI'];
$base_url = 'https://' . $_SERVER['HTTP_HOST'];
$current_url = $base_url . $request_uri;

// Meta tags dinâmicas
$page_title = "Lista de Fornecedores - Agência Led";
$page_description = "Descubra fornecedores nacionais secretos para seu negócio. Listas exclusivas de fornecedores confiáveis.";
$page_keywords = "fornecedores, nacionais, secretos, agência led, dropshipping, revenda";

// Verificar se é uma requisição AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Se for AJAX, retornar apenas dados JSON
if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Requisição processada']);
    exit;
}

// Função para gerar URLs amigáveis
function generateFriendlyUrl($string) {
    $string = strtolower($string);
    $string = preg_replace('/[áàâãä]/u', 'a', $string);
    $string = preg_replace('/[éèêë]/u', 'e', $string);
    $string = preg_replace('/[íìîï]/u', 'i', $string);
    $string = preg_replace('/[óòôõö]/u', 'o', $string);
    $string = preg_replace('/[úùûü]/u', 'u', $string);
    $string = preg_replace('/[ç]/u', 'c', $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Dados dos produtos (pode ser carregado do banco de dados)
$produtos = [
    [
        'id' => 1,
        'nome' => 'Autopeças e Ferramentas',
        'descricao' => 'Distribuidores e fornecedores de automotivas, ferramentas e peças.',
        'preco' => 29.90,
        'fornecedores' => '1 Fornecedor',
        'imagem' => 'assets-listas-lp/images/NOVIDADES-3.png',
        'link' => 'https://seguro.agencialed.com/r/2I5VKL2MJG',
        'slug' => 'autopecas-ferramentas'
    ],
    [
        'id' => 2,
        'nome' => 'Moda Feminina',
        'descricao' => 'Fornecedores especializados em moda feminina.',
        'preco' => 49.90,
        'fornecedores' => '+30 Fornecedores',
        'imagem' => 'assets-listas-lp/images/NOVIDADES-4.png',
        'link' => 'https://seguro.agencialed.com/r/LH5BK5FJFL',
        'slug' => 'moda-feminina'
    ],
    [
        'id' => 3,
        'nome' => 'Moda Masculina',
        'descricao' => 'Fornecedores de moda masculina.',
        'preco' => 29.90,
        'fornecedores' => '4 Fornecedores',
        'imagem' => 'assets-listas-lp/images/NOVIDADES-6.png',
        'link' => 'https://seguro.agencialed.com/r/BCHAHE4L8I',
        'slug' => 'moda-masculina'
    ],
    [
        'id' => 4,
        'nome' => 'Semijoias',
        'descricao' => 'Fornecedores de semijoias e acessórios.',
        'preco' => 19.90,
        'fornecedores' => '10 Fornecedores',
        'imagem' => 'assets-listas-lp/images/novo04-768x768-1.webp',
        'link' => 'https://seguro.agencialed.com/r/TCM4QEHHIV',
        'slug' => 'semijoias'
    ]
];

// Processar roteamento para URLs amigáveis
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Se não houver path, mostrar página principal
if (empty($path)) {
    $page_type = 'home';
} else {
    // Verificar se é uma página de produto específico
    $produto_encontrado = null;
    foreach ($produtos as $produto) {
        if ($path === $produto['slug']) {
            $produto_encontrado = $produto;
            $page_type = 'produto';
            break;
        }
    }
    
    if (!$produto_encontrado) {
        $page_type = '404';
    }
}

// Headers de cache e segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($current_url); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="assets-agencia-led/img/icone-favorito-led.png" type="image/png">
    <link rel="apple-touch-icon" href="assets-agencia-led/img/icone-favorito-led.png">

    <!-- CSS -->
    <link rel="stylesheet" href="assets-listas-lp/css/patterns.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/enhancements.min.css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Montserrat%3Aital%2Cwght%400%2C300%3B0%2C400%3B0%2C500%3B0%2C600%3B0%2C700%3B0%2C800%3B0%2C900%3B1%2C300%3B1%2C500%3B1%2C600%3B1%2C700%3B1%2C800%3B1%2C900&display=swap&ver=1.1.3">
    <link rel="stylesheet" href="assets-listas-lp/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/style.css">
    <link rel="stylesheet" href="assets-listas-lp/css/theme.css">
    <link rel="stylesheet" href="assets-listas-lp/css/frontend.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/post-8.css">
    <link rel="stylesheet" href="assets-listas-lp/css/e-animation-pulse.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/widget-image.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/widget-heading.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/bounceIn.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/zoomIn.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/e-animation-grow.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/widget-divider.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/widget-toggle.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/shapes.min.css">
    <link rel="stylesheet" href="assets-listas-lp/css/post-2573.css">
    <link rel="stylesheet" href="assets-listas-lp/css/inter.css">
    <link rel="stylesheet" href="assets-listas-lp/css/poppins.css">
    <link rel="stylesheet" href="assets-listas-lp/css/montserrat.css">
    <link rel="stylesheet" href="assets-listas-lp/css/worksans.css">
    <link rel="stylesheet" href="assets-listas-lp/css/roboto.css">
    <link rel="stylesheet" href="assets-listas-lp/css/mavenpro.css">

    <style>
        .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload),
        .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * {
            background-image: none !important;
        }
        @media screen and (max-height: 1024px) {
            .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload),
            .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload) * {
                background-image: none !important;
            }
            .tt-top {
                color: #ffe101!important;
                font-size: 60px;
                text-align: center;
                width: 75%;
                margin: 0 auto;
                font-weight: 800!important;
            }
            .bg-tt-top {
                background: #009c2e;
                height: 175px;
                display: flex;
                align-items: center;
            }
            .mg-bttn {
                margin-bottom: 7rem!important;
            }
        }
        @media screen and (max-height: 640px) {
            .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload),
            .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload) * {
                background-image: none !important;
            }
        }
        @media (max-width: 480px) {
            .tt-top {
                font-size: 30px;
                width: 90%;
            }
        }
        @media (min-width: 481px) and (max-width: 768px) { 
            .tt-top {
                font-size: 45px;
                width: 90%;
            }
        }
    </style>

    <!-- JavaScript -->
    <script src="assets-listas-lp/js/jquery.min.js"></script>
    <script src="assets-listas-lp/js/jquery-migrate.min.js"></script>
</head>

<body data-rsssl="1" class="wp-singular page-template page-template-elementor_canvas page page-id-2573 wp-custom-logo wp-theme-hello-academy eio-default elementor-default elementor-template-canvas elementor-kit-8 elementor-page elementor-page-2573">

<?php if ($page_type === 'home'): ?>
    <!-- Página Principal -->
    <section class="bg-tt-top elementor-section elementor-top-section elementor-element elementor-element-6b891a61 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-element_type="section" data-settings='{"background_background":"classic"}'>
        <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-28ec5e88" data-id="28ec5e88" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-218477ce elementor-widget elementor-widget-heading" data-id="218477ce" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default tt-top">LISTA DE FORNECEDORES NACIONAIS SECRETOS</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mg-bttn elementor-section elementor-inner-section elementor-element elementor-element-24e2a1cf elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="24e2a1cf" data-element_type="section">
        <div class="elementor-container elementor-column-gap-default">
            <?php foreach ($produtos as $produto): ?>
            <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-1142c0c" data-id="1142c0c" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-affe0e1 elementor-widget elementor-widget-image" data-id="affe0e1" data-element_type="widget" data-widget_type="image.default">
                        <div class="elementor-widget-container">
                            <img loading="lazy" decoding="async" width="500" height="500" src="<?php echo htmlspecialchars($produto['imagem']); ?>" class="elementor-animation-pulse attachment-large size-large wp-image-2684" alt="<?php echo htmlspecialchars($produto['nome']); ?>" srcset="<?php echo htmlspecialchars($produto['imagem']); ?> 500w" sizes="(max-width: 500px) 100vw, 500px">
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-ea4ffca elementor-widget elementor-widget-heading" data-id="ea4ffca" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default"><?php echo htmlspecialchars($produto['nome']); ?></h2>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-2781a2c elementor-widget elementor-widget-heading" data-id="2781a2c" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default"><?php echo htmlspecialchars($produto['fornecedores']); ?></h2>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-0370739 elementor-align-center elementor-widget__width-inherit elementor-widget elementor-widget-button" data-id="0370739" data-element_type="widget" data-settings='{"_animation":"bouncein","_animation_mobile":"zoomin"}' data-widget_type="button.default">
                        <div class="elementor-widget-container">
                            <div class="elementor-button-wrapper">
                                <a class="elementor-button elementor-button-link elementor-size-sm elementor-animation-grow" href="<?php echo htmlspecialchars($produto['link']); ?>" target="_blank">
                                    <span class="elementor-button-content-wrapper">
                                        <span class="elementor-button-icon">
                                            <svg aria-hidden="true" class="e-font-icon-svg e-fas-tags" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M497.941 225.941L286.059 14.059A48 48 0 0 0 252.118 0H48C21.49 0 0 21.49 0 48v204.118a48 48 0 0 0 14.059 33.941l211.882 211.882c18.744 18.745 49.136 18.746 67.882 0l204.118-204.118c18.745-18.745 18.745-49.137 0-67.882zM112 160c-26.51 0-48-21.49-48-48s21.49-48 48-48 48 21.49 48 48-21.49 48-48 48zm513.941 133.823L421.823 497.941c-18.745 18.745-49.137 18.745-67.882 0l-.36-.36L527.64 323.522c16.999-16.999 26.36-39.6 26.36-63.64s-9.362-46.641-26.36-63.64L331.397 0h48.721a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882z"></path></svg>
                                        </span>
                                        <span class="elementor-button-text">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Seção de Dúvidas Frequentes -->
    <section class="elementor-section elementor-top-section elementor-element elementor-element-6148df40 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="6148df40" data-element_type="section" data-settings='{"background_background":"classic","shape_divider_top":"tilt"}'>
        <div class="elementor-shape elementor-shape-top" aria-hidden="true" data-negative="false">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none">
                <path class="elementor-shape-fill" d="M0,6V0h1000v100L0,6z"></path>
            </svg>
        </div>
        <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-5f4f513d" data-id="5f4f513d" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-53d4c19e elementor-widget elementor-widget-text-editor" data-id="53d4c19e" data-element_type="widget" data-widget_type="text-editor.default">
                        <div class="elementor-widget-container">
                            <p><span style="color: #ffffff;">DÚVIDAS FREQUENTES</span></p>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-68985827 elementor-widget elementor-widget-toggle" data-id="68985827" data-element_type="widget" data-widget_type="toggle.default">
                        <div class="elementor-widget-container">
                            <div class="elementor-toggle">
                                <div class="elementor-toggle-item">
                                    <div id="elementor-tab-title-1751" class="elementor-tab-title" data-tab="1" role="button" aria-controls="elementor-tab-content-1751" aria-expanded="false">
                                        <span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
                                            <span class="elementor-toggle-icon-closed"><svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path></svg></span>
                                            <span class="elementor-toggle-icon-opened"><svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg></span>
                                        </span>
                                        <a class="elementor-toggle-title" tabindex="0">OS FORNECEDORES SÃO CONFIÁVEIS?</a>
                                    </div>
                                    <div id="elementor-tab-content-1751" class="elementor-tab-content elementor-clearfix" data-tab="1" role="region" aria-labelledby="elementor-tab-title-1751">
                                        <p>Sim, todos os fornecedores são extremamente competentes e confiáveis.</p>
                                    </div>
                                </div>
                                <div class="elementor-toggle-item">
                                    <div id="elementor-tab-title-1752" class="elementor-tab-title" data-tab="2" role="button" aria-controls="elementor-tab-content-1752" aria-expanded="false">
                                        <span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
                                            <span class="elementor-toggle-icon-closed"><svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path></svg></span>
                                            <span class="elementor-toggle-icon-opened"><svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg></span>
                                        </span>
                                        <a class="elementor-toggle-title" tabindex="0">COMO RECEBO A LISTA?</a>
                                    </div>
                                    <div id="elementor-tab-content-1752" class="elementor-tab-content elementor-clearfix" data-tab="2" role="region" aria-labelledby="elementor-tab-title-1752">
                                        <p>Assim que é confirmado o pagamento, o acesso a área do cliente será enviado para seu e-mail com login e senha. Muito fácil de acessar!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php elseif ($page_type === 'produto' && $produto_encontrado): ?>
    <!-- Página de Produto Específico -->
    <section class="bg-tt-top elementor-section elementor-top-section elementor-element elementor-element-6b891a61 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-element_type="section" data-settings='{"background_background":"classic"}'>
        <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-28ec5e88" data-id="28ec5e88" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-218477ce elementor-widget elementor-widget-heading" data-id="218477ce" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default tt-top"><?php echo htmlspecialchars($produto_encontrado['nome']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mg-bttn elementor-section elementor-inner-section elementor-element elementor-element-24e2a1cf elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="24e2a1cf" data-element_type="section">
        <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-1142c0c" data-id="1142c0c" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-affe0e1 elementor-widget elementor-widget-image" data-id="affe0e1" data-element_type="widget" data-widget_type="image.default">
                        <div class="elementor-widget-container">
                            <img loading="lazy" decoding="async" width="500" height="500" src="<?php echo htmlspecialchars($produto_encontrado['imagem']); ?>" class="elementor-animation-pulse attachment-large size-large wp-image-2684" alt="<?php echo htmlspecialchars($produto_encontrado['nome']); ?>" srcset="<?php echo htmlspecialchars($produto_encontrado['imagem']); ?> 500w" sizes="(max-width: 500px) 100vw, 500px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-1142c0c" data-id="1142c0c" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-ea4ffca elementor-widget elementor-widget-heading" data-id="ea4ffca" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default"><?php echo htmlspecialchars($produto_encontrado['nome']); ?></h2>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-2781a2c elementor-widget elementor-widget-heading" data-id="2781a2c" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default"><?php echo htmlspecialchars($produto_encontrado['fornecedores']); ?></h2>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-2781a2c elementor-widget elementor-widget-text-editor" data-id="2781a2c" data-element_type="widget" data-widget_type="text-editor.default">
                        <div class="elementor-widget-container">
                            <p><?php echo htmlspecialchars($produto_encontrado['descricao']); ?></p>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-0370739 elementor-align-center elementor-widget__width-inherit elementor-widget elementor-widget-button" data-id="0370739" data-element_type="widget" data-settings='{"_animation":"bouncein","_animation_mobile":"zoomin"}' data-widget_type="button.default">
                        <div class="elementor-widget-container">
                            <div class="elementor-button-wrapper">
                                <a class="elementor-button elementor-button-link elementor-size-sm elementor-animation-grow" href="<?php echo htmlspecialchars($produto_encontrado['link']); ?>" target="_blank">
                                    <span class="elementor-button-content-wrapper">
                                        <span class="elementor-button-icon">
                                            <svg aria-hidden="true" class="e-font-icon-svg e-fas-tags" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M497.941 225.941L286.059 14.059A48 48 0 0 0 252.118 0H48C21.49 0 0 21.49 0 48v204.118a48 48 0 0 0 14.059 33.941l211.882 211.882c18.744 18.745 49.136 18.746 67.882 0l204.118-204.118c18.745-18.745 18.745-49.137 0-67.882zM112 160c-26.51 0-48-21.49-48-48s21.49-48 48-48 48 21.49 48 48-21.49 48-48 48zm513.941 133.823L421.823 497.941c-18.745 18.745-49.137 18.745-67.882 0l-.36-.36L527.64 323.522c16.999-16.999 26.36-39.6 26.36-63.64s-9.362-46.641-26.36-63.64L331.397 0h48.721a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882z"></path></svg>
                                        </span>
                                        <span class="elementor-button-text">COMPRAR POR R$ <?php echo number_format($produto_encontrado['preco'], 2, ',', '.'); ?></span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- Página 404 -->
    <section class="bg-tt-top elementor-section elementor-top-section elementor-element elementor-element-6b891a61 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-element_type="section" data-settings='{"background_background":"classic"}'>
        <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-28ec5e88" data-id="28ec5e88" data-element_type="column">
                <div class="elementor-widget-wrap elementor-element-populated">
                    <div class="elementor-element elementor-element-218477ce elementor-widget elementor-widget-heading" data-id="218477ce" data-element_type="widget" data-widget_type="heading.default">
                        <div class="elementor-widget-container">
                            <h2 class="elementor-heading-title elementor-size-default tt-top">PÁGINA NÃO ENCONTRADA</h2>
                        </div>
                    </div>
                    <div class="elementor-element elementor-element-2781a2c elementor-widget elementor-widget-text-editor" data-id="2781a2c" data-element_type="widget" data-widget_type="text-editor.default">
                        <div class="elementor-widget-container">
                            <p>A página que você está procurando não foi encontrada.</p>
                            <p><a href="/">Voltar para a página inicial</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Footer -->
<section class="elementor-section elementor-top-section elementor-element elementor-element-6148df40 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="6148df40" data-element_type="section" data-settings='{"background_background":"classic"}'>
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-5f4f513d" data-id="5f4f513d" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-60321614 elementor-widget elementor-widget-image" data-id="60321614" data-element_type="widget" data-widget_type="image.default">
                    <div class="elementor-widget-container">
                        <img loading="lazy" decoding="async" width="180" height="60" src="assets-listas-lp/images/logo-led.png" class="attachment-large size-large wp-image-2729" alt="">
                    </div>
                </div>
                <div class="elementor-element elementor-element-a40e66e elementor-widget elementor-widget-text-editor" data-id="a40e66e" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        <p>Copyright <b>© Agência LED</b></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript -->
<script type="text/javascript" src="assets-listas-lp/js/hooks.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/i18n.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/hello-academy-scripts.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/webpack.runtime.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/frontend-modules.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/core.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/frontend.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/wp-consent-api.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/webpack-pro.runtime.min.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/frontend.min_1.js"></script>
<script type="text/javascript" src="assets-listas-lp/js/elements-handlers.min.js"></script>

<script>
// Lazy loading para backgrounds
const lazyloadRunObserver = () => {
    const lazyloadBackgrounds = document.querySelectorAll('.e-con.e-parent:not(.e-lazyloaded)');
    const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                let lazyloadBackground = entry.target;
                if(lazyloadBackground) {
                    lazyloadBackground.classList.add('e-lazyloaded');
                }
                lazyloadBackgroundObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '200px 0px 200px 0px' });
    lazyloadBackgrounds.forEach((lazyloadBackground) => {
        lazyloadBackgroundObserver.observe(lazyloadBackground);
    });
};

const events = [
    'DOMContentLoaded',
    'elementor/lazyload/observe',
];

events.forEach((event) => {
    document.addEventListener(event, lazyloadRunObserver);
});

// Configuração do Elementor
var elementorFrontendConfig = {
    "environmentMode": {"edit": false, "wpPreview": false, "isScriptDebug": false},
    "i18n": {
        "shareOnFacebook": "Compartilhar no Facebook",
        "shareOnTwitter": "Compartilhar no Twitter",
        "pinIt": "Fixar",
        "download": "Baixar",
        "downloadImage": "Baixar imagem",
        "fullscreen": "Tela cheia",
        "zoom": "Zoom",
        "share": "Compartilhar",
        "playVideo": "Reproduzir vídeo",
        "previous": "Anterior",
        "next": "Próximo",
        "close": "Fechar"
    },
    "is_rtl": false,
    "breakpoints": {"xs": 0, "sm": 480, "md": 768, "lg": 1025, "xl": 1440, "xxl": 1600},
    "responsive": {
        "breakpoints": {
            "mobile": {"label": "Dispositivos móveis no modo retrato", "value": 767, "default_value": 767, "direction": "max", "is_enabled": true},
            "tablet": {"label": "Tablet no modo retrato", "value": 1024, "default_value": 1024, "direction": "max", "is_enabled": true}
        }
    },
    "version": "3.29.2",
    "is_static": false,
    "urls": {"assets": "assets-listas-lp/"},
    "kit": {
        "active_breakpoints": ["viewport_mobile", "viewport_tablet"],
        "global_image_lightbox": "yes",
        "lightbox_enable_counter": "yes",
        "lightbox_enable_fullscreen": "yes",
        "lightbox_enable_zoom": "yes",
        "lightbox_enable_share": "yes",
        "lightbox_title_src": "title",
        "lightbox_description_src": "description"
    },
    "post": {"id": 2573, "title": "Lista de Fornecedores - Agência Led", "excerpt": "", "featuredImage": false}
};
</script>

</body>
</html> 