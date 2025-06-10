<?php
require_once "../../funcoes.php";
session_start();

$autores = listar('autor');
$qtdAutores = contarTotal('autor');

$tituloPagina = "AUTORES";
$tituloH1 = 'Gestão Autores';
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
                NOVO AUTOR
            </a>
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

        <div class='titleliv'>
            <div class="tabela">
                <div class="tisch">
                    <table>
                        <tr>
                            <th>Nome</th>
                            <th>Sobrenome</th>
                            <th>Data de Nascimento</th>
                            <th>Ação</th>
                        </tr>
                        <?php foreach ($autores as $autor): ?>
                            <tr>
                                <td><?=htmlspecialchars($autor["aut_nome"])?></td>
                                <td><?=htmlspecialchars($autor["aut_data_nascimento"])?></td>
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