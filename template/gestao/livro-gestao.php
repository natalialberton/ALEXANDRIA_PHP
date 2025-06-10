<?php
require_once "../../funcoes.php";
session_start();

$livros = listar('livro');
$qtdLivros = contarTotal('livro');
$qtdCategorias = contarTotal('categoria');
$qtdAutores = contarTotal('autor');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $busca = $_POST['busca'] ?? null;
    if ($busca) {
        $livros = pesquisar('livro', $busca, 'liv_titulo', 'liv_isbn');
        if (empty($livros)) {
            echo "<script> alert('Nenhum livro encontrado!'); </script>";
            exit();
        }
    }
}

$tituloPagina = "LIVROS";
$tituloH1 = 'Gestão Livros';
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>CADASTRAMENTO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="../cadastro/livro-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVO LIVRO</span>
            </a> 
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
        <div class="campo"></div>

        <div class="search-section">
            <div class="titulo">
                <h2>LIVROS</h2>
            </div>
            <div class='barra'>
                <form method="POST">
                    <input type="text" class="search-input" id="busca" placeholder="🔍 Título ou ISBN">
                    <button type="submit">Pesquisar</button>
                </form>
            </div>
        </div>

        <div class='titleliv'>
            <div class="tabela">
                <div class="tisch">
                    <table>
                        <tr>
                            <th>Título</th>
                            <th>ISBN</th>
                            <th>Edição</th>
                            <th>Ano Publicação</th>
                            <th>Páginas</th>
                            <th>Estoque</th>
                            <th>Alteração Estoque</th>
                            <th>Ação</th>
                        </tr>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <?php $autor = selecionarPorId('autor', $livro["fk_aut"], 'pk_aut');
                                      $categoria = selecionarPorId('categoria', $livro["fk_cat"], 'pk_cat'); 
                                ?>
                                <td><?= htmlspecialchars($livro["liv_titulo"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_isbn"]) ?></td>
                                <td><?= htmlspecialchars($autor['aut_nome']) ?></td>
                                <td><?= htmlspecialchars($categoria['cat_nome']) ?></td>
                                <td><?= htmlspecialchars($livro["liv_edicao"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_anoPublicacao"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_num_paginas"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_estoque"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_dataAlteracaoEstoque"]) ?></td>
                                <td>
                                    <a href="../../crud/excluir-livro.php?id=<?=$livro['pk_liv']?>" >
                                    <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                </a>
                                <a href="../edicao/livro-edicao.php?id=<?=$livro['pk_liv']?>">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>