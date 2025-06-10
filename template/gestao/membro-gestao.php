<?php

require_once "../../funcoes.php";

$membros = listar('membro');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "MEMBROS";
$tituloH1= "GESTÃO MEMBROS";
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>CADASTRAMENTO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="../cadastro/membro-cadastro.php" class="action-btn">
                <span class="plus-icon">+</span>NOVO MEMBRO
            </a>
        </div>
    </div>
    
    <div class = "titulo">
        <h2>Membros</h2>
    </div>

    <div class='titleliv'>
        <div class="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Ação</th>
                    </tr>
                    <?php foreach ($membros as $membro): ?>
                        <tr>
                            <td><?= htmlspecialchars($membro["mem_nome"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_cpf"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_telefone"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_email"]) ?></td>
                            <td>
                                <a href="../../crud/excluir-membro.php?id=<?=$membro['pk_mem']?>" >
                                    <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                </a>
                                <a href="../edicao/membro-edicao.php?id=<?=$membro['pk_mem']?>">
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