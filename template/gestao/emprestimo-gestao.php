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
$qtdLivroAtrasado = contarTotalCondicional('emprestimo', "emp_status = 'Empréstimo Atrasado' OR emp_status = 'Renovação Atrasada'");
$qtdLivrosAVencer = contarTotalCondicional('emprestimo', "(emp_dataDev - emp_dataEmp) = 3");
$qtdLivrosNoPrazo = contarTotalCondicional('emprestimo', "emp_status = 'Empréstimo Ativo' OR emp_status = 'Renovação Ativa'");

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
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-title stat-title-atrasado">ATRASADO</div>
                <div class="stat-number stat-number-atrasado"><?= $qtdLivroAtrasado['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title stat-title-a-vencer">VENCE EM TRÊS DIAS</div>
                <div class="stat-number stat-number-a-vencer"><?= $qtdLivrosAVencer['total'] ?></div>
            </div>
            </a>
            <div class="stat-card">
                <div class="stat-title stat-title-no-prazo">NO PRAZO</div>
                <div class="stat-number stat-number-no-prazo"><?= $qtdLivrosNoPrazo['total'] ?></div>
            </div>
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

<!--POPUP CADASTRAMENTO-->
<dialog class="popup" id="popupCadastroEmprestimo">
<div class="popup-content">
<div class="popup__container">
<h1>NOVO EMPRÉSTIMO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_emprestimo">
                <div class="form-group">
                <label class="label-cadastro" for="fk_mem">CPF Membro: </label>
                <input list="membros" name="fk_mem" onkeypress="mascara(this,cpfMasc)" required>
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
                <label class="label-cadastro" for="fk_liv">ISBN Livro: </label>
                <input list="livros" name="fk_liv" required>
                <datalist class="input-cadastro" id="livros">
                    <?php foreach ($livros as $livro): ?>
                        <option value="<?=htmlspecialchars($livro['liv_isbn']); ?>">
                            <?=htmlspecialchars($livro['liv_titulo']); ?>
                        </option>
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="emp_dataEmp">Data: </label>
                <input type="date" name="emp_dataEmp" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="mem_senha">Senha Membro: </label>
                <input type="password" name="mem_senha" required minlength="6" maxlength="6">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroEmprestimo')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoEmprestimo">
<?php

    if (isset($_GET['id'])) {
        $idEmp = $_GET['id'];
        $emprestimo = selecionarPorId('emprestimo', $idEmp, 'pk_emp');
        $membroOriginal = selecionarPorId('membro', $emprestimo['fk_mem'], 'pk_mem');
        $livroOriginal = selecionarPorId('livro', $emprestimo['fk_liv'], 'pk_liv');
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_emprestimo') {
                crudEmprestimo(2, $idEmp);
            }
        }
    }

    if ($emprestimo) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO EMPRÉSTIMO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="editar-id" value="<?= $idEmp ?? '' ?>">
                <input type="hidden" name="form-id" value="editar_emprestimo">
                
                <div class="form-group">
                <label class="label-cadastro" for="fk_mem">CPF Membro: </label>
                <input list="membros" name="fk_mem" required
                       value="<?=htmlspecialchars($membroOriginal['mem_cpf']) ?? ''?>">
                <datalist class="input-cadastro" id="membros">
                    <?php foreach ($membros as $membro): ?>
                        <option value="<?=htmlspecialchars($membro['mem_cpf']); ?>"><?=htmlspecialchars($membro['mem_nome']); ?></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            </div>
            <div class="form-group">
                <label class="label-cadastro" for="fk_liv">ISBN Livro: </label>
                <input list="livros" name="fk_liv" required
                       value="<?=htmlspecialchars($livroOriginal['liv_isbn']) ?? ''?>">>
                <datalist class="input-cadastro" id="livros">
                    <?php foreach ($livros as $livro): ?>
                        <option value="<?=htmlspecialchars($livro['liv_isbn']); ?>"><?=htmlspecialchars($livro['liv_nome']); ?></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="emp_dataEmp">Data Empréstimo: </label>
                <input type="date" name="emp_dataEmp" value="<?=$emprestimo['emp_dataEmp']?>" required>
            </div>

            <div class="form-group">
                <label for="emp_dataDevReal">Devolução Real: </label>
                <input type="date" name="emp_dataDevReal" value="<?=$emprestimo['emp_dataDevReal'] ?? null?>">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="emp_status">Status: </label>
                <select class="input-cadastro" name="emp_status" required>
                    <option value="Empréstimo Ativo" <?= ($emprestimo['emp_status'] ?? '') === 'Empréstimo Ativo' ? 'selected' : '' ?>>Empréstimo Ativo</option>
                    <option value="Empréstimo Atrasado" <?= ($emprestimo['emp_status'] ?? '') === 'Empréstimo Atrasado' ? 'selected' : '' ?>>Empréstimo Atrasado</option>
                    <option value="Renovação Ativa" <?= ($emprestimo['emp_status'] ?? '') === 'Renovação Ativa' ? 'selected' : '' ?>>Renovação Ativa</option>
                    <option value="Renovação Atrasada" <?= ($emprestimo['emp_status'] ?? '') === 'Renovação Atrasada' ? 'selected' : '' ?>>Renovação Atrasada</option>
                    <option value="Finalizado" <?= ($emprestimo['emp_status'] ?? '') === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                </select>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='emprestimo-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>

</body>
</html>