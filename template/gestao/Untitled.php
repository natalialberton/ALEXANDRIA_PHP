<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico Semanal de Empréstimos e Reservas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container-grafico {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Gráfico Semanal de Empréstimos e Reservas</h1>
    <div class="container-grafico">
        <canvas id="graficoSemanal"></canvas>
    </div>

<?php
$pdo = conectarBanco(); 
    // Consulta para obter contagens semanais de empréstimos e reservas
    $sql = "
        SELECT 
            DATE_FORMAT(emp_dataEmp, '%Y-%U') AS semana,
            COUNT(DISTINCT e.pk_emp) AS qtd_emprestimos,
            COUNT(DISTINCT r.pk_res) AS qtd_reservas
        FROM EMPRESTIMO e
        LEFT JOIN RESERVA r ON DATE_FORMAT(e.emp_dataEmp, '%Y-%U') = DATE_FORMAT(r.res_dataMarcada, '%Y-%U')
        GROUP BY semana
        UNION
        SELECT 
            DATE_FORMAT(res_dataMarcada, '%Y-%U') AS semana,
            COUNT(DISTINCT e.pk_emp) AS qtd_emprestimos,
            COUNT(DISTINCT r.pk_res) AS qtd_reservas
        FROM RESERVA r
        LEFT JOIN EMPRESTIMO e ON DATE_FORMAT(r.res_dataMarcada, '%Y-%U') = DATE_FORMAT(e.emp_dataEmp, '%Y-%U')
        GROUP BY semana
        ORDER BY semana DESC
        LIMIT 8
    ";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "<p style='color: red; text-align: center;'>Erro na consulta: " . $e->getMessage() . "</p>";
        exit;
    }

    // Processar dados para o Chart.js
    $semanas = [];
    $emprestimos = [];
    $reservas = [];

    foreach ($dados as $linha) {
        $semana = $linha['semana'];
        if (!in_array($semana, $semanas)) {
            $semanas[] = $semana;
            $emprestimos[$semana] = 0;
            $reservas[$semana] = 0;
        }
        $emprestimos[$semana] += $linha['qtd_emprestimos'];
        $reservas[$semana] += $linha['qtd_reservas'];
    }

    $semanas = array_reverse($semanas);
    $contagem_emprestimos = array_values(array_intersect_key($emprestimos, array_flip($semanas)));
    $contagem_reservas = array_values(array_intersect_key($reservas, array_flip($semanas)));

    // Formatar semanas para exibição
    $semanas_formatadas = array_map(function($semana) {
        $ano_semana = explode('-', $semana);
        $ano = $ano_semana[0];
        $num_semana = ltrim($ano_semana[1], '0') ?: '1';
        return "Semana $num_semana/$ano";
    }, $semanas);
    ?>

    <script>
        const ctx = document.getElementById('graficoSemanal').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($semanas_formatadas); ?>,
                datasets: [
                    {
                        label: 'Empréstimos',
                        data: <?php echo json_encode($contagem_emprestimos); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Reservas',
                        data: <?php echo json_encode($contagem_reservas); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantidade'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Semana'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Empréstimos e Reservas por Semana'
                    }
                }
            }
        });
    </script>
</body>
</html>