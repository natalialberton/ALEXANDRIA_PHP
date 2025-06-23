<?php
require_once "../../geral.php";
session_start();

$tituloPagina = "HOME";
$tituloH1 = "HOME | " . $_SESSION['user_nome'];
include '../header.php';

$conexao = conectaBd();

$sql = "
    SELECT 
        u.user_cpf AS cpf,
        u.user_nome AS nome,
        u.user_email AS email,
        e.pk_emp AS id_emprestimo,
        l.liv_titulo AS livro,
        e.emp_dataEmp AS data_emprestimo,
        e.emp_dataDev AS data_devolucao,
        e.emp_status AS status
    FROM EMPRESTIMO e
    JOIN USUARIO u ON e.fk_user = u.pk_user
    JOIN LIVRO l ON e.fk_liv = l.pk_liv
    ORDER BY e.emp_dataEmp DESC
";

//u.user_cpf AS cpf,
//u.user_nome AS nome,
//u.user_email AS email,


try {
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

?>

<div class="main">

    <div class="status">
    </div>
    <div class="reservas">
        <div class="multas"></div>
    </div>
</div>

<div class="titulo">
    <h2>Empréstimos em Aberto</h2>
</div>

<div class='titleliv'>
    <div class="tabela">
        <div class="tisch tisch-overflow">
            <table>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>ID Emp</th>
                    <th>Livro</th>
                    <th>Data Emp</th>
                    <th>Data Dev</th>
                    <th>Status</th>
                </tr>
                <?php if (count($resultado) > 0): ?>
                    <?php foreach ($resultado as $linha): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($linha['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($linha['nome']); ?></td>
                            <td><?php echo htmlspecialchars($linha['email']); ?></td>
                            <td><?php echo htmlspecialchars($linha['id_emprestimo']); ?></td>
                            <td><?php echo htmlspecialchars($linha['livro']); ?></td>
                            <td><?php echo htmlspecialchars($linha['data_emprestimo']); ?></td>
                            <td><?php echo $linha['data_devolucao'] ? htmlspecialchars($linha['data_devolucao']) : 'Pendente'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($linha['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='8'>Nenhum empréstimo encontrado.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<div class="titulo">
    <h2>Reservas em Aberto</h2>
</div>

<div class='titleliv'>
    <div class="tabela">
        <div class="tisch tisch-overflow">
            <table>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>ID Emp</th>
                    <th>Livro</th>
                    <th>Data Emp</th>
                    <th>Data Dev</th>
                    <th>Status</th>
                </tr>
                <?php if (count($resultado) > 0): ?>
                    <?php foreach ($resultado as $linha): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($linha['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($linha['nome']); ?></td>
                            <td><?php echo htmlspecialchars($linha['email']); ?></td>
                            <td><?php echo htmlspecialchars($linha['id_emprestimo']); ?></td>
                            <td><?php echo htmlspecialchars($linha['livro']); ?></td>
                            <td><?php echo htmlspecialchars($linha['data_emprestimo']); ?></td>
                            <td><?php echo $linha['data_devolucao'] ? htmlspecialchars($linha['data_devolucao']) : 'Pendente'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($linha['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='8'>Nenhuma reserva encontrada.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>