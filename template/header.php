<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--PEGANDO O TÍTULO DEFINIDO PELA PÁGINA; 'ALEXANDRIA', CASO NÃO ESTEJA DEFINIDO-->
    <title><?php echo isset($tituloPagina) ? $tituloPagina : 'ALEXANDRIA'; ?></title>
    <link rel="stylesheet" href="../../static/css/bilubilu.css">
    <link rel="stylesheet" href="../../static/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../../static/js/geral.js"></script>
</head>

<body>
    <nav>
    <div id="iconeMenu">
        <!-- Adicionando um ícone animado para abrir o menu de navegação -->
        <div class="barrasIconeMenu" onclick="animaIconeMenu(this)">
            <div id="barra1"></div>
            <div id="barra2"></div>
            <div id="barra3"></div>
        </div>
    </div>

    <!-- Adicionando configuração para abrir um menu lateral -->
    <div id="navegaMenu" class="menuLateral">
        <h2 id="cabecalhoMenu">Menu</h2>
        <a href="home.php" target="_blank" class="linkMenu" >Home</a>
        <a href="livro-gestao.php" target="_blank" class="linkMenu">Livros</a>
        <a href="emprestimo-gestao.php" target="_blank" class="linkMenu">Empréstimos</a>
        <a href="remessa-gestao.php" target="_blank" class="linkMenu">Remessas</a>
        <a href="membro-gestao.php" target="_blank" class="linkMenu">Membros</a>
        <a href="fornecedor-gestao.php" target="_blank" class="linkMenu">Fornecedores</a>
        <a href="funcionario-gestao.php" target="_blank" class="linkMenu">Funcionários</a>
        <a href="dashboard.php" target="_blank" class="linkMenu">Dashboard</a>
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