<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Biblioteca</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.15);
        }

        .chart-title {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .loading {
            text-align: center;
            color: white;
            font-size: 1.2rem;
            padding: 50px;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Dashboard Biblioteca</h1>
            <p>Sistema de Gestão e Análise de Dados</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalLivros">-</div>
                <div class="stat-label">Total de Livros</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalMembros">-</div>
                <div class="stat-label">Membros Ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="emprestimosAtivos">-</div>
                <div class="stat-label">Empréstimos Ativos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="reservasAbertas">-</div>
                <div class="stat-label">Reservas Abertas</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="chart-container">
                <h3 class="chart-title">📊 Status dos Empréstimos</h3>
                <div class="chart-wrapper">
                    <canvas id="emprestimosChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">📈 Livros por Categoria</h3>
                <div class="chart-wrapper">
                    <canvas id="categoriasChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">🎯 Status das Reservas</h3>
                <div class="chart-wrapper">
                    <canvas id="reservasChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">💰 Planos de Assinatura</h3>
                <div class="chart-wrapper">
                    <canvas id="planosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuração global dos gráficos
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.font.size = 12;

        // Dados simulados para demonstração (em produção, viriam do PHP/MySQL)
        const dadosSimulados = {
            emprestimos: {
                'Empréstimo Ativo': 45,
                'Empréstimo Atrasado': 8,
                'Renovação Ativa': 12,
                'Renovação Atrasada': 3,
                'Finalizado': 150
            },
            categorias: {
                'Ficção': 120,
                'Não-ficção': 85,
                'Técnico': 65,
                'Infantil': 95,
                'Biografia': 40,
                'História': 75
            },
            reservas: {
                'Aberta': 25,
                'Cancelada': 8,
                'Finalizada': 180,
                'Atrasada': 5
            },
            planos: {
                'Básico': 80,
                'Premium': 45,
                'Família': 25,
                'Estudante': 60
            }
        };

        // Atualizar estatísticas
        document.getElementById('totalLivros').textContent = '480';
        document.getElementById('totalMembros').textContent = '210';
        document.getElementById('emprestimosAtivos').textContent = '68';
        document.getElementById('reservasAbertas').textContent = '25';

        // Gráfico de Empréstimos (Rosca)
        const emprestimosCtx = document.getElementById('emprestimosChart').getContext('2d');
        new Chart(emprestimosCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(dadosSimulados.emprestimos),
                datasets: [{
                    data: Object.values(dadosSimulados.emprestimos),
                    backgroundColor: [
                        '#4CAF50',
                        '#FF6B6B',
                        '#4FC3F7',
                        '#FFB74D',
                        '#9C27B0'
                    ],
                    borderWidth: 0,
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
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Gráfico de Categorias (Barra)
        const categoriasCtx = document.getElementById('categoriasChart').getContext('2d');
        new Chart(categoriasCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(dadosSimulados.categorias),
                datasets: [{
                    label: 'Quantidade de Livros',
                    data: Object.values(dadosSimulados.categorias),
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#f5576c',
                        '#4facfe',
                        '#00f2fe'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Gráfico de Reservas (Pizza)
        const reservasCtx = document.getElementById('reservasChart').getContext('2d');
        new Chart(reservasCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(dadosSimulados.reservas),
                datasets: [{
                    data: Object.values(dadosSimulados.reservas),
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#17a2b8',
                        '#dc3545'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Gráfico de Planos (Barra Horizontal)
        const planosCtx = document.getElementById('planosChart').getContext('2d');
        new Chart(planosCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(dadosSimulados.planos),
                datasets: [{
                    label: 'Assinantes',
                    data: Object.values(dadosSimulados.planos),
                    backgroundColor: [
                        '#FF6B6B',
                        '#4ECDC4',
                        '#45B7D1',
                        '#96CEB4'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.chart-container, .stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

    <?php
    // Exemplo de conexão com banco de dados (descomentado para uso real)
    /*
    $host = 'localhost';
    $dbname = 'biblioteca';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta para empréstimos por status
        $stmt = $pdo->prepare("SELECT emp_status, COUNT(*) as total FROM EMPRESTIMO GROUP BY emp_status");
        $stmt->execute();
        $emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para livros por categoria
        $stmt = $pdo->prepare("
            SELECT c.cat_nome, COUNT(l.pk_liv) as total 
            FROM CATEGORIA c 
            LEFT JOIN LIVRO l ON c.pk_cat = l.fk_cat 
            GROUP BY c.pk_cat, c.cat_nome
        ");
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para reservas por status
        $stmt = $pdo->prepare("SELECT res_status, COUNT(*) as total FROM RESERVA GROUP BY res_status");
        $stmt->execute();
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para membros por plano
        $stmt = $pdo->prepare("
            SELECT p.plan_nome, COUNT(m.pk_mem) as total 
            FROM PLANO p 
            LEFT JOIN MEMBRO m ON p.pk_plan = m.fk_plan 
            GROUP BY p.pk_plan, p.plan_nome
        ");
        $stmt->execute();
        $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Converter dados para JavaScript
        echo "<script>";
        echo "const dadosReais = {";
        echo "emprestimos: " . json_encode($emprestimos) . ",";
        echo "categorias: " . json_encode($categorias) . ",";
        echo "reservas: " . json_encode($reservas) . ",";
        echo "planos: " . json_encode($planos);
        echo "};";
        echo "</script>";

    } catch(PDOException $e) {
        echo "Erro na conexão: " . $e->getMessage();
    }
    */
    ?>
</body>
</html>