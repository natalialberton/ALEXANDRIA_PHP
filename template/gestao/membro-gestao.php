<?php

require_once "../../funcoes.php";

$membros = listar('membro');

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
            <a href="../cadastro/membro-cadastro.php" class="action-btn" id="btn-cadastro-membro">
                <span class="plus-icon">+ NOVO MEMBRO</span>
            </a>     
        </div>
    </div>
    
    <div class = "titulo">
        <h2>Membros</h2>
    </div>

    <div class='titleliv'>
        <div class="tabela">
            <div class="tisch">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Ação</th>
                    </tr>
                    <?php foreach ($membros as $membro): ?>
                        <tr>
                            <td><?= htmlspecialchars($membro["mem_nome"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_cpf"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_telefone"]) ?></td>
                            <td><?= htmlspecialchars($membro["mem_email"]) ?></td>
                            <td>
                                <a href="../../excluir-membro.php?id=<?=$membro['pk_mem']?>" >
                                    <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                                </a>
                                <a href="../edicao/membro-edicao.php?id=<?=$membro['pk_mem']?>">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>


    <!--POPUP PARA CADASTRO DE MEMBRO-->
    <div class="popup-overlay" id="popup-cadastro-membro">
        <div class="cadastro-popup">
            <div class="container">
                <h1>CADASTRAMENTO MEMBRO</h1>
                <form action='../../cadastrar-membro.php' method="POST" enctype="multipart/form-data">
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
                            <select class="input-cadastro" name="fk_plan" id="fk_plan" required>
                                <?php foreach ($planos as $plano): ?>
                                    <option value="<?=htmlspecialchars($plano['pk_plan']); ?>">
                                        <?= htmlspecialchars($plano['plan_nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mem_senha">Senha: </label>
                        <input type="password" name="mem_senha" id="mem_senha" required minlength="6" maxlength="6">
                    </div>

                    <div class="form-row">
                        <button class="btn btn-save" type="submit">Cadastrar</button>
                        <button class="btn btn-cancel">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>