<?php

require_once ('../../funcoes.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$membro = selecionarPorId('membro', $id, 'pk_mem');

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id']) ) {
    editarMembro($id);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIÇÃO</title>
    <link rel="stylesheet" href="../../static/css/signIn.css">
    <script src="../../static/javascript/geral.js"></script>
</head>

<main class="main-content">
<div class="container">
<h1>EDIÇÃO MEMBRO</h1>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <input type="hidden" name="acao" value="cadastrar_membro">
                <label for="mem_nome">Nome: </label>
                <input type="text" name="mem_nome" id="mem_nome" required
                       onkeypress="mascara(this,nomeMasc)"
                       value="<?=$membro['mem_nome']?>">
            </div>
            <div class="form-group">
                <label for="mem_cpf">CPF: </label>
                <input type="text" name="mem_cpf" id="mem_cpf" required 
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
                <input type="tel" name="mem_telefone" id="mem_telefone" required
                    pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                    title="(00) 00000-0000"
                    maxlength="15"
                    onkeypress="mascara(this,telefoneMasc)"
                    value="<?=$membro['mem_telefone']?>">
            </div>

            <div class="form-group">
                <label for="mem_email">Email: </label>
                <input type="email" name="mem_email" id="mem_email" value="<?=$membro['mem_email']?>" required>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="mem_dataInscricao">Data Inscrição: </label>
                <input type="date" name="mem_dataInscricao" id="mem_dataInscricao" 
                       value="<?=$membro['mem_dataInscricao']?>">
            </div>

            <div class="form-group">
                <label class="label-cadastro" for="mem_status">Status: </label>
                <select class="input-cadastro" name="mem_status" id="mem_status" required>
                    <option value="Ativo"> Ativo </option>
                    <option value="Suspenso"> Suspenso </option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="mem_senha">Senha: </label>
            <input type="password" name="mem_senha" id="mem_senha" required 
                   length="6" 
                   value="<?=$membro['mem_senha']?>">
        </div>

        <div class="form-row">
            <button class="btn btn-save" type="submit">Alterar</button>
            <a href="../gestao/membro-gestao.php" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
</main>
</body>
</html>