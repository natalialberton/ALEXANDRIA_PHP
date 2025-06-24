<?php

session_start();
require_once "../../geral.php";

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
}

if($_SESSION['tipoUser'] === 'Almoxarife') {
    enviarSweetAlert('home.php', 'erroAlerta', 'Acesso a página negado!');
}

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'cadastrar_emprestimo') {
            crudEmprestimo(1, '');
        } elseif ($_POST['form-id'] === 'excluir_emprestimo') {
            crudEmprestimo(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'emprestimo';
$livros = listar('livro');
$membros = listar('membro');
$usuarios = listar('usuario');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "EMPRÉSTIMOS";
$tituloH1= "GESTÃO EMPRÉSTIMOS";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>GERAL</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroEmprestimo')"><span class="plus-icon">+</span>NOVO EMPRÉSTIMO</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>EMPRÉSTIMOS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, status" oninput="pesquisarDadoTabela('emprestimo')">
        </div>
    </div>

    <div class='titleliv'>
        <div class="tabela" id="container-tabela">
            <div class="tisch">
                <?php include 'tabelas.php'; ?>
            </div>
        </div>
    </div>
</main>
</body>
</html>