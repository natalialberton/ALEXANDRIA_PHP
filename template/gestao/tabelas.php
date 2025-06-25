<?php
require_once "../../geral.php";

// Definir a tabela (vinda de GET ou sessão)
$tabela = $_GET['tabela'] ?? $_SESSION['tabela'] ?? null;
$termoBusca = $_GET['termo'] ?? '';

// MENSAGEM PADRÃO PARA NOT FOUND
$mensagem = !empty($termoBusca)
    ? "Não foi possível encontrar $tabela para a pesquisa \"" . htmlspecialchars($termoBusca) . "\""
    : "Não foi possível encontrar $tabela";

// SWITCH DE ACORDO COM O VALOR DA TABELA
switch($tabela) {
    case 'membro':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_mem', 'mem_nome', 'mem_cpf')
            : listar('membro');
        break;
    case 'usuario':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_user', 'user_nome', 'user_cpf')
            : listar('usuario');
        break;
    case 'autor':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_aut', 'aut_nome', '')
            : listar('autor');
        break;
    case 'categoria':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_cat', 'cat_nome', '')
            : listar('categoria');
        break;
    case 'livro':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_liv', 'liv_titulo', 'liv_isbn')
            : listar('livro');
        break;
    case 'fornecedor':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_forn', 'forn_nome', 'forn_cnpj')
            : listar('fornecedor');
        break;
    case 'emprestimo':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_emp', 'emp_status', '')
            : listar('emprestimo');
        break;
    case 'reserva':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_res', 'res_status', '')
            : listar('reserva');
        break;
    case 'remessa':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_rem', 'rem_data', '')
            : listar('remessa');
        break;
    case 'multa':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_mul', 'mul_status', '')
            : listar('multa');
        break;
                            
    default:
        $dados = [];
        break;
}
?>

<?php if($tabela === 'membro'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
        if (!empty($dados)): 
            foreach ($dados as $dado): 
        ?>
            <tr>
                <td><?= htmlspecialchars($dado["pk_mem"]) ?></td>
                <td><?= htmlspecialchars($dado["mem_nome"]) ?></td>
                <td><?= htmlspecialchars($dado["mem_cpf"]) ?></td>
                <td><?= htmlspecialchars($dado["mem_telefone"]) ?></td>
                <td><?= htmlspecialchars($dado["mem_email"]) ?></td>
                <td><?= htmlspecialchars($dado["mem_status"]) ?></td>
                <td>
                    <button style="background: none; border: none; padding: 0; cursor: pointer;"
                            onclick="confirmarExclusao('membro-gestao.php', 'excluir_membro', <?= $dado['pk_mem'] ?>, 'Tem certeza que deseja excluir este membro?')">
                        <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                    </button>
                    <button onclick="location.href='?id=<?= $dado['pk_mem'] ?>#editarMembro'" 
                            style="background: none; border: none; padding: 0; cursor: pointer;">
                        <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                    </button>
                    <a href="../../historicoPdf.php?id_mem=<?= $dado['pk_mem'] ?>" target="_blank" 
                       style="text-decoration: none; cursor: pointer;">
                        <i class="fas fa-file-pdf" title="Gerar Relatório em PDF" style="font-size: 20px; color: #a69c60;"></i>
                    </a>
                </td>
            </tr>
        <?php 
            endforeach;
        else: 
            echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i> $mensagem</td></tr>";
        endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'fornecedor'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CNPJ</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Endereço</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado): 
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_forn"]) ?></td>
                        <td><?= htmlspecialchars($dado["forn_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["forn_cnpj"]) ?></td>
                        <td><?= htmlspecialchars($dado["forn_telefone"]) ?></td>
                        <td><?= htmlspecialchars($dado["forn_email"]) ?></td>
                        <td><?= htmlspecialchars($dado["forn_endereco"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('fornecedor-gestao.php', 'excluir_fornecedor', <?= $dado['pk_forn'] ?>, 'Tem certeza que deseja excluir este fornecedor?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_forn'] ?>#editarFornecedor'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'autor'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Data Nascimento</th>
            <th>Genêro Literário</th>
            <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
            <th>Ação</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado): 
                    $categoria = selecionarPorId('categoria', $dado['fk_cat'], 'pk_cat');
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_aut"]) ?></td>
                        <td><?= htmlspecialchars($dado["aut_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["aut_dataNascimento"]) ?></td>
                        <td><?= htmlspecialchars($categoria["cat_nome"]) ?></td>
                        <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('autor-gestao.php', 'excluir_autor', <?= $dado['pk_aut'] ?>, 'Tem certeza que deseja excluir este autor?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_aut'] ?>#editarAutor'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'categoria'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
            <th>Ação</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado):
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_cat"]) ?></td>
                        <td><?= htmlspecialchars($dado["cat_nome"]) ?></td>
                        <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('categoria-gestao.php', 'excluir_categoria', <?= $dado['pk_cat'] ?>, 'Tem certeza que deseja excluir esta categoria?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_cat'] ?>#editarCategoria'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'livro'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>ISBN</th>
            <th>Autor</th>
            <th>Categoria</th>
            <th>Edição</th>
            <th>Estoque</th>
            <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
            <th>Ação</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado):
                    $autor = selecionarPorId('autor', $dado['fk_aut'], 'pk_aut');
                    $categoria = selecionarPorId('categoria', $dado['fk_cat'], 'pk_cat');
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_liv"]) ?></td>
                        <td><?= htmlspecialchars($dado["liv_titulo"]) ?></td>
                        <td><?= htmlspecialchars($dado["liv_isbn"]) ?></td>
                        <td><?= htmlspecialchars($autor["aut_nome"]) ?></td>
                        <td><?= htmlspecialchars($categoria["cat_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["liv_edicao"]) ?></td>
                        <td><?= $dado["liv_estoque"] ?? 0 ?></td>
                        <?php if($_SESSION['tipoUser'] !== 'Almoxarife'): ?>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('livro-gestao.php', 'excluir_livro', <?= $dado['pk_liv'] ?>, 'Tem certeza que deseja excluir este livro?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_liv'] ?>#editarLivro'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'usuario'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Cargo</th>
            <th>Admissão</th>
            <th>Demissão</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado): 
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_user"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_cpf"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_telefone"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_email"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_tipoUser"]) ?></td>
                        <td><?= $dado["user_dataAdmissao"] ?? ''?></td>
                        <td><?= $dado["user_dataDemissao"] ?? '' ?></td>
                        <td><?= htmlspecialchars($dado["user_status"]) ?></td>
                        <td>
                            <button onclick="location.href='?id=<?= $dado['pk_user'] ?>#editarUsuario'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'emprestimo'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Livro</th>
            <th>Membro</th>
            <th>Data Marcada</th>
            <th>Devolução</th>
            <th>Devolução Real</th>
            <th>Funcionário</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado):
                    $livro = selecionarPorId('livro', $dado['fk_liv'], 'pk_liv');
                    $membro = selecionarPorId('membro', $dado['fk_mem'], 'pk_mem');   
                    $usuario = selecionarPorId('usuario', $dado['fk_user'], 'pk_user');    
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_emp"]) ?></td>
                        <td><?= htmlspecialchars($livro["liv_isbn"]) ?></td>
                        <td><?= htmlspecialchars($membro["mem_cpf"]) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($dado["emp_dataEmp"]))) ?? null?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($dado["emp_dataDev"]))) ?? null?></td>
                        <td><?= !empty($dado["emp_dataDevReal"]) ? (date('d/m/Y', strtotime($dado["emp_dataDevReal"]))) : null?></td>
                        <td><?= htmlspecialchars($usuario["user_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["emp_status"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('emprestimo-gestao.php', 'excluir_emprestimo', <?= $dado['pk_emp'] ?>, 'Tem certeza que deseja excluir este registro de empréstimo? Esta ação afetará o histórico de empréstimos do membro!')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_emp'] ?>#editarEmprestimo'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'reserva'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Livro</th>
            <th>Membro</th>
            <th>Data Marcada</th>
            <th>Vencimento</th>
            <th>Finalizada</th>
            <th>Funcionário</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado):
                    $livro = selecionarPorId('livro', $dado['fk_liv'], 'pk_liv');
                    $membro = selecionarPorId('membro', $dado['fk_mem'], 'pk_mem');   
                    $usuario = selecionarPorId('usuario', $dado['fk_user'], 'pk_user');    
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_res"]) ?></td>
                        <td><?= htmlspecialchars($livro["liv_isbn"]) ?></td>
                        <td><?= htmlspecialchars($membro["mem_cpf"]) ?></td>
                        <td><?= $dado["res_dataMarcada"] ?? ''?></td>
                        <td><?= $dado["res_dataVencimento"] ?? ''?></td>
                        <td><?= $dado["res_dataFinalizada"] ?? ''?></td>
                        <td><?= htmlspecialchars($usuario["user_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["res_status"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('reserva-gestao.php', 'excluir_reserva', <?= $dado['pk_res'] ?>, 'Tem certeza que deseja excluir este registro de reserva?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_res'] ?>#editarReserva'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'remessa'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Livro</th>
            <th>Fornecedor</th>
            <th>Data</th>
            <th>Quantidade</th>
            <th>Funcionário</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado):
                    $livro = selecionarPorId('livro', $dado['fk_liv'], 'pk_liv');
                    $fornecedor = selecionarPorId('fornecedor', $dado['fk_forn'], 'pk_forn');   
                    $usuario = selecionarPorId('usuario', $dado['fk_user'], 'pk_user');    
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_rem"]) ?></td>
                        <td><?= htmlspecialchars($livro["liv_isbn"]) ?></td>
                        <td><?= htmlspecialchars($fornecedor["forn_cnpj"]) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($dado["rem_data"]))) ?? null?></td>
                        <td><?= htmlspecialchars($dado["rem_qtd"])?></td>
                        <td><?= htmlspecialchars($usuario["user_nome"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('remessa-gestao.php', 'excluir_remessa', <?= $dado['pk_rem'] ?>, 'Tem certeza que deseja excluir este registro de remessa?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_rem'] ?>#editarRemessa'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php elseif($tabela === 'multa'): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Membro Nome</th>
            <th>Membro CPF</th>
            <th>ID Empréstimo</th>
            <th>Dias Atraso</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado):
                    $membro = selecionarPorId('membro', $dado['fk_mem'], 'pk_mem');  
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_mul"]) ?></td>
                        <td><?= htmlspecialchars($membro["mem_nome"]) ?></td>
                        <td><?= htmlspecialchars($membro["mem_cpf"]) ?></td>
                        <td><?= htmlspecialchars($dado["fk_emp"]) ?></td>
                        <td><?= htmlspecialchars($dado["mul_qtdDias"]) ?></td>
                        <td><?= htmlspecialchars($dado["mul_valor"])?></td>
                        <td><?= htmlspecialchars($dado["mul_status"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('multa-gestao.php', 'excluir_multa', <?= $dado['pk_mul'] ?>, 'Tem certeza que deseja excluir este registro de multa?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_mul'] ?>#editarMulta'" 
                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60;"></i>
                            </button>
                        </td>
                    </tr>
        <?php 
                endforeach;
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i>$mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php endif; ?>
