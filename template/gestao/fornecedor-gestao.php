<?php

require_once "../../funcoes.php";

$fornecedores= listar('fornecedor');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "FORNECEDORES";
$tituloH1= "GESTÃO FORNECEDORES";
include '../header.php';

?>

<main class="main-content">
    <div class="titulo">
        <h2>CADASTRAMENTO</h2>
    </div>
    <div class="top-section">
        <div class="actions-section">
            <a href="fornecedor-cadastro.php" class="action-btn">
                <span class="plus-icon">+ NOVO FORNECEDOR</span>
            </a>     
        </div>
    </div>
    
    <div class = "titulo">
        <h2>Fornecedores</h2>
    </div>

    <div class='titleliv'>
        <div class="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Endereço</th>
                        <th>Ação</th>
                    </tr>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <tr>
                            <td><?= htmlspecialchars($fornecedor["forn_nome"]) ?></td>
                            <td><?= htmlspecialchars($fornecedor["forn_cnpj"]) ?></td>
                            <td><?= htmlspecialchars($fornecedor["forn_telefone"]) ?></td>
                            <td><?= htmlspecialchars($fornecedor["forn_email"]) ?></td>
                            <td><?= htmlspecialchars($fornecedor["forn_endereco"]) ?></td>
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