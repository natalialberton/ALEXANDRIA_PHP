<?php

require_once ('../../funcoes.php');

$categorias = listar('categoria');
$autores = listar('autor');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    cadastrarLivro();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIÇÃO</title>
    <link rel="stylesheet" href="../../static/css/signIn.css">
    <script src="../../static/javascript/geral.js"></script>
</head>

<main class="main-content">
<div class="container">
<h1>EDIÇÃO LIVRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="acao" value="cadastrar_livro">
                <label for="liv_titulo">Título: </label>
                <input type="text" name="liv_titulo" id="liv_titulo" required>
            </div>
            <div class="form-group">
                <label for="liv_isbn">ISBN: </label>
                <input type="text" name="liv_isbn" id="liv_isbn" required 
                    title="978-85-333-0227-3"
                    maxlength="17"
                    onkeypress="mascara(this,isbnMasc)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="fk_aut">Autor: </label>
                <datalist class="input-cadastro" name="fk_aut" id="fk_aut" required>
                    <?php foreach ($autores as $autor): ?>
                        <option value="<?=htmlspecialchars($autor['pk_aut']); ?>">
                            <?= htmlspecialchars($autor['aut_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="fk_cat">Categoria: </label>
                <datalist class="input-cadastro" name="fk_cat" id="fk_cat" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?=htmlspecialchars($categoria['pk_cat']); ?>">
                            <?= htmlspecialchars($categoria['cat_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="liv_edicao">Edição: </label>
                <input type="text" name="liv_edicao" id="liv_edicao" required>
            </div>

            <div class="form-group">
                <label for="liv_anoPublicacao">Ano Publicação: </label>
                <input type="text" name="liv_anoPublicacao" id="liv_anoPublicacao" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="liv_dataAlteracaoEstoque">Data Alteração Estoque: </label>
                <input type="date" name="liv_dataAlteracaoEstoque" id="liv_dataAlteracaoEstoque" 
                    value="<?php echo date('d-m-Y'); ?>">
            </div>

            <div class="form-group">
                <label for="liv_estoque">Estoque: </label>
                <input type="text" name="liv_estoque" id="liv_estoque" >
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="liv_num_paginas">Número Páginas: </label>
                <input type="text" name="liv_num_paginas" id="liv_num_paginas">
            </div>

            <div class="form-group">
                <label for="liv_idioma">Idioma: </label>
                <input type="text" name="liv_idioma" id="liv_idioma" >
            </div>
        </div>

        <div class="form-group">
            <label for="liv_sinopse">Sinopse: </label>
            <input type="textarea" name="liv_sinopse" id="liv_sinopse" required minlength="6" maxlength="6">
        </div>

        <div class="form-row">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <a href="../gestao/livro-gestao.php" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
</main>
</body>
</html>