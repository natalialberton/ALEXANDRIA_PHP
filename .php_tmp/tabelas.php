<?php

require_once "../../geral.php";

$termoBusca = $_GET['termo'] ?? '';
$tabela = $_GET['tabela'] ?? $_SESSION['tabela'] ?? '';

$mensagem = !empty($termoBusca) ?
    "Não foi possível encontrar $tabela para a pesquisa \"" . htmlspecialchars($termoBusca) . "\"" :
    "Não foi possível encontrar $tabela";

switch($tabela) {
    case 'membro':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_mem', 'mem_nome', 'mem_cpf') : listar('membro');
        break;
    case 'fornecedor':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_forn', 'forn_nome', 'forn_cnpj') : listar('fornecedor');
        break;
    case 'autor':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_aut', 'aut_nome', '') : listar('autor');
        break;
    case 'categoria':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_cat', 'cat_nome', '') : listar('categoria');
        break;
    case 'livro':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_liv', 'liv_titulo', 'liv_isbn') : listar('livro');
         break;
    case 'usuario':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_user', 'user_nome', 'user_cpf') : listar('usuario');
        break;
    default:
        $dados = [];
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
            <th>Plano</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody id="tabela-body">
        <?php 
            if(!empty($dados)): 
                foreach ($dados as $dado): 
                    $plano = selecionarPorId('plano', $dado['fk_plan'], 'pk_plan');
        ?>
                    <tr>
                        <td><?= htmlspecialchars($dado["pk_mem"]) ?></td>
                        <td><?= htmlspecialchars($dado["mem_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["mem_cpf"]) ?></td>
                        <td><?= htmlspecialchars($dado["mem_telefone"]) ?></td>
                        <td><?= htmlspecialchars($dado["mem_email"]) ?></td>
                        <td><?= htmlspecialchars($plano["plan_nome"]) ?></td>
                        <td><?= htmlspecialchars($dado["mem_status"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('membro-gestao.php', 'excluir_membro', <?= $dado['pk_mem'] ?>, 'Tem certeza que deseja excluir este membro?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
                            <button onclick="location.href='?id=<?= $dado['pk_mem'] ?>#editarMembro'" 
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
            <th>Ação</th>
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
            <th>Ação</th>
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
            <th>Ação</th>
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
                        <td><?= htmlspecialchars($dado["liv_estoque"]) ?></td>
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
            <th>Login</th>
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
                        <td><?= htmlspecialchars($dado["user_login"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_cpf"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_telefone"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_email"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_tipoUser"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_dataAdmissao"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_dataDemissao"]) ?></td>
                        <td><?= htmlspecialchars($dado["user_status"]) ?></td>
                        <td>
                            <button style="background: none; border: none; padding: 0; cursor: pointer;"
                                    onclick="confirmarExclusao('funcionario-gestao.php', 'excluir_usuario', <?= $dado['pk_user'] ?>, 'Tem certeza que deseja excluir este funcionário?')">
                                <i class='fas fa-trash-alt' style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                            </button>
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
<?php endif; ?>