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
        if($_POST['form-id'] === 'cadastrar_multa') {
            crudMulta(1, '');
        } elseif ($_POST['form-id'] === 'excluir_multa') {
            crudMulta(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'multa';
$emprestimos = listar('emprestimo');
$membros = listar('membro');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "MULTAS";
$tituloH1= "GESTÃO MULTAS";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>GERAL</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroMulta')"><span class="plus-icon">+</span>NOVA MULTA</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>MULTAS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, status" oninput="pesquisarDadoTabela('multa')">
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

<!--POPUP CADASTRAMENTO-->
<dialog class="popup" id="popupCadastroMulta">
<div class="popup-content">
<div class="popup__container">
<h1>NOVA MULTA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_multa">
                <div class="form-group">
                <label class="label-cadastro" for="fk_mem">CPF Membro: </label>
                <input list="membros" name="fk_mem" onkeypress="mascara(this,cpfMasc)" maxlength="14" required>
                <datalist class="input-cadastro" id="membros">
                    <?php foreach ($membros as $membro): ?>
                        <option value="<?=htmlspecialchars($membro['mem_cpf']); ?>">
                            <?=htmlspecialchars($membro['mem_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="fk_emp">ID Empréstimo: </label>
                <input list="emprestimos" name="fk_emp" required>
                <datalist class="input-cadastro" id="emprestimos">
                    <?php foreach ($emprestimos as $emprestimo): ?>
                        <option value="<?=htmlspecialchars($emprestimo['pk_emp']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="mul_qtdDias">Dias de Atraso: </label>
                <input type="number" name="mul_qtdDias" required>
            </div>

            <div class="form-group">
                <label for="mul_valor">Valor: </label>
                <input type="text" name="mul_valor" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroMulta')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDICAO-->
<dialog class="popup" id="popupEdicaoMulta">
<?php

    if (isset($_GET['id'])) {
        $idMul = $_GET['id'];
        $multa = selecionarPorId('multa', $idMul, 'pk_mul');
        $memOriginal = selecionarPorId('membro', $multa['fk_mem'], 'pk_mem');
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_multa') {
                crudMulta(2, $idMul);
            }
        }
    }

    if ($multa) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO MULTA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="editar-id" value="<?= $idMul ?? '' ?>">
                <input type="hidden" name="form-id" value="editar_multa">
                
                <div class="form-group">
                 <label class="label-cadastro" for="fk_forn">CNPJ Fornecedor: </label>
                <input list="fornecedores" name="fk_forn" onkeypress="mascara(this,cnpjMasc)" 
                       maxlength="18" value="<?= $fornOriginal['forn_cnpj'] ?>" required>
                <datalist class="input-cadastro" id="fornecedores">
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?=htmlspecialchars($fornecedor['forn_cnpj']); ?>">
                            <?=htmlspecialchars($fornecedor['forn_nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="fk_liv">ISBN Livro: </label>
                <input list="livros" name="fk_liv" maxlength="17" value="<?= $livroOriginal['liv_isbn'] ?>"
                       onkeypress="mascara(this,isbnMasc)" required>
                <datalist class="input-cadastro" id="livros">
                    <?php foreach ($livros as $livro): ?>
                        <option value="<?=htmlspecialchars($livro['liv_isbn']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rem_data">Data: </label>
                <input type="date" name="rem_data" value="<?= $fornOriginal['forn_cnpj'] ?>" required>
            </div>

            <div class="form-group">
                <label for="res_qtd">Quantidade: </label>
                <input type="number" name="res_qtd" required>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='multa-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>

</body>
</html>