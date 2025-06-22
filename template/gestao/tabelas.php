<?php

require_once "../../funcoes.php";

$termoBusca = $_GET['termo'] ?? '';
$tabela = $_GET['tabela'] ?? $GLOBALS['tabela'] ?? '';

$mensagem = !empty($termoBusca) ?
    "Não foi possível encontrar $tabela para a pesquisa \"" . htmlspecialchars($termoBusca) . "\"" :
    "Não foi possível encontrar $tabela";

switch($tabela) {
    case 'membro':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_mem', 'mem_nome', 'mem_cpf') : listar('membro');
        break;
    case 'funcionario':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_user', 'user_nome', '') : listar('funcionario');
        break;
    default:
        $dados = [];
}


?>
<?php if($tabela === 'membro'): ?>
<table id="tabela-membros">
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
            else: echo "<tr><td colspan='8' class='text-center'><i class='fas fa-search'></i> $mensagem</td></tr>";
            endif;
        ?>
    </tbody>
</table>

<?php 
    endif; 
?>