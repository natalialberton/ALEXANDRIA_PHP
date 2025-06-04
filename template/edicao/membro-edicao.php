<!--

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "CADASTRO LIVRO";
$tituloH1= "CADASTRAMENTO LIVRO";
include '../header.php';

-->

<?php


require_once ('../../funcoes.php');

$planos = listar('plano');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<head>
    <link rel="stylesheet" href="../../static/css/signIn.css">
</head>
<main>
    <div class="container">
        <h1>EDIÇÃO MEMBRO</h1>
        
        <form action="../../editar-membro.php?id=<?=$id?>" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="mem_nome">Nome</label>
                    <input type="text" id="mem_nome" name="mem_nome" required>
                </div>
                <div class="form-group">
                    <label for="mem_cpf">CPF</label>
                    <input type="text" id="mem_cpf" name="mem_cpf" required
                           pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" 
                           title="000.000.000-00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mem_telefone">Telefone</label>
                    <input type="tel" id="mem_telefone" name="mem_telefone" required
                           pattern="\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}" 
                           title="(00) 00000-0000">
                </div>
                <div class="form-group">
                    <label for="mem_email">Email</label>
                    <input type="email" id="mem_email" name="mem_email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mem_senha">Senha</label>
                    <input type="password" id="mem_senha" name="mem_senha" required minlength="3">
                </div>
                <div class="form-group">
                    <label for="mem_dataInscricao">Data Inscrição</label>
                    <input type="date" id="mem_dataInscricao" name="mem_dataInscricao">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mem_status">Status</label>
                    <select name="mem_status" id="mem_status" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Suspenso">Suspenso</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fk_plan">Plano</label>
                    <select name="fk_plan" id="fk_plan" required>
                        <?php foreach ($planos as $plano): ?>
                            <option value="<?=htmlspecialchars($plano['pk_plan']); ?>">
                                <?= htmlspecialchars($plano['plan_nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <a href="../gestao/membro-gestao.php" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save">Salvar</button>
            </div>
        </form>
    </div>
</main>
</html>