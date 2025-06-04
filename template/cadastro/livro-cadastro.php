<!--

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "CADASTRO LIVRO";
$tituloH1= "CADASTRAMENTO LIVRO";
include '../header.php';

-->

<head>
    <link rel="stylesheet" href="../../static/css/signIn.css">
</head>
<main>
    <div class="container">
        <h1>Cadastramento Livro</h1>
        
        <form>
            <div class="form-row">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo">
                </div>
                <div class="form-group">
                    <label for="autor">Autor</label>
                    <input type="text" id="autor" name="autor">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <input type="text" id="categoria" name="categoria">
                </div>
                <div class="form-group">
                    <label for="dataPublicacao">Data Publicação</label>
                    <input type="date" id="dataPublicacao" name="dataPublicacao">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn">
                </div>
                <div class="form-group">
                    <label for="estoque">Estoque</label>
                    <input type="number" id="estoque" name="estoque">
                </div>
            </div>

            <div class="form-group">
                <label for="sinopse">Sinopse</label>
                <textarea id="sinopse" name="sinopse" placeholder="Digite a sinopse do livro..."></textarea>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-cancel">Cancelar</button>
                <button type="submit" class="btn btn-save">Salvar</button>
            </div>
        </form>
    </div>
</main>
</html>