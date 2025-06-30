<?php

session_start();
require_once '../../geral.php';

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
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
            <button class="action-btn" id="btn_abrePopupCadastro" onclick="abrePopup('popupCadastroLivro')"><span class="plus-icon">+</span>NOVO LIVRO</button>
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
<div class="popup__container">
<h1>CADASTRAMENTO LIVRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_livro">
                <label for="liv_titulo">Título: </label>
                <input type="text" name="liv_titulo" id="cad_titulo" required>
            </div>

            <div class="form-group">
                <label for="liv_isbn">ISBN: </label>
                <input type="text" name="liv_isbn" maxlength="17" id="cad_isbn"
                       onkeypress="mascara(this,isbnMasc)" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="aut_nome">Autor: </label>
                <input list="autores" name="aut_nome" id="cad_autor" required>
                <datalist class="input-cadastro" id="autores">
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?=htmlspecialchars($autor['aut_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="cat_nome">Categoria: </label>
                <input list="categorias" name="cat_nome" id="cad_categoria" required>
                <datalist class="input-cadastro" id="categorias">
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?=htmlspecialchars($categoria['cat_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_edicao">Edição: </label>
                <input type="int" name="liv_edicao" id="cad_edicao" required>
            </div>

            <div class="form-group">
                <label for="liv_anoPublicacao">Ano Publicação: </label>
                <input type="number" name="liv_anoPublicacao" id="cad_anoPublicacao" maxlength="4" required>
            </div>

            <div class="form-group">
                <label for="liv_num_paginas">Nº Páginas: </label>
                <input type="int" name="liv_num_paginas" id="cad_nPaginas" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_idioma">Idioma: </label>
                <input type="text" name="liv_idioma" id="cad_idioma" required>
            </div>

            <div class="form-group">
                <label for="liv_estoque">Estoque: </label>
                <input type="int" name="liv_estoque" id="cad_estoque" required>
            </div>

            <div class="form-group">
                <label for="liv_dataAlteracaoEstoque">Data Alteração Estoque: </label>
                <input type="date" name="liv_dataAlteracaoEstoque" id="cad_dataAlteracaoEstoque"
                       value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_sinopse">Sinopse: </label>
                <input type="text" name="liv_sinopse" id="cad_sinopse" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit" id="cad_btn">Cadastrar</button>
            <button class="btn btn-cancel" onclick="fechaPopup('popupCadastroLivro')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoLivro">
<?php

    if (isset($_GET['id'])) {
        $idLivro= $_GET['id'];
        $livro = selecionarPorId('livro', $idLivro, 'pk_liv');
        $categoriaOriginal = selecionarPorId('categoria', $livro['fk_cat'], 'pk_cat');
        $autorOriginal = selecionarPorId('autor', $livro['fk_aut'], 'pk_aut');
        $timestamp = $livro['liv_dataAlteracaoEstoque'];
        $dataFormatada = date('Y-m-d', strtotime($timestamp));
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_livro') {
                crudLivro(2, $idLivro);
            }
        }
    }

    if ($livro) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO LIVRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="editar_livro">
                <input type="hidden" name="editar-id" value="<?= $idLivro ?? '' ?>">
                <label for="liv_titulo">Título: </label>
                <input type="text" name="liv_titulo" value="<?= htmlspecialchars($livro['liv_titulo']) ?>" required>
            </div>

            <div class="form-group">
                <label for="liv_isbn">ISBN: </label>
                <input type="text" name="liv_isbn" maxlength="16" maxlength="17" 
                       onkeypress="mascara(this,isbnMasc)" value="<?= htmlspecialchars($livro['liv_isbn']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="aut_nome">Autor: </label>
                <input list="autores" name="aut_nome" required
                       value="<?= htmlspecialchars($autorOriginal['aut_nome']) ?? '' ?>">
                <datalist class="input-cadastro" id="autores">
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?=htmlspecialchars($autor['aut_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="cat_nome">Categoria: </label>
                <input list="categorias" name="cat_nome" required
                       value="<?= htmlspecialchars($categoriaOriginal['cat_nome']) ?? '' ?>">
                <datalist class="input-cadastro" id="categorias">
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?=htmlspecialchars($categoria['cat_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_edicao">Edição: </label>
                <input type="int" name="liv_edicao" value="<?= htmlspecialchars($livro['liv_edicao']) ?>" required>
            </div>

            <div class="form-group">
                <label for="liv_anoPublicacao">Ano Publicação: </label>
                <input type="number" name="liv_anoPublicacao" maxlength="4" value="<?= htmlspecialchars($livro['liv_anoPublicacao']) ?>" required>
            </div>

            <div class="form-group">
                <label for="liv_num_paginas">Nº Páginas: </label>
                <input type="int" name="liv_num_paginas" value="<?= htmlspecialchars($livro['liv_num_paginas']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_idioma">Idioma: </label>
                <input type="text" name="liv_idioma" value="<?= htmlspecialchars($livro['liv_idioma']) ?>" required>
            </div>

            <div class="form-group">
                <label for="liv_estoque">Estoque: </label>
                <input type="int" name="liv_estoque" value="<?= htmlspecialchars($livro['liv_estoque']) ?>" required>
            </div>

            <div class="form-group">
                <label for="liv_dataAlteracaoEstoque">Data Alteração Estoque: </label>
                <input type="date" name="liv_dataAlteracaoEstoque" value="<?= htmlspecialchars($dataFormatada) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_sinopse">Sinopse: </label>
                <input type="text" name="liv_sinopse" value="<?= htmlspecialchars($livro['liv_sinopse']) ?>" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='livro-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>
</body>
</html>