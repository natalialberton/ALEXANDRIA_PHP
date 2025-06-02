<?php

require_once "../../funcoes.php";

$remessas = listar('remessa');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "REMESSAS";
$tituloH1= "GESTÃO REMESSAS";
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>REGISTRO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="remessa-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVA REMESSA</span>
            </a>
        </div>
    </div>
    
    <div class = "titulo">
        <h2>Remessas</h2>
    </div>

    <div class='titleliv'>
        <div class="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <th>Código</th>
                        <!--<th>Livro</th>-->
                        <!--<th>Membro</th>-->
                        <!--<th>Fornecedor</th>-->
                        <th>Quantidade</th>
                        <th>Data</th>
                        <!--<th>Ação</th>-->
                    </tr>
                    <?php foreach ($remessas as $remessa): ?>
                        <tr>
                            <td><?= htmlspecialchars($remessa['pk_rem']) ?></td>
                            <td><?= htmlspecialchars($remessa["rem_qtd"]) ?></td>
                            <td><?= htmlspecialchars($remessa["rem_data"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</main>
</body>
</html>