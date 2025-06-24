<?php

session_start();
require_once '../../geral.php';

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] == 'cadastrar_fornecedor') {
            crudFornecedor(1, '');
        } elseif ($_POST['form-id'] == 'excluir_fornecedor') {
            crudFornecedor(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'fornecedor';

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "FORNECEDORES";
$tituloH1= "GESTÃO FORNECEDORES";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>CADASTRAMENTO</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroFornecedor')"><span class="plus-icon">+</span>NOVO FORNECEDOR</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>FORNECEDORES</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, nome ou CNPJ" oninput="pesquisarDadoTabela('fornecedor')">
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
<dialog class="popup" id="popupCadastroFornecedor">
<div class="popup-content">
<div class="container">
<h1>CADASTRAMENTO FORNECEDOR</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_fornecedor">
                <label for="forn_nome">Nome: </label>
                <input type="text" name="forn_nome" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="forn_cnpj">CNPJ: </label>
                <input type="text" name="forn_cnpj" required 
                    title="00.000.000/0000-00"
                    maxlength="18"
                    onkeypress="mascara(this,cnpjMasc)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="forn_telefone">Telefone: </label>
                <input type="tel" name="forn_telefone" required
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)">
            </div>

            <div class="form-group">
                <label for="forn_email">Email: </label>
                <input type="email" name="forn_email" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="forn_endereco">Endereço: </label>
                <input type="text" name="forn_endereco" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <button class="btn btn-cancel" onclick="fechaPopup('popupCadastroFornecedor')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoFornecedor">
<?php

    if (isset($_GET['id'])) {
        $idFornecedor = $_GET['id'];
        $fornecedor = selecionarPorId('fornecedor', $idFornecedor, 'pk_forn');
        
        if (!$fornecedor) {
            echo "<script>window.location.href='fornecedor-gestao.php';</script>";
            exit();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_fornecedor') {
                crudFornecedor(2, $idFornecedor);
            }
        }
    }

    if ($fornecedor) :
?>
<div class="popup-content">
<div class="container">
<h1>EDIÇÃO FORNECEDOR</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="editar_fornecedor">
                <input type="hidden" name="editar-id" value="<?= $idFornecedor ?? '' ?>">
                <label for="forn_nome">Nome: </label>
                <input type="text" name="forn_nome" onkeypress="mascara(this,nomeMasc)" required value="<?=$fornecedor['forn_nome']?>">
            </div>
            <div class="form-group">
                <label for="forn_cnpj">CNPJ: </label>
                <input type="text" name="forn_cnpj" required  
                    title="00.000.000/0000-00"
                    maxlength="18"
                    onkeypress="mascara(this,cnpjMasc)"
                    value="<?=$fornecedor['forn_cnpj']?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="forn_telefone">Telefone: </label>
                <input type="tel" name="forn_telefone" required

                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)"
                    value="<?=$fornecedor['forn_telefone']?>">
            </div>

            <div class="form-group">
                <label for="forn_email">Email: </label>
                <input type="email" name="forn_email" required value="<?=$fornecedor['forn_email']?>">
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="forn_endereco">Endereço: </label>
                <input type="text" name="forn_endereco" required value="<?=$fornecedor['forn_endereco']?>">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='fornecedor-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>
</body>
</html>