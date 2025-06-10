<?php

require_once ('../../funcoes.php');

$planos = listar('plano');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    cadastrarMembro();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CADASTRO</title>
    <link rel="stylesheet" href="../../static/css/signIn.css">
    <script src="../../static/javascript/geral.js"></script>
</head>

<main class="main-content">
<div class="container">
<h1>CADASTRAMENTO MEMBRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="acao" value="cadastrar_membro">
                <label for="mem_nome">Nome: </label>
                <input type="text" name="mem_nome" id="mem_nome" onkeypress="mascara(this,nomeMasc)" required>
            </div>
            <div class="form-group">
                <label for="mem_cpf">CPF: </label>
                <input type="text" name="mem_cpf" id="mem_cpf" required 
                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                    title="000.000.000-00"
                    maxlength="14"
                    onkeypress="mascara(this,cpfMasc)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="mem_telefone">Telefone: </label>
                <input type="tel" name="mem_telefone" id="mem_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)">
            </div>

            <div class="form-group">
                <label for="mem_email">Email: </label>
                <input type="email" name="mem_email" id="mem_email" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="mem_dataInscricao">Data Inscrição: </label>
                <input type="date" name="mem_dataInscricao" id="mem_dataInscricao" 
                    value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="fk_plan">Plano: </label>
                <input list="fk_plan" name="fk_plan">
                <datalist class="input-cadastro" name="fk_plan" id="fk_plan" required>
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?=htmlspecialchars($plano['plan_nome']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>

        <div class="form-group">
            <label for="mem_senha">Senha: </label>
            <input type="password" name="mem_senha" id="mem_senha" required minlength="6" maxlength="6">
        </div>

        <div class="form-row">
            <button class="btn btn-save" type="submit">Cadastrar</button>
            <a href="../gestao/membro-gestao.php" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
</main>
</body>
</html>