<?php

require_once "../../geral.php";

$funcionarios = listar('usuario');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "FUNCIONÁRIOS";
$tituloH1= "GESTÃO FUNCIONÁRIOS";
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>CADASTRAMENTO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="funcionario-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVO FUNCIONÁRIO</span>
            </a>     
        </div>
    </div>
    
    <div class = "titulo">
        <h2>Funcionário</h2>
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
                        <th>Admissão</th>
                        <th>Status</th>
                        <!--<th>Tipo Usuário</th>-->
                        <th>Ação</th>
                    </tr>
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr>
                            <td><?= htmlspecialchars($funcionario["user_nome"]) ?></td>
                            <td><?= htmlspecialchars($funcionario["user_cpf"]) ?></td>
                            <td><?= htmlspecialchars($funcionario["user_telefone"]) ?></td>
                            <td><?= htmlspecialchars($funcionario["user_email"]) ?></td>
                            <td><?= htmlspecialchars($funcionario["user_dataAdmissao"]) ?></td>
                            <td><?= htmlspecialchars($funcionario["user_status"]) ?></td>
                            <!--<td><?= htmlspecialchars($funcionario["fk_tipoUser"]) ?></td>-->
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