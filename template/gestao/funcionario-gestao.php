<?php

session_start();
require_once "../../geral.php";

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
}

if($_SESSION['tipoUser'] !== 'Administrador') {
    enviarSweetAlert('home.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'cadastrar_usuario') {
            crudFuncionario(1, '');
        }
    }
}

$_SESSION['tabela'] = 'usuario';

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "FUNCIONÁRIOS";
$tituloH1= "GESTÃO FUNCIONÁRIOS";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>CADASTRAMENTO</h2>
            <button class="action-btn" id="btn_abrePopupCadastro" onclick="abrePopup('popupCadastroUsuario')"><span class="plus-icon">+</span>NOVO FUNCIONÁRIO</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>FUNCIONÁRIOS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, nome ou CPF" oninput="pesquisarDadoTabela('usuario')">
        </div>
    </div>

    <div class='titleliv'>
        <div class="tabela" id="container-tabela">
            <div class="tisch">
                <?php include 'tabelas.php'; ?>
            </div>
        </div>
    </div>
</main>

<!--POPUP CADASTRAMENTO-->
<dialog class="popup" id="popupCadastroUsuario">
<div class="popup-content">
<div class="popup__container">
<h1>CADASTRAMENTO FUNCIONÁRIO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_usuario">
                <label for="user_nome">Nome: </label>
                <input type="text" name="user_nome" id="cad_nome" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="user_cpf">CPF: </label>
                <input type="text" name="user_cpf" id="cad_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_telefone">Telefone: </label>
                <input type="tel" name="user_telefone" id="cad_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)">
            </div>

            <div class="form-group">
                <label for="user_email">Email: </label>
                <input type="email" name="user_email" id="cad_email" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="user_dataAdmissao">Admissão: </label>
                <input type="date" name="user_dataAdmissao" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="user_dataDemissao">Demissão: </label>
                <input type="date" name="user_dataDemissao">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="user_tipoUser">Cargo: </label>
                <select class="input-cadastro" name="user_tipoUser" required>
                    <option value="Administrador" selected>Administrador</option>
                    <option value="Secretaria">Secretaria</option>
                    <option value="Almoxarife">Almoxarife</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_login">Login: </label>
                <input type="text" name="user_login" id="cad_login" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="user_senha">Senha: </label>
                <input type="password" name="user_senha" id="cad_senha" required minlength="8">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" id="cad_btn" type="submit">Cadastrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroUsuario')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoUsuario">
<?php

    if (isset($_GET['id'])) {
        $idFunc = $_GET['id'];
        $usuario = selecionarPorId('usuario', $idFunc, 'pk_user');
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_funcionario') {
                crudFuncionario(2, $idFunc);
            }
        }
    }

    if ($usuario) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO FUNCIONÁRIO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="editar-id" value="<?= $idFunc ?? '' ?>">
                <input type="hidden" name="form-id" value="editar_funcionario">
                <label for="user_nome">Nome: </label>
                <input type="text" name="user_nome" onkeypress="mascara(this,nomeMasc)" 
                       value="<?=htmlspecialchars($usuario['user_nome'])?>" required>
            </div>
            <div class="form-group">
                <label for="user_cpf">CPF: </label>
                <input type="text" name="user_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)"
                    value="<?=htmlspecialchars($usuario['user_cpf'])?>">
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
                    value="<?=htmlspecialchars($usuario['user_telefone'])?>">
            </div>

            <div class="form-group">
                <label for="user_email">Email: </label>
                <input type="email" name="user_email" value="<?=htmlspecialchars($usuario['user_email'])?>" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="user_dataAdmissao">Admissão: </label>
                <input type="date" name="user_dataAdmissao" value="<?=htmlspecialchars($usuario['user_dataAdmissao']) ?? null?>">
            </div>

            <div class="form-group">
                <label for="user_dataDemissao">Demissão: </label>
                <input type="date" name="user_dataDemissao" value="<?=htmlspecialchars($usuario['user_dataDemissao']) ?? null?>">
            </div>
        </div>

        <?php if($_SESSION['pk_user'] !== $idFunc): ?>
        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="user_tipoUser">Cargo: </label>
                <select class="input-cadastro" name="user_tipoUser" required>
                    <option value="Administrador" <?= ($usuario['user_tipoUser'] ?? '') === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    <option value="Secretaria" <?= ($usuario['user_tipoUser'] ?? '') === 'Secretaria' ? 'selected' : '' ?>>Secretaria</option>
                    <option value="Almoxarife" <?= ($usuario['user_tipoUser'] ?? '') === 'Almoxarife' ? 'selected' : '' ?>>Almoxarife</option>
                </select>
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="user_status">Status: </label>
                <select class="input-cadastro" name="user_status" required>
                    <option value="Ativo" <?= ($usuario['user_status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Inativo" <?= ($usuario['user_status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
        </div>
        <?php endif ?>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='funcionario-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?> 
</dialog>
</body>
</html>