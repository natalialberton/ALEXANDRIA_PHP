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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .geral {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .blocos {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .blocos:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .numero {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .titulo {
            font-size: 1.1rem;
            color: #666;
            font-weight: 500;
        }

        .charts-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .chart-title {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #555;
            font-size: 1.3rem;
            font-weight: 500;
        }

        .dados-tabela {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .total-row {
            background: #667eea !important;
            color: white;
            font-weight: bold;
        }

        .total-row:hover {
            background: #5a6fd8 !important;
        }

        .percentage {
            font-weight: bold;
            color: #667eea;
        }

        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .chart-wrapper {
                height: 300px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Alexandria</h1>
    </div>

    <div class="container">
        <!-- Cards de Estatísticas -->
        <div class="geral">
            <div class="blocos">
                <div class="numero"><?= $stats['total_livros'] ?></div>
                <div class="titulo">Total de Títulos</div>
            </div>
            <div class="blocos">
                <div class="numero"><?= $stats['total_estoque'] ?></div>
                <div class="titulo">Exemplares em Estoque</div>
            </div>
            <div class="blocos">
                <div class="numero"><?= $stats['emprestimos_ativos'] ?></div>
                <div class="titulo">Empréstimos Ativos</div>
            </div>
            <div class="blocos">
                <div class="numero"><?= $stats['membros_ativos'] ?></div>
                <div class="titulo">Membros Ativos</div>
            </div>
        </div>
    </div>

    <!-- Seção dos Gráficos -->
    <div class="charts-section">
        <h2 class="section-title">Análise do Acervo por Categoria</h2>
        <div class="charts-container">
            <!-- Gráfico de Pizza -->
            <div>
                <h3 class="chart-title">Distribuição Percentual</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoPizza"></canvas>
                </div>
            </div>
            
            <!-- Gráfico de Barras -->
            <div>
                <h3 class="chart-title">Comparação de Títulos</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoBarras"></canvas>
                </div>
            </div>
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

    <script>
        // Dados do PHP para JavaScript
        const labels = <?= json_encode($labels) ?>;
        const dados = <?= json_encode($valores) ?>;
        const estoques = <?= json_encode($estoques) ?>;
        const cores = <?= json_encode(array_slice($cores, 0, count($labels))) ?>;

        // Configuração do gráfico de pizza
        const ctxPizza = document.getElementById('graficoPizza').getContext('2d');
        const graficoPizza = new Chart(ctxPizza, {
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
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11
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

        // Configuração do gráfico de barras
        const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
        const graficoBarras = new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Número de Títulos',
                    data: dados,
                    backgroundColor: cores.map(cor => cor + '80'), // Adiciona transparência
                    borderColor: cores,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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
                                const value = context.parsed.y;
                                const estoque = estoques[context.dataIndex];
                                const total = dados.reduce((a, b) => a + b, 0);
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
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });

        // Animação de entrada para os cards
        window.addEventListener('load', function () {
            const cards = document.querySelectorAll('.blocos');
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
SISTEMA ALEXANDRIA - DASHBOARD ATUALIZADO

Este dashboard mostra:
1. Estatísticas gerais da biblioteca
2. Gráfico de pizza com distribuição percentual por categoria
3. Gráfico de barras para comparação visual dos números
4. Tabela detalhada com análise do acervo

NOVIDADES DESTA VERSÃO:
- Gráfico de barras lado a lado com o de pizza
- Layout responsivo com grid
- Melhor organização visual dos gráficos
- Tooltips consistentes em ambos os gráficos
- Design aprimorado e moderno

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