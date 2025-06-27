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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<link rel="icon" type="image/png" href="../../static/img/favicon-light.png" media="(prefers-color-scheme: light)">
<link rel="icon" type="image/png" href="../../static/img/favicon-dark.png" media="(prefers-color-scheme: dark)">

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
            <a href="reserva-gestao.php" class="linkMenu"><i class="fa-regular fa-folder"></i></i>&nbspReservas</a>
            <a href="multa-gestao.php" class="linkMenu"><i class="fa-solid fa-money-bill"></i></i>&nbspMultas</a>
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
            <button onclick="location.href='?id=<?= $_SESSION['pk_user'] ?>#dadosPessoais'">
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
<!--POPUP DADOS PESSOAIS-->
<dialog class="popup" id="popupDadosPessoais">
<?php

if (isset($_GET['id'])) {
    $idUser = $_GET['id'];
    $usuarioLogado = selecionarPorId('usuario', $idUser, 'pk_user');
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'editar_usuarioLogado') {
            crudFuncionario(2, $idUser);
        }
    }
}

if ($usuarioLogado) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>DADOS PESSOAIS</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="editar_usuarioLogado">
                <input type="hidden" name="editar-id" value="<?= $idUser ?? '' ?>">
                <label for="user_nome">Nome: </label>
                <input type="text" name="user_nome" onkeypress="mascara(this,nomeMasc)" 
                       value="<?=htmlspecialchars($usuarioLogado['user_nome'])?>" required>
            </div>
            <div class="form-group">
                <label for="user_cpf">CPF: </label>
                <input type="text" name="user_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)"
                    value="<?=htmlspecialchars($usuarioLogado['user_cpf'])?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_telefone">Telefone: </label>
                <input type="tel" name="user_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)"
                    value="<?=htmlspecialchars($usuarioLogado['user_telefone'])?>">
            </div>

            <div class="form-group">
                <label for="user_email">Email: </label>
                <input type="email" name="user_email" value="<?=htmlspecialchars($usuarioLogado['user_email'])?>" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="user_dataAdmissao">Admissão: </label>
                <input type="date" name="user_dataAdmissao" value="<?=htmlspecialchars($usuarioLogado['user_dataAdmissao']) ?? null?>" required>
            </div>

            <div class="form-group">
                <label for="user_dataDemissao">Demissão: </label>
                <input type="date" name="user_dataDemissao" value="<?=htmlspecialchars($usuarioLogado['user_dataDemissao']) ?? null?>">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="user_tipoUser">Cargo: </label>
                <select class="input-cadastro" name="user_tipoUser" required>
                    <option value="Administrador" <?= ($usuarioLogado['user_tipoUser'] ?? '') === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    <option value="Secretaria" <?= ($usuarioLogado['user_tipoUser'] ?? '') === 'Secretaria' ? 'selected' : '' ?>>Secretaria</option>
                    <option value="Almoxarife" <?= ($usuarioLogado['user_tipoUser'] ?? '') === 'Almoxarife' ? 'selected' : '' ?>>Almoxarife</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_login">Login: </label>
                <input type="text" name="user_login" onkeypress="mascara(this,nomeMasc)" 
                       value="<?=htmlspecialchars($usuarioLogado['user_login'])?>"required>
            </div>
            <div class="form-group">
                <label for="user_senha">Senha: </label>
                <input type="password" name="user_senha" required minlength="8">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href=''">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>

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