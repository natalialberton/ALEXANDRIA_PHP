<?php

session_start();
require_once '../../geral.php';

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] == 'cadastrar_autor') {
            crudAutor(1, '');
        } elseif ($_POST['form-id'] == 'excluir_autor') {
            crudAutor(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'autor';
$categorias = listar('categoria');
$qtdLivros = contarTotal('livro');
$qtdCategorias = contarTotal('categoria');
$qtdAutores = contarTotal('autor');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "AUTORES";
$tituloH1= "GESTÃO AUTORES";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>CADASTRAMENTO</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroAutor')"><span class="plus-icon">+</span>NOVO AUTOR</button>
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
        <h2>AUTORES</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID ou nome" oninput="pesquisarDadoTabela('autor')">
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
<dialog class="popup" id="popupCadastroAutor">
<div class="popup-content">
<div class="popup__container">
<h1>CADASTRAMENTO AUTOR</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_autor">
                <label for="aut_nome">Nome: </label>
                <input type="text" name="aut_nome" onkeypress="mascara(this,nomeMasc)" required>
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

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoAutor">
<?php

    if (isset($_GET['id'])) {
        $idAutor = $_GET['id'];
        $autor = selecionarPorId('autor', $idAutor, 'pk_aut');
        $categoriaOriginal = selecionarPorId('categoria', $autor['fk_cat'], 'pk_cat');
        
        if (!$autor) {
            echo "<script>window.location.href='autor-gestao.php';</script>";
            exit();
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_autor') {
                crudAutor(2, $idAutor);
            }
        }
    }

    if ($autor) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO AUTOR</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="editar_autor">
                <input type="hidden" name="editar-id" value="<?= $idAutor ?? '' ?>">
                <label for="aut_nome">Nome: </label>
                <input type="text" name="aut_nome" onkeypress="mascara(this,nomeMasc)" required value="<?=$autor['aut_nome']?>">
            </div>
            <div class="form-group">
                <label for="aut_dataNascimento">Data Nascimento: </label>
                <input type="date" name="aut_dataNascimento" required value="<?=$autor['aut_dataNascimento']?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="cat_nome">Gênero Literário: </label>
                <input list="categorias" name="cat_nome" required
                       value="<?=htmlspecialchars($categoriaOriginal['cat_nome']) ?? ''?>">
                <datalist class="input-cadastro" id="categorias">
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?=htmlspecialchars($categoria['cat_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='autor-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>
</body>
</html>