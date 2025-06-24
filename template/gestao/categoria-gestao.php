<?php

session_start();
require_once "../../geral.php";

if($_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('home.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] == 'cadastrar_categoria') {
            crudCategoria(1, '');
        } elseif ($_POST['form-id'] == 'excluir_categoria') {
            crudCategoria(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'categoria';
$qtdLivros = contarTotal('livro');
$qtdCategorias = contarTotal('categoria');
$qtdAutores = contarTotal('autor');

$tituloPagina = "CATEGORIAS";
$tituloH1 = 'Gestão Categorias';
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>CADASTRAMENTO</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroCategoria')"><span class="plus-icon">+</span>NOVA CATEGORIA</button>
        </div>

        <div class="stats-section">
            <a href="livro-gestao.php">
                <div class="stat-card">
                    <div class="stat-title">LIVROS</div>
                    <div class="stat-number"><?= $qtdLivros['total'] ?></div>
                </div>
            </a>
            <a href="categoria-gestao.php">
                <div class="stat-card">
                    <div class="stat-title">CATEGORIAS</div>
                    <div class="stat-number"><?= $qtdCategorias['total'] ?></div>
                </div>
            </a>
            <a href="autor-gestao.php">
                <div class="stat-card">
                    <div class="stat-title">AUTORES</div>
                    <div class="stat-number"><?= $qtdAutores['total'] ?></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="search-section">
        <h2>CATEGORIAS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID ou nome" oninput="pesquisarDadoTabela('categoria')">
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
<dialog class="popup" id="popupCadastroCategoria">
<div class="popup-content">
<div class="container">
<h1>CADASTRAMENTO CATEGORIA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_categoria">
                <label for="cat_nome">Nome: </label>
                <input type="text" name="cat_nome" onkeypress="mascara(this,nomeMasc)" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <button class="btn btn-cancel" onclick="fechaPopup('popupCadastroCategoria')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoCategoria">
<?php

    if (isset($_GET['id'])) {
        $idCategoria = $_GET['id'];
        $categoria = selecionarPorId('categoria', $idCategoria, 'pk_cat');
      
        if (!$categoria) {
            echo "<script>window.location.href='categoria-gestao.php';</script>";
            exit();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_categoria') {
                crudCategoria(2, $idCategoria);
            }
        }
    }

    if ($categoria) :
?>
<div class="popup-content">
<div class="container">
<h1>EDIÇÃO CATEGORIA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="editar_categoria">
                <input type="hidden" name="editar-id" value="<?= $idCategoria ?? '' ?>">
                <label for="cat_nome">Nome: </label>
                <input type="text" name="cat_nome" onkeypress="mascara(this,nomeMasc)" required value="<?=$categoria['cat_nome']?>">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='categoria-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>
</body>
</html>