<?php

require_once "../../funcoes.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['form-id'])) {
        if($_POST['form-id'] === 'cadastrar_membro') {
            membro(1, '');
        } elseif ($_POST['form-id'] === 'excluir_membro') {
            membro(3, $_POST['id']);
        }
    }
}

if(!$termoBusca) {
    $membros = listar('membro');
}

$planos = listar('plano');

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
            <button class="action-btn" onclick="abrePopup('popupCadastroMembro')"><span class="plus-icon">+</span>NOVO MEMBRO</button>
        </div>
    </div>
    
    <div class="search-section">
            <div class="titulo">
                <h2>MEMBROS</h2>
            </div>
            <div class='barra'>
                <input type="text" class="search-input" id="busca" placeholder="Pesquisar">
            </div>
        </div>

    <div class='titleliv'>
        <div class="tabela" id="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Plano</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                    <?php foreach ($membros as $membro): 
                        $plano = selecionarPorId('plano', $membro['fk_plan'], 'pk_plan');
                    ?>
                        
                        <tr>
                            <td><?= htmlspecialchars($membro["mem_nome"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_cpf"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_telefone"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_email"]) ?></td>
                            <td><?= htmlspecialchars($plano["plan_nome"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_status"]) ?></td>
                            <td>
                                <!--<a href="../../crud/excluir-membro.php?id=<?=$membro['pk_mem']?>" >
                                    <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                </a>-->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="form-id" value="excluir_membro">
                                    <input type="hidden" name="id" value="<?= $membro['pk_mem'] ?>">
                                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;"
                                            onclick="return confirm('Tem certeza que deseja excluir este membro?')">
                                        <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                    </button>
                                </form>
                                <button onclick="location.href='?id=<?= $membro['pk_mem'] ?>#editarMembro'" 
                                        style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
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
                <label for="mem_dataInscricao">Data Inscrição: </label>
                <input type="date" name="mem_dataInscricao"
                    value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="plan_nome">Plano: </label>
                <input list="plan_nome" name="plan_nome">
                <datalist class="input-cadastro" name="plan_nome" required>
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?=htmlspecialchars($plano['plan_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-group">
            <label for="mem_senha">Senha: </label>
            <input type="password" name="mem_senha" required minlength="6" maxlength="6">
        </div>

        <div class="form-row">
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
                membro(2, $idMembro);
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
                <label for="mem_dataInscricao">Data Inscrição: </label>
                <input type="date" name="mem_dataInscricao"
                       value="<?=$membro['mem_dataInscricao']?>">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="mem_status">Status: </label>
                <select class="input-cadastro" name="mem_status" required>
                    <option value="Ativo" <?= ($membro['mem_status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Suspenso" <?= ($membro['mem_status'] ?? '') === 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label-cadastro" for="plan_nome">Plano: </label>
                <input list="plan_nome" name="plan_nome" 
                       value="<?=htmlspecialchars($planoOriginal['plan_nome']) ?? ''?>">
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

        <div class="form-row">
            <button class="btn btn-save" type="submit">Alterar</button>
            <button class="btn btn-cancel" onclick="location.href='membro-gestao.php'">Cancelar</button>
        </div>
    </form>
</div>
</div>
<?php endif; ?> 
</dialog>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../../static/javascript/geral.js"></script>
</body>
</html>