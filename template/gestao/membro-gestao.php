<?php

session_start();
require_once "../../geral.php";

permitirAcesso($_SESSION['statusUser'], $_SESSION['tipoUser'], 'Almoxarife', 'membro-gestao.php');

//DIRECIONANDO OS FORMULÁRIOS DE CADASTRO E EXCLUSÃO
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'cadastrar_membro') {
            crudMembro(1, '');
        } elseif ($_POST['form-id'] === 'excluir_membro') {
            crudMembro(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'membro';
$planos = listar('plano');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "MEMBROS";
$tituloH1= "GESTÃO MEMBROS";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>CADASTRAMENTO</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroMembro')"><span class="plus-icon">+</span>NOVO MEMBRO</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>MEMBROS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, nome ou CPF" oninput="pesquisarDadoTabela('membro')">
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
<dialog class="popup" id="popupCadastroMembro">
<div class="popup-content">
<div class="container">
<h1>CADASTRAMENTO MEMBRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_membro">
                <label for="mem_nome">Nome: </label>
                <input type="text" name="mem_nome" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="mem_cpf">CPF: </label>
                <input type="text" name="mem_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="mem_telefone">Telefone: </label>
                <input type="tel" name="mem_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)">
            </div>

            <div class="form-group">
                <label for="mem_email">Email: </label>
                <input type="email" name="mem_email" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="plan_nome">Plano: </label>
                <input list="plan_nome" name="plan_nome" required>
                <datalist class="input-cadastro" name="plan_nome" required>
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?=htmlspecialchars($plano['plan_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label for="mem_senha">Senha: </label>
                <input type="password" name="mem_senha" required minlength="6" maxlength="6">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <button class="btn btn-cancel" onclick="fechaPopup('popupCadastroMembro')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP EDIÇÃO-->
<dialog class="popup" id="popupEdicaoMembro">
<?php

    if (isset($_GET['id'])) {
        $idMembro = $_GET['id'];
        $membro = selecionarPorId('membro', $idMembro, 'pk_mem');
        $planoOriginal = selecionarPorId('plano', $membro['fk_plan'], 'pk_plan');
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_membro') {
                crudMembro(2, $idMembro);
            }
        }
    }

    if ($membro) :
?>
<div class="popup-content">
<div class="container">
<h1>EDIÇÃO MEMBRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="editar_membro">
                <input type="hidden" name="editar-id" value="<?= $idMembro ?? '' ?>">
                <label for="mem_nome">Nome: </label>
                <input type="text" name="mem_nome" required
                       onkeypress="mascara(this,nomeMasc)"
                       value="<?=$membro['mem_nome']?>">
            </div>
            <div class="form-group">
                <label for="mem_cpf">CPF: </label>
                <input type="text" name="mem_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)"
                    value="<?=$membro['mem_cpf']?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="mem_telefone">Telefone: </label>
                <input type="tel" name="mem_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)"
                    value="<?=$membro['mem_telefone']?>">
            </div>

            <div class="form-group">
                <label for="mem_email">Email: </label>
                <input type="email" name="mem_email" value="<?=$membro['mem_email']?>" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="mem_status">Status: </label>
                <select class="input-cadastro" name="mem_status" required>
                    <option value="Ativo" <?= ($membro['mem_status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Suspenso" <?= ($membro['mem_status'] ?? '') === 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mem_senha">Senha: </label>
                <input type="password" name="mem_senha" required minlength="6" maxlength="6">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="plan_nome">Plano: </label>
                <input list="plan_nome" name="plan_nome" required
                       value="<?=htmlspecialchars($planoOriginal['plan_nome']) ?? ''?>">
                <datalist class="input-cadastro" name="plan_nome">
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?=htmlspecialchars($plano['plan_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" type="button" onclick="location.href='membro-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?> 
</dialog>
</body>
</html>