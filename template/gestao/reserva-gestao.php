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
        if($_POST['form-id'] === 'cadastrar_reserva') {
            crudReserva(1, '');
        } elseif ($_POST['form-id'] === 'excluir_reserva') {
            crudReserva(3, $_POST['id']);
        }
    }
}

$_SESSION['tabela'] = 'reserva';
$livros = listar('livro');
$membros = listar('membro');
$usuarios = listar('usuario');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
$tituloPagina = "RESERVAS";
$tituloH1= "GESTÃO RESERVAS";
include '../header.php';

?>

<main class="main-content">
    <div class="top-section">
        <div class="actions-section">
            <h2>GERAL</h2>
            <button class="action-btn" onclick="abrePopup('popupCadastroReserva')"><span class="plus-icon">+</span>NOVA RESERVA</button>
        </div>
    </div>
    
    <div class="search-section">
        <h2>RESERVAS</h2>
        <div class='search-section__barra'>
            <i class='fi fi-rs-search'></i>
            <input type="text" class="search-input" id="pesquisaInput" size="26";
                   placeholder="Pesquisar ID, status" oninput="pesquisarDadoTabela('reserva')">
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
<dialog class="popup" id="popupCadastroReserva">
<div class="popup-content">
<div class="popup__container">
<h1>NOVA RESERVA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="form-id" value="cadastrar_reserva">
                <div class="form-group">
                <label class="label-cadastro" for="fk_mem">CPF Membro: </label>
                <input list="membros" name="fk_mem" required>
                <datalist class="input-cadastro" id="membros">
                    <?php foreach ($membros as $membro): ?>
                        <option value="<?=htmlspecialchars($membro['mem_cpf']); ?>">
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
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="res_prazo">Prazo: </label>
                <input type="number" name="res_prazo" required>
            </div>

            <div class="form-group">
                <label for="res_dataMarcada">Data: </label>
                <input type="date" name="res_dataMarcada" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="emp_status">Status: </label>
                <select class="input-cadastro" name="emp_status" required>
                    <option value="Aberta" selected>Aberta</option>
                    <option value="Cancelada">Cancelada</option>
                    <option value="Finalizada">Finalizada</option>
                    <option value="Atrasada">Atrasada</option>
                </select>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroReserva')">Cancelar</button>
        </div>
    </form>
</div>
</div>
</dialog>

<!--POPUP CADASTRAMENTO-->
<dialog class="popup" id="popupEdicaoReserva">
<?php

    if (isset($_GET['id'])) {
        $idRes = $_GET['id'];
        $reserva = selecionarPorId('reserva', $idRes, 'pk_res');
        $membroOriginal = selecionarPorId('membro', $reserva['fk_mem'], 'pk_mem');
        $livroOriginal = selecionarPorId('livro', $reserva['fk_liv'], 'pk_liv');
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['form-id'])) {
            if($_POST['form-id'] === 'editar_reserva') {
                crudReserva(2, $idRes);
            }
        }
    }

    if ($reserva) :
?>
<div class="popup-content">
<div class="popup__container">
<h1>EDIÇÃO RESERVA</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="editar-id" value="<?= $idRes ?? '' ?>">
                <input type="hidden" name="form-id" value="editar_reserva">
                
                <div class="form-group">
                <label class="label-cadastro" for="fk_mem">CPF Membro: </label>
                <input list="membros" name="fk_mem" required
                       value="<?=htmlspecialchars($membroOriginal['mem_cpf']) ?? ''?>">
                <datalist class="input-cadastro" id="membros">
                    <?php foreach ($membros as $membro): ?>
                        <option value="<?=htmlspecialchars($membro['mem_cpf']); ?>">
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
                        <option value="<?=htmlspecialchars($livro['liv_isbn']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="res_dataMarcada">Data: </label>
                <input type="date" name="res_dataMarcada" value="<?= htmlspecialchars($reserva['res_dataMarcada']); ?>" required>
            </div>

            <div class="form-group">
                <label for="res_dataFinalizada">Finalização: </label>
                <input type="date" name="res_dataMarcada" value="<?= htmlspecialchars($reserva['res_dataFinalizada']); ?>" required>
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="emp_status">Status: </label>
                <select class="input-cadastro" name="emp_status" required>
                    <option value="Aberta" <?= ($reserva['res_status'] ?? '') === 'Aberta' ? 'selected' : '' ?>>Aberta</option>
                    <option value="Cancelada" <?= ($reserva['res_status'] ?? '') === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    <option value="Finalizada" <?= ($reserva['res_status'] ?? '') === 'Finalizada' ? 'selected' : '' ?>>Finalizada</option>
                    <option value="Atrasada" <?= ($reserva['res_status'] ?? '') === 'Atrasada' ? 'selected' : '' ?>>Atrasada</option>
                </select>
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-save" type="submit">Registrar</button>
            <button class="btn btn-cancel" type="button" onclick="fechaPopup('popupCadastroReserva')">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>
</dialog>

</body>
</html>