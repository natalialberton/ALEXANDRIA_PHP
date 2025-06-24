<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--PEGANDO O TÍTULO DEFINIDO PELA PÁGINA; 'ALEXANDRIA', CASO NÃO ESTEJA DEFINIDO-->
    <title><?php echo isset($tituloPagina) ? $tituloPagina : 'ALEXANDRIA'; ?></title>
    <link rel="stylesheet" href="../../static/css/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../static/css/geral.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../static/css/popup.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../static/css/dashboard.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <script src="../../static/javascript/geral.js?v=<?= time() ?>"></script>
</head>

<body>
    <nav>
        <div id="iconeMenu">
            <div class="barrasIconeMenu" onclick="animaIconeMenu(this)">
                <div id="barra1"></div>
                <div id="barra2"></div>
                <div id="barra3"></div>
            </div>
        </div>

        <div id="navegaMenu" class="menuLateral">
            <h2 id="cabecalhoMenu">Menu</h2>
            <a href="home.php" class="linkMenu" >Home</a>
            <a href="livro-gestao.php" class="linkMenu">Acervo</a>
            <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
                <a href="emprestimo-gestao.php" class="linkMenu">Empréstimos</a>
            <?php endif;?>
            <?php if($_SESSION['tipoUser'] !== 'Secretaria'): ?>
                <a href="remessa-gestao.php" class="linkMenu">Remessas</a>
            <?php endif;?>
            <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
                <a href="membro-gestao.php" class="linkMenu">Membros</a>
            <?php endif;?>
            <a href="fornecedor-gestao.php" class="linkMenu">Fornecedores</a>
            <?php if($_SESSION['tipoUser'] === 'Administrador'): ?>
                <a href="funcionario-gestao.php" class="linkMenu">Funcionários</a>
            <?php endif;?>
            <?php if($_SESSION['tipoUser'] === 'Administrador'): ?>
                <a href="dashboard.php" class="linkMenu">Dashboard</a>
            <?php endif;?>
        </div>
    </nav>
    <header>
        <div id="logo" class="logo">
            <h1><?php echo isset($tituloH1) ? $tituloH1 : 'errinho eita'; ?></h1>
        </div>
        <div class="brand-section">
            <div class="alexandria-logo">
                <div class="img"> <img src="../../static/img/LOGO.png" class="logoGestao" alt="Logo Alexandria"> </div>
            </div>
        </div>
    </header>