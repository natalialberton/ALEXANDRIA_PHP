<?php

session_start();
require_once "../../geral.php";

if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('../index.php', 'erroAlerta', 'Acesso a página negado!');
}

$tituloPagina = "HOME";
$tituloH1 = "HOME | " . $_SESSION['user_nome'];
include '../header.php';

$conexao = conectaBd();

$sql = 
    "SELECT 
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
    WHERE e.emp_status != 'Finalizado'
    ORDER BY e.emp_status DESC
";

$sqlReserva =
    "SELECT 
    r.pk_res AS id_reserva,
    m.mem_cpf AS cpf_membro,
    m.mem_nome AS nome_membro,
    m.mem_email AS email_membro,
    l.liv_titulo AS titulo_livro,
    u.user_nome AS nome_usuario,
    r.res_dataMarcada AS data_marcada,
    r.res_dataVencimento AS data_vencimento,
    r.res_dataFinalizada AS data_finalizada,
    r.res_status AS status

FROM RESERVA r
JOIN MEMBRO m ON r.fk_mem = m.pk_mem
JOIN LIVRO l ON r.fk_liv = l.pk_liv
JOIN USUARIO u ON r.fk_user = u.pk_user
WHERE r.res_status = 'Aberta' OR r.res_status = 'Atrasada'
ORDER BY r.res_status DESC";

try {
    $stmtReserva = $conexao->prepare($sqlReserva);
    $stmtReserva->execute();
    $resultadoReservas = $stmtReserva->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na consulta de reservas: " . $e->getMessage());
}

$sqlMultasDetalhadas = "
    SELECT 
        m.pk_mul AS id_multa,
        mb.mem_cpf AS cpf_membro,
        mb.mem_nome AS nome_membro,
        e.pk_emp AS id_emprestimo,
        e.emp_dataEmp AS data_emprestimo,
        e.emp_dataDev AS data_devolucao,
        m.mul_qtdDias AS dias_em_atraso,
        m.mul_valor AS valor_multa,
        m.mul_status AS status
    FROM MULTA m
    JOIN MEMBRO mb ON m.fk_mem = mb.pk_mem
    JOIN EMPRESTIMO e ON m.fk_emp = e.pk_emp
    WHERE m.mul_status = 'Aberta'
    ORDER BY m.pk_mul DESC
";
try {
    $stmtMultas = $conexao->prepare($sqlMultasDetalhadas);
    $stmtMultas->execute();
    $resultadoMultas = $stmtMultas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na consulta de multas: " . $e->getMessage());
}


$sqlEmp = "
    SELECT 
        SUM(emp_status = 'Empréstimo Ativo') AS em_aberto,
        SUM(emp_status = 'Empréstimo Atrasado') AS em_atrasado
    FROM EMPRESTIMO
";
$resEmp = $conexao->query($sqlEmp)->fetch(PDO::FETCH_ASSOC);

// Reservas
$sqlRes = "
    SELECT 
        SUM(res_status = 'Aberta') AS res_aberto,
        SUM(res_status = 'Atrasada') AS res_atrasada
    FROM RESERVA
";
$resRes = $conexao->query($sqlRes)->fetch(PDO::FETCH_ASSOC);

// Multas
$sqlMul = "
    SELECT 
        COUNT(*) AS qtd_multas,
        SUM(mul_valor) AS total_multas
    FROM MULTA
    WHERE mul_status = 'Aberta'
";
$resMul = $conexao->query($sqlMul)->fetch(PDO::FETCH_ASSOC);


try {
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="../../static/javascript/semanal.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;

        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 100px;
        }

        .card {
            background: rgb(81, 61, 70);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-label {
            font-size: 1.1em;
            color:white;
            font-weight: 500;
        }

        .card.emprestimos .card-number {
            color: #fff;
        }

        .card.reservas .card-number {
            color: #fff;
        }

        .card.ativos .card-number {
            color: #fff;
        }

        .card.abertas .card-number {
            color: #fff;
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: rgb(81, 61, 70);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(10px);
        }

        .chart-title {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #FFEE87CC;
        }

        .chart-container {
            position: relative;
            height: 200px;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: white;
            font-size: 1.2em;
        }

        .error {
            background: rgba(231, 76, 60, 0.9);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .refresh-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            margin: 20px auto;
            display: block;
            transition: background 0.3s ease;
        }

        .refresh-btn:hover {
            background: #2980b9;
        }


        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }

            .cards-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div id="loading" class="loading">
            <p> Carregando dados </p>
        </div>

        <div id="error" class="error" style="display: none;"></div>

        <div id="dashboard" style="display: none;">
            <div class="cards-container">
                <div class="card emprestimos">
                    <div class="card-number" id="totalEmprestimos">0</div>
                    <div class="card-label">Total de Empréstimos</div>
                </div>
                <div class="card reservas">
                    <div class="card-number" id="totalReservas">0</div>
                    <div class="card-label">Total de Reservas</div>
                </div>
                <div class="card ativos">
                    <div class="card-number" id="emprestimosAtivos">0</div>
                    <div class="card-label">Empréstimos Ativos</div>
                </div>
                <div class="card abertas">
                    <div class="card-number" id="reservasAbertas">0</div>
                    <div class="card-label">Reservas Abertas</div>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <div class="chart-title">Status dos Empréstimos</div>
                    <div class="chart-container">
                        <canvas id="emprestimosChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-title">Status das Reservas</div>
                    <div class="chart-container">
                        <canvas id="reservasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </div>

</body>

</html>


<div class="tituloHome">
    <h2>Empréstimos em Aberto:</h2>
</div>
<div class="grupo">
    <div class="blocos-horizontal">
        <div class="bloco-container">
            <div class="titulo-bloco">Em Aberto</div>
            <div class="bloco amarelo"><?= $resEmp['em_aberto'] ?></div>
        </div>
        <div class="bloco-container">
            <div class="titulo-bloco">Atrasada</div>
            <div class="bloco vermelho"><?= $resEmp['em_atrasado'] ?></div>
        </div>
    </div>
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
                            <td><?php echo $linha['data_devolucao'] ? htmlspecialchars($linha['data_devolucao']) : ''; ?>
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

<div class="tituloHome">
    <h2>Reservas em Aberto:</h2>
</div>
<div class="grupo">
    <div class="blocos-horizontal">
        <div class="bloco-container">
            <div class="titulo-bloco">Em Aberto</div>
            <div class="bloco amarelo"><?= $resRes['res_aberto'] ?></div>
        </div>
        <div class="bloco-container">
            <div class="titulo-bloco">Atrasada</div>
            <div class="bloco vermelho"><?= $resRes['res_atrasada'] ?></div>
        </div>
    </div>
</div>


<div class='titleliv'>
    <div class="tabela">
        <div class="tisch tisch-overflow">
            <table>
                <tr>
                    <th>ID</th>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Livro</th>
                    <th>Data Marc</th>
                    <th>Data Venc</th>
                    <th>Data Final</th>
                    <th>Status</th>
                </tr>
                <?php if (count($resultadoReservas) > 0): ?>
                    <?php foreach ($resultadoReservas as $linha): ?>
                        <tr>
                            <td><?= htmlspecialchars($linha['id_reserva'] ?? '') ?></td>
                            <td><?= htmlspecialchars($linha['cpf_membro'] ?? '') ?></td>
                            <td><?= htmlspecialchars($linha['nome_membro'] ?? '') ?></td>
                            <td><?= htmlspecialchars($linha['email_membro'] ?? '') ?></td>
                            <td><?= htmlspecialchars($linha['titulo_livro'] ?? '') ?></td>
                            <td><?= htmlspecialchars($linha['data_marcada'] ?? '') ?></td>
                            <td><?= $linha['data_vencimento'] ? htmlspecialchars($linha['data_vencimento']) : 'Não informada'; ?>
                            </td>
                            <td><?= $linha['data_finalizada'] ? htmlspecialchars($linha['data_finalizada']) : ''; ?></td>
                            <td><?= htmlspecialchars($linha['status'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">Nenhuma reserva encontrada.</td>
                    </tr>
                <?php endif; ?>

            </table>
        </div>
    </div>
</div>

<div class="tituloHome">
    <h2>Multas em Aberto:</h2>
</div>

<div class="grupo">
    <div class="botoes-container blocos-horizontal">
        <div class="bloco-container">
            <div class="titulo-bloco">Preço da Multa</div>
            <div class="bloco azul-escuro">R$<?= number_format($resMul['total_multas'] ?? 0, 2, ',', '.') ?></div>
        </div>
        <div class="bloco-container">
            <div class="titulo-bloco"> Devendo:</div>
            <div class="bloco vermelho"><?= $resMul['qtd_multas'] ?? 0 ?></div>
        </div>
    </div>
</div>




<div class="titleliv">
    <div class="tabela">
        <div class="tisch tisch-overflow">
            <table>
                <tr>
                    <th>ID Multa</th>
                    <th>CPF</th>
                    <th>Nome do Membro</th>
                    <th>ID Empréstimo</th>
                    <th>Data Empréstimo</th>
                    <th>Data Devolução</th>
                    <th>Dias em Atraso</th>
                    <th>Valor (R$)</th>
                    <th>Status</th>
                </tr>
                <?php if (count($resultadoMultas) > 0): ?>
                    <?php foreach ($resultadoMultas as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['id_multa'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['cpf_membro'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['nome_membro'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['id_emprestimo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['data_emprestimo'] ?? '') ?></td>
                            <td><?= $m['data_devolucao'] ? htmlspecialchars($m['data_devolucao']) : 'Pendente'; ?></td>
                            <td><?= htmlspecialchars($m['dias_em_atraso'] ?? '0') ?></td>
                            <td>R$ <?= number_format($m['valor_multa'] ?? 0, 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($m['status'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">Nenhuma multa em aberto encontrada.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
