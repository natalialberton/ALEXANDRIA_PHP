<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../geral.php';
$pdo = conectaBd(); 

$tipo = $_GET['tipo'] ?? '';


try {
    switch($tipo) {
        case 'emprestimos_reservas_mes':
            $sql = "
                SELECT 
                    mes,
                    SUM(emprestimos) as emprestimos,
                    SUM(reservas) as reservas
                FROM (
                    SELECT 
                        DATE_FORMAT(emp_dataEmp, '%Y-%m') as mes,
                        COUNT(*) as emprestimos, 
                        0 as reservas
                    FROM EMPRESTIMO 
                    WHERE emp_dataEmp >= '2023-01-01'
                    GROUP BY DATE_FORMAT(emp_dataEmp, '%Y-%m')
                    
                    UNION ALL
                    
                    SELECT 
                        DATE_FORMAT(res_dataMarcada, '%Y-%m') as mes,
                        0 as emprestimos, 
                        COUNT(*) as reservas
                    FROM RESERVA 
                    WHERE res_dataMarcada >= '2023-01-01'
                    GROUP BY DATE_FORMAT(res_dataMarcada, '%Y-%m')
                ) AS combined
                GROUP BY mes
                ORDER BY mes
            ";
            break;

        case 'livros_mais_emprestados':
           
            $sql = "
                SELECT 
                    l.liv_titulo as titulo,
                    COUNT(e.pk_emp) as total_emprestimos
                FROM LIVRO l
                LEFT JOIN EMPRESTIMO e ON l.pk_liv = e.fk_liv
                GROUP BY l.pk_liv, l.liv_titulo
                HAVING total_emprestimos > 0
                ORDER BY total_emprestimos DESC
                LIMIT 10
            ";
            break;

        case 'categorias_mais_emprestadas':
            // Categorias mais emprestadas
            $sql = "
                SELECT 
                    c.cat_nome as categoria,
                    COUNT(e.pk_emp) as total_emprestimos
                FROM CATEGORIA c
                INNER JOIN LIVRO l ON c.pk_cat = l.fk_cat
                INNER JOIN EMPRESTIMO e ON l.pk_liv = e.fk_liv
                GROUP BY c.pk_cat, c.cat_nome
                ORDER BY total_emprestimos DESC
                LIMIT 8
            ";
            break;

        case 'autores_mais_emprestados':
            // Autores mais emprestados
            $sql = "
                SELECT 
                    a.aut_nome as autor,
                    COUNT(e.pk_emp) as total_emprestimos
                FROM AUTOR a
                INNER JOIN LIVRO l ON a.pk_aut = l.fk_aut
                INNER JOIN EMPRESTIMO e ON l.pk_liv = e.fk_liv
                GROUP BY a.pk_aut, a.aut_nome
                ORDER BY total_emprestimos DESC
                LIMIT 8
            ";
            break;

        case 'multas_mes':
            
            $sql = "
                SELECT 
                    DATE_FORMAT(e.emp_dataEmp, '%Y-%m') as mes,
                    SUM(m.mul_valor) as total_multas,
                    COUNT(m.pk_mul) as quantidade_multas
                FROM MULTA m
                INNER JOIN EMPRESTIMO e ON m.fk_emp = e.pk_emp
                WHERE e.emp_dataEmp >= '2023-01-01'
                GROUP BY DATE_FORMAT(e.emp_dataEmp, '%Y-%m')
                ORDER BY mes
            ";
            break;

        default:
            throw new Exception('Tipo de gráfico não especificado');
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dados = $stmt->fetchAll();

    echo json_encode($dados);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>

