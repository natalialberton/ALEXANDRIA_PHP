<?php
require_once "../../geral.php";

// Definir a tabela (vinda de GET ou sessão)
$tabela = $_GET['tabela'] ?? $_SESSION['tabela'] ?? '';
$termoBusca = $_GET['termo'] ?? '';

// Mensagem padrão caso não haja resultados
$mensagem = !empty($termoBusca)
    ? "Não foi possível encontrar $tabela para a pesquisa \"" . htmlspecialchars($termoBusca) . "\""
    : "Não foi possível encontrar $tabela";

// Obter os dados da tabela correspondente
switch($tabela) {
    case 'membro':
        $dados = !empty($termoBusca)
            ? retornoPesquisa($termoBusca, $tabela, 'pk_mem', 'mem_nome', 'mem_cpf')
            : listar('membro');
        break;
    case 'emprestimo':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_emp', 'emp_status', '') : listar('emprestimo');
        break;
    case 'reserva':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_res', 'res_status', '') : listar('reserva');
        break;
    case 'remessa':
        $dados = !empty($termoBusca) ? retornoPesquisa($termoBusca, $tabela, 'pk_rem', '', '') : listar('remessa');
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
                        <i class="fas fa-pencil-alt" style="font-size: 20px; color: #a69c60; margin-right: 7px;"></i>
                    </button>
                    <!-- Link corrigido para geraPdf.php na mesma pasta 'gestao' -->
                    <a href="geraPdf.php?id_mem=<?= $dado['pk_mem'] ?>" target="_blank" 
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
<?php endif; ?>
