<?php

require_once '../../geral.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'logout') {
            logout();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo isset($tituloPagina) ? $tituloPagina : 'ALEXANDRIA'; ?></title>
    <link rel="stylesheet" href="../../static/css/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../static/css/geral.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../static/css/popup.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../static/css/dashboard.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/png" href="../../static/img/favicon.png">



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
            <a href="home.php" class="linkMenu"><i class='bx bx-home'></i>&nbspHome</a>
            <a href="livro-gestao.php" class="linkMenu"><i class='bx bx-book'></i>&nbspLivros</a>
            <a href="categoria-gestao.php" class="linkMenu"><i class='bx bx-folder'></i> &nbspCategorias</a>
            <a href="autor-gestao.php" class="linkMenu"><i class="fa-solid fa-pen-nib"></i>&nbspAutores</a>
            <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
            <a href="emprestimo-gestao.php" class="linkMenu"><i class='bx bx-folder-open'></i>&nbspEmpréstimos</a>
            <a href="reserva-gestao.php" class="linkMenu"><i class='bx bx-folder-open'></i>&nbspReservas</a>
            <a href="multa-gestao.php" class="linkMenu"><i class='bx bx-folder-open'></i>&nbspMultas</a>
            <a href="membro-gestao.php" class="linkMenu"><i class='bx bx-group'></i>&nbspMembros</a>
            <?php endif; ?>
            <?php if($_SESSION['tipoUser'] !== 'Secretaria'): ?>
            <a href="remessa-gestao.php" class="linkMenu">	<i class='bx bx-package'></i>&nbspRemessas</a>
            <a href="fornecedor-gestao.php" class="linkMenu"><i class='bx bx-receipt'></i>&nbspFornecedores</a>
            <?php endif; ?>
            <?php if($_SESSION['tipoUser'] === 'Administrador'): ?>
            <a href="funcionario-gestao.php" class="linkMenu"><i class='bx bx-id-card'></i>&nbspFuncionários</a>
            <a href="dashboard.php" class="linkMenu"><i class='bx bxs-grid-alt'></i>&nbspDashboard</a>
            <?php endif; ?>
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

        <button id="btnPerfil" onclick="toggleDropdown()">
            <i class="fas fa-circle-user"></i>
        </button>

        <div class="dropdown" id="dropdownMenu">
            <button onclick="DadosPessoais()">
                <i class="fas fa-user"></i>
                <span style="font-family: 'Montserrat'">Dados Pessoais</span>
            </button>
            <form method="POST">
                <input type="hidden" name="form-id" value="logout">
                <button type="submit">
                    <i class="fas fa-sign-out-alt"></i>
                    <span style="font-family: 'Montserrat'">Log Out</span>
                </button>
            </form>
        </div>
    </header>

<body>
    <script>
        const dropdown = document.getElementById('dropdownMenu');

        function toggleDropdown() {
            dropdown.classList.toggle('active');
        }

      
        document.addEventListener('click', function (e) {
            const button = document.getElementById('btnPerfil');
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>