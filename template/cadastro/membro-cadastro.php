<?php

require_once ('../../funcoes.php');

$planos = listar('plano');

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "Cadastro Membro";
$tituloH1= "CADASTRAMENTO MEMBRO";
include '../header.php';

?>
<head>
    <link rel="stylesheet" href="../../static/css/cadastro.css">
</head>

<main class="main-content">
    <form action="../../funcoes.php" method="POST">
        <input type="hidden" name="acao" value="cadastrar_membro">
        <label class="label-cadastro" for="mem_nome">Nome: </label>
        <input class="input-cadastro" type="text" name="mem_nome" id="mem_nome" required>

        <label class="label-cadastro" for="mem_cpf">CPF: </label>
        <input class="input-cadastro" type="text" name="mem_cpf" id="mem_cpf" required 
               pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
               title="Formato: 000.000.000-00">

        <label class="label-cadastro" for="mem_telefone">Telefone: </label>
        <input class="input-cadastro" type="tel" name="mem_telefone" id="mem_telefone" required
               pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
               title="Formato: (00) 00000-0000">

        <label class="label-cadastro" for="mem_email">Email: </label>
        <input class="input-cadastro" type="email" name="mem_email" id="mem_email" required>

        <label class="label-cadastro" for="mem_dataInscricao">Data Inscrição: </label>
        <input class="input-cadastro" type="date" name="mem_dataInscricao" id="mem_dataInscricao" 
               value="<?php echo date('Y-m-d'); ?>">

        <label class="label-cadastro" for="mem_senha">Senha: </label>
        <input class="input-cadastro" type="password" name="mem_senha" id="mem_senha" required minlength="6">

        <label class="label-cadastro" for="mem_status">Status: </label>
        <select class="input-cadastro" name="mem_status" id="mem_status" required>
            <option value="Ativo">Ativo</option>
            <option value="Suspenso">Suspenso</option>
        </select>

        <label class="label-cadastro" for="fk_plan">Plano: </label>
        <select class="input-cadastro" name="fk_plan" id="fk_plan" required>
            <?php foreach ($planos as $plano): ?>
                <option value="<?=htmlspecialchars($plano['pk_plan']); ?>">
                    <?= htmlspecialchars($plano['plan_nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="button-cadastrar" type="submit">Cadastrar</button>
        <a href="membro-gestao.php" class="button-cadastrar">Cancelar</a>
    </form>

</main>
</body>
</html>