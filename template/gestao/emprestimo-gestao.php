<?php

require_once "../../funcoes.php";

$emprestimos = listar('emprestimo');
$reservas = listar('reserva');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "EMPRÉSTIMOS";
$tituloH1= "GESTÃO EMPRÉSTIMOS";
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>REGISTRO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="emprestimo-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVO EMPRÉSTIMO</span>
            </a>
            <a href="reserva-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVA RESERVA</span>
            </a> 
        </div>
    </div>
    
    <div class = "titulo">
        <h2>Empréstimos</h2>
    </div>

    <div class='titleliv'>
        <div class="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <!--<th>Livro</th>-->
                        <!--<th>Membro</th>-->
                        <th>Data Empréstimo</th>
                        <th>Data Devolução</th>
                        <th>Status</th>
                        <!--<th>Ação</th>-->
                    </tr>
                    <?php foreach ($emprestimos as $emprestimo): ?>
                        <tr>
                            <td><?= htmlspecialchars($emprestimo["emp_dataEmp"]) ?></td>
                            <td><?= htmlspecialchars($emprestimo["emp_dataDev"]) ?></td>
                            <td><?= htmlspecialchars($emprestimo["emp_status"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

    <div class = "titulo">
        <h2>Reservas</h2>
    </div>

    <div class='titleliv'>
        <div class="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <!--<th>Livro</th>-->
                        <!--<th>Membro</th>-->
                        <th>Data Marcada</th>
                        <th>Vencimento</th>
                        <th>Finalização</th>
                        <th>Status</th>
                        <th>Observações</th>
                        <!--<th>Ação</th>-->
                    </tr>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva["res_dataMarcada"]) ?></td>
                            <td><?= htmlspecialchars($reserva["res_dataVencimento"]) ?></td>
                            <td><?= htmlspecialchars($reserva["res_dataFinalizada"]) ?></td>
                            <td><?= htmlspecialchars($reserva["res_status"]) ?></td>
                            <td><?= htmlspecialchars($reserva["res_observacoes"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</main>
</body>
</html>