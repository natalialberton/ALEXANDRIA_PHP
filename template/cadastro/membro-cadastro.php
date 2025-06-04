<?php

require_once ('../../funcoes.php');

$planos = listar('plano');

?>
<head>
    <link rel="stylesheet" href="../../static/css/signIn.css">
</head>

<main class="main-content">
<div class="container">
<h1>CADASTRAMENTO MEMBRO</h1>
    <form action='../../cadastrar-membro.php' method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <input type="hidden" name="acao" value="cadastrar_membro">
            <label for="mem_nome">Nome: </label>
            <input type="text" name="mem_nome" id="mem_nome" required>
        </div>
        <div class="form-group">
            <label for="mem_cpf">CPF: </label>
            <input type="text" name="mem_cpf" id="mem_cpf" required 
                   pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                   title="000.000.000-00">
        </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="mem_telefone">Telefone: </label>
        <input type="tel" name="mem_telefone" id="mem_telefone" required
               pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
               title="(00) 00000-0000">
         </div>
     </div>

    <div class="form-row">
        <div class="form-group">
        <label for="mem_email">Email: </label>
        <input type="email" name="mem_email" id="mem_email" required>
        </div>
     </div>


        <label for="mem_dataInscricao">Data Inscrição: </label>
        <input type="date" name="mem_dataInscricao" id="mem_dataInscricao" 
               value="<?php echo date('Y-m-d'); ?>">

        <label for="mem_senha">Senha: </label>
        <input type="password" name="mem_senha" id="mem_senha" required minlength="3">

        <label for="mem_status">Status: </label>
        <select name="mem_status" id="mem_status" required>
            <option value="Ativo">Ativo</option>
            <option value="Suspenso">Suspenso</option>
        </select>

        <label for="fk_plan">Plano: </label>
        <select name="fk_plan" id="fk_plan" required>
            <?php foreach ($planos as $plano): ?>
                <option value="<?=htmlspecialchars($plano['pk_plan']); ?>">
                    <?= htmlspecialchars($plano['plan_nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="button-cadastrar" type="submit">Cadastrar</button>
        <a href="../gestao/membro-gestao.php" class="button-cadastrar">Cancelar</a>
    </form>
</div>
</main>
</body>
</html>