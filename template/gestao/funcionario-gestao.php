<?php

session_start();
require_once "../../geral.php";

if($_SESSION['statusUser'] !== 'Ativo' || $_SESSION['tipoUser'] !== 'Administrador') {
    enviarSweetAlert('home.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'cadastrar_usuario') {
            crudFuncionario(1, '');
        } elseif ($_POST['form-id'] === 'excluir_usuario') {
            crudFuncionario(3, $_POST['id']);
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
            <button class="action-btn" onclick="abrePopup('popupCadastroUsuario')"><span class="plus-icon">+</span>NOVO FUNCIONÁRIO</button>
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
<div class="container">
<h1>CADASTRAMENTO FUNCIONÁRIO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_usuario">
                <label for="user_nome">Nome: </label>
                <input type="text" name="user_nome" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="user_cpf">CPF: </label>
                <input type="text" name="user_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_telefone">Telefone: </label>
                <input type="tel" name="user_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)">
            </div>

            <div class="form-group">
                <label for="user_email">Email: </label>
                <input type="email" name="user_email" required>
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
                <label class="label-cadastro" for="user_status">Status: </label>
                <select class="input-cadastro" name="user_status" required>
                    <option value="Administrador">Administrador</option>
                    <option value="Secretaria">Secretaria</option>
                    <option value="Almoxarife">Almoxarife</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_login">Login: </label>
                <input type="text" name="user_login" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="user_senha">Senha: </label>
                <input type="password" name="user_senha" required minlength="8">
            </div>
        </div>

        <div class="form-row">
            <p>Confira os dados com atenção! O sistema só permite a exclusão de funcionário por 1 hora após seu cadastro!</p>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroUsuario')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>
</body>
</html>