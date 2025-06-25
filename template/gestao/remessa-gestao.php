<?php

session_start();
require_once "../../geral.php";

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
}

if($_SESSION['tipoUser'] === 'Secretaria') {
    enviarSweetAlert('home.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'cadastrar_remessa') {
            crudRemessa(1, '');
        } elseif ($_POST['form-id'] === 'excluir_remessa') {
            crudRemessa(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'remessa';
$livros = listar('livro');
$fornecedores = listar('fornecedor');
$usuarios = listar('usuario');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "REMESSAS";
$tituloH1= "GESTÃO REMESSAS";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>GERAL</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroRemessa')"><span class="plus-icon">+</span>NOVA remessa</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>REMESSAS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, data" oninput="pesquisarDadoTabela('remessa')">
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
<dialog class="popup" id="popupCadastroRemessa">
<div class="popup-content">
<div class="popup__container">
<h1>NOVA REMESSA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_remessa">
                <div class="form-group">
                <label class="label-cadastro" for="fk_forn">CNPJ Fornecedor: </label>
                <input list="fornecedores" name="fk_forn" onkeypress="mascara(this,cnpjMasc)" maxlength="18" required>
                <datalist class="input-cadastro" id="fornecedores">
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?=htmlspecialchars($fornecedor['forn_cnpj']); ?>">
                            <?=htmlspecialchars($fornecedor['forn_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="fk_liv">ISBN Livro: </label>
                <input list="livros" name="fk_liv" maxlength="17" onkeypress="mascara(this,isbnMasc)" required>
                <datalist class="input-cadastro" id="livros">
                    <?php foreach ($livros as $livro): ?>
                        <option value="<?=htmlspecialchars($livro['liv_isbn']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rem_data">Data: </label>
                <input type="date" name="rem_data" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="rem_qtd">Quantidade: </label>
                <input type="number" name="rem_qtd" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroRemessa')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP CADASTRAMENTO-->
<dialog class="popup" id="popupEdicaoRemessa">
<?php

    if (isset($_GET['id'])) {
        $idRem = $_GET['id'];
        $remessa = selecionarPorId('remessa', $idRem, 'pk_rem');
        $fornOriginal = selecionarPorId('fornecedor', $remessa['fk_forn'], 'pk_forn');
        $livroOriginal = selecionarPorId('livro', $remessa['fk_liv'], 'pk_liv');
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_remessa') {
                crudRemessa(2, $idRem);
            }
        }
    }

    if ($remessa) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO REMESSA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="editar-id" value="<?= $idRem ?? '' ?>">
                <input type="hidden" name="form-id" value="editar_remessa">
                
                <div class="form-group">
                 <label class="label-cadastro" for="fk_forn">CNPJ Fornecedor: </label>
                <input list="fornecedores" name="fk_forn" onkeypress="mascara(this,cnpjMasc)" 
                       maxlength="18" value="<?= $fornOriginal['forn_cnpj'] ?>" required>
                <datalist class="input-cadastro" id="fornecedores">
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?=htmlspecialchars($fornecedor['forn_cnpj']); ?>">
                            <?=htmlspecialchars($fornecedor['forn_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="fk_liv">ISBN Livro: </label>
                <input list="livros" name="fk_liv" maxlength="17" value="<?= $livroOriginal['liv_isbn'] ?>"
                       onkeypress="mascara(this,isbnMasc)" required>
                <datalist class="input-cadastro" id="livros">
                    <?php foreach ($livros as $livro): ?>
                        <option value="<?=htmlspecialchars($livro['liv_isbn']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rem_data">Data: </label>
                <input type="date" name="rem_data" value="<?= $fornOriginal['forn_cnpj'] ?>" required>
            </div>

            <div class="form-group">
                <label for="res_qtd">Quantidade: </label>
                <input type="number" name="res_qtd" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='remessa-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>

</body>
</html>