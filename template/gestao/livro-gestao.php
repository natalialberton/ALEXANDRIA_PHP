<?php

session_start();
require_once '../../geral.php';

if($_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('home.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] == 'cadastrar_livro') {
            crudLivro(1, '');
        } elseif ($_POST['form-id'] == 'excluir_livro') {
            crudLivro(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'livro';
$categorias = listar('categoria');
$autores = listar('autor');
$qtdLivros = contarTotal('livro');
$qtdCategorias = contarTotal('categoria');
$qtdAutores = contarTotal('autor');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "LIVROS";
$tituloH1 = 'GESTÃO LIVROS';
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>CADASTRAMENTO</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroLivro')"><span class="plus-icon">+</span>NOVO LIVRO</button>
        </div>

        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-title">LIVROS</div>
                <div class="stat-number"><?= $qtdLivros['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">CATEGORIAS</div>
                <div class="stat-number"><?= $qtdCategorias['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">AUTORES</div>
                <div class="stat-number"><?= $qtdAutores['total'] ?></div>
            </div>
        </div>
    </div>

    <div class="search-section">
        <h2>LIVROS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, nome ou ISBN" oninput="pesquisarDadoTabela('livro')">
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
<dialog class="popup" id="popupCadastroLivro">
<div class="popup-content">
<div class="container">
<h1>CADASTRAMENTO LIVRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_livro">
                <label for="liv_titulo">Título: </label>
                <input type="text" name="liv_titulo" required>
            </div>

            <div class="form-group">
                <label for="aut_dataNascimento">Data Nascimento: </label>
                <input type="date" name="aut_dataNascimento" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="cat_nome">Gênero Literário: </label>
                <input list="categorias" name="cat_nome" required>
                <datalist class="input-cadastro" id="categorias">
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?=htmlspecialchars($categoria['cat_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <button class="btn btn-cancel" onclick="fechaPopup('popupCadastroAutor')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

</body>
</html>