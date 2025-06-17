<?php
// Configuração do banco de dados ALEXANDRIA
$host = 'localhost:3307';
$dbname = 'ALEXANDRIA';
$username = 'root';
$password = '';

try {
    // Conexão com o banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query para buscar quantidade de livros por categoria
    $sql = "SELECT 
                c.cat_nome as categoria,
                COUNT(cl.fk_liv) as total_livros,
                SUM(l.liv_estoque) as total_estoque
            FROM CATEGORIA c
            LEFT JOIN CAT_LIV cl ON c.pk_cat = cl.fk_cat
            LEFT JOIN LIVRO l ON cl.fk_liv = l.pk_liv
            GROUP BY c.pk_cat, c.cat_nome
            ORDER BY total_livros DESC";
    //Essa consulta retorna, para cada categoria, a contagem de livros e a soma do estoque, listando-os do maior para o menor número de livros.


    $stmt = $pdo->query($sql);
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar dados para o gráfico
    $labels = [];
    $valores = [];
    $estoques = [];
    $cores = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF99CC', '#66FF66'];

    foreach ($dados as $row) {
        $labels[] = $row['categoria'];
        $valores[] = (int) $row['total_livros'];
        $estoques[] = (int) $row['total_estoque'];
    }

    // Buscar estatísticas gerais
    $stats_sql = "SELECT 
                    (SELECT COUNT(*) FROM LIVRO) as total_livros,
                    (SELECT SUM(liv_estoque) FROM LIVRO) as total_estoque,
                    (SELECT COUNT(*) FROM EMPRESTIMO WHERE emp_status IN ('Empréstimo Ativo', 'Renovação Ativa')) as emprestimos_ativos,
                    (SELECT COUNT(*) FROM MEMBRO WHERE fk_status = 1) as membros_ativos";

    $stats_stmt = $pdo->query($stats_sql);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Alexandria - Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <div class="header">
        <h1>📚 Sistema Alexandria</h1>
        <p>Dashboard - Análise do Acervo por Categoria</p>
    </div>

    <div class="container">
        <!-- Cards de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_livros'] ?></div>
                <div class="stat-label">Total de Títulos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_estoque'] ?></div>
                <div class="stat-label">Exemplares em Estoque</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['emprestimos_ativos'] ?></div>
                <div class="stat-label">Empréstimos Ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['membros_ativos'] ?></div>
                <div class="stat-label">Membros Ativos</div>
            </div>
        </div>

        <!-- Gráfico de Pizza -->
        <div class="chart-container">
            <h2 class="section-title">Distribuição de Livros por Categoria</h2>
            <div class="chart-wrapper">
                <canvas id="graficoPizza"></canvas>
            </div>
        </div>

        <!-- Tabela de Dados -->
        <div class="dados-tabela">
            <h3 class="section-title">Detalhamento por Categoria</h3>
            <table>
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Títulos</th>
                        <th>Estoque Total</th>
                        <th>Percentual</th>
                        <th>Média por Título</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_titulos = array_sum($valores);
                    foreach ($dados as $index => $row):
                        $percentual = $total_titulos > 0 ? ($row['total_livros'] / $total_titulos) * 100 : 0;
                        $media_estoque = $row['total_livros'] > 0 ? $row['total_estoque'] / $row['total_livros'] : 0;
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['categoria']) ?></strong></td>
                            <td><?= $row['total_livros'] ?></td>
                            <td><?= $row['total_estoque'] ?> exemplares</td>
                            <td class="percentage"><?= number_format($percentual, 1) ?>%</td>
                            <td><?= number_format($media_estoque, 1) ?> ex/título</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td><strong>TOTAL GERAL</strong></td>
                        <td><strong><?= $total_titulos ?></strong></td>
                        <td><strong><?= array_sum($estoques) ?> exemplares</strong></td>
                        <td class="percentage"><strong>100%</strong></td>
                        <td><strong><?= $total_titulos > 0 ? number_format(array_sum($estoques) / $total_titulos, 1) : '0' ?>
                                ex/título</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Dados do PHP para JavaScript
        const labels = <?= json_encode($labels) ?>;
        const dados = <?= json_encode($valores) ?>;
        const estoques = <?= json_encode($estoques) ?>;
        const cores = <?= json_encode(array_slice($cores, 0, count($labels))) ?>;

        // Configuração do gráfico
        const ctx = document.getElementById('graficoPizza').getContext('2d');
        const graficoPizza = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Títulos por Categoria',
                    data: dados,
                    backgroundColor: cores,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverBorderWidth: 5,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribuição do Acervo por Categoria Literária',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        color: '#333',
                        padding: 20
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            },
                            generateLabels: function (chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const dataset = data.datasets[0];
                                        const value = dataset.data[i];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);

                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: dataset.backgroundColor[i],
                                            strokeStyle: dataset.borderColor,
                                            lineWidth: dataset.borderWidth,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const estoque = estoques[context.dataIndex];
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);

                                return [
                                    `${label}`,
                                    `Títulos: ${value}`,
                                    `Estoque: ${estoque} exemplares`,
                                    `Percentual: ${percentage}%`
                                ];
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });

        // Animação de entrada para os cards
        window.addEventListener('load', function () {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 150);
            });
        });
    </script>
</body>

</html>

<?php
/*
SISTEMA ALEXANDRIA - DASHBOARD

Este dashboard mostra:
1. Estatísticas gerais da biblioteca
2. Gráfico de pizza com distribuição de livros por categoria
3. Tabela detalhada com análise do acervo

DADOS ANALISADOS:
- Total de títulos cadastrados
- Exemplares em estoque
- Empréstimos ativos
- Membros ativos
- Distribuição por categoria literária

CONFIGURAÇÃO:
1. Ajuste as credenciais do banco no início do arquivo
2. Certifique-se que o banco ALEXANDRIA está criado e populado
3. Execute o arquivo em servidor com PHP

POSSÍVEIS EXPANSÕES:
- Gráfico de empréstimos por mês
- Top 10 livros mais emprestados
- Análise de membros por plano
- Relatório de multas
- Dashboard de reservas
*/
?>