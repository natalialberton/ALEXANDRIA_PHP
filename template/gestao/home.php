<?php

require_once '../../funcoes.php';
session_start();

if(!isset($_SESSION['pk_user'])) {
    echo "<script> alert('Você precisa estar logado para acessar esta página!'); </script>";
    header('Location: ../index.php?erro=2');
    exit();
}

$livros = listar('livro');
$categorias = listar('categoria');
$autores = listar('autor');
$qtdLivros = contarTotal('livro');
$qtdCategorias = contarTotal('categoria');
$qtdAutores = contarTotal('autor');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "HOME";
$tituloH1= "HOME | " . $_SESSION['user_nome'];
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>Geral</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="../cadastro/livro-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVO EMPRESTIMO</span>
            </a>  
            <a class="action-btn">
                <span class="plus-icon">+</span>
               +Registrar Renovação
            </a>
         
        </div>

        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-title">ATRASADO</div>
                <div class="stat-number"><?= $qtdLivros['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">A VENCER</div>
                <div class="stat-number"><?= $qtdCategorias['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">NO PRAZO</div>
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
                <!--<input type="text" class="search-input" placeholder="🔍 Pesquisar">-->
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
                            <tr><!--aqui-->
                                <td><?= htmlspecialchars($livro["liv_titulo"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_isbn"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_edicao"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_anoPublicacao"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_num_paginas"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_estoque"]) ?></td>
                                <td><?= htmlspecialchars($livro["liv_dataAlteracaoEstoque"]) ?></td>
                                <td>
                                    <a href="../../excluir-livro.php?id=<?=$livro['pk_liv']?>" >
                                        <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                    </a>
                                    <a href="../../editar-livro.php?id=<?=$livro['pk_liv']?>">
                                        <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class = "titulo"><h2>Categorias</h2></div>

        <div class='titleliv'>
            <div class="tabela">
                <div class="tisch">
                    <table>
                        <tr>
                            <th>Categoria</th>
                            <!--<th>Quantidade de Livros</th>-->
                            <th>Ação</th>
                        </tr>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?= htmlspecialchars($categoria["cat_nome"]) ?></td>
                                <!--<td><?= htmlspecialchars($categoria["pk_cat"]) ?></td>-->
                                <td>
                                    <i class='fas fa-trash-alt'
                                        style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="search-section">
            <div class="titulo">
                <h2>Autor</h2>
            </div>
            <div class='barra'>
                <input type="text" class="search-input" placeholder="🔍 Pesquisar">
            </div>
        </div>
    </main>
</body>

</html>