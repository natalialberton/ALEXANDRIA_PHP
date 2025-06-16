<?php
require_once "../../funcoes.php";
session_start();

$categorias = listar('categoria');

$tituloPagina = "CATEGORIAS";
$tituloH1 = 'Gestão Categorias';
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>CADASTRAMENTO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a class="action-btn">
                <span class="plus-icon">+</span>
                NOVA CATEGORIA
            </a>
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
    </main>
</body>

</html>