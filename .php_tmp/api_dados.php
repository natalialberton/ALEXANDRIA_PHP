<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once "../../geral.php";

// Check if user is authenticated
if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $conexao = conectaBd();
    
    // Query for loan statistics by status
    $sqlEmprestimosStatus = "
        SELECT 
            emp_status,
            COUNT(*) as quantidade
        FROM EMPRESTIMO
        GROUP BY emp_status
        ORDER BY quantidade DESC
    ";
    $resEmprestimosStatus = $conexao->query($sqlEmprestimosStatus)->fetchAll(PDO::FETCH_ASSOC);
    
    // Query for reservation statistics by status
    $sqlReservasStatus = "
        SELECT 
            res_status,
            COUNT(*) as quantidade
        FROM RESERVA
        GROUP BY res_status
        ORDER BY quantidade DESC
    ";
    $resReservasStatus = $conexao->query($sqlReservasStatus)->fetchAll(PDO::FETCH_ASSOC);
    
    // Query for totals
    $sqlTotais = "
        SELECT 
            (SELECT COUNT(*) FROM EMPRESTIMO) as total_emprestimos,
            (SELECT COUNT(*) FROM RESERVA) as total_reservas,
            (SELECT COUNT(*) FROM EMPRESTIMO WHERE emp_status = 'Empréstimo Ativo') as emprestimos_ativos,
            (SELECT COUNT(*) FROM RESERVA WHERE res_status = 'Aberta') as reservas_abertas
    ";
    $resTotais = $conexao->query($sqlTotais)->fetch(PDO::FETCH_ASSOC);
    
    // Weekly data for chart (loans per week)
    $sqlSemanal = "
        SELECT 
            YEAR(emp_dataEmp) as ano,
            WEEK(emp_dataEmp) as semana,
            COUNT(*) as quantidade
        FROM EMPRESTIMO 
        WHERE emp_dataEmp >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
        GROUP BY YEAR(emp_dataEmp), WEEK(emp_dataEmp)
        ORDER BY YEAR(emp_dataEmp), WEEK(emp_dataEmp)
        LIMIT 7
    ";
    $resSemanal = $conexao->query($sqlSemanal)->fetchAll(PDO::FETCH_ASSOC);
    
    // Daily data for all dates (for date picker functionality)
    $sqlDiario = "
        SELECT 
            DATE(emp_dataEmp) as data_ref,
            COUNT(*) as qtd_emprestimos,
            (SELECT COUNT(*) FROM RESERVA WHERE DATE(res_dataMarcada) = DATE(emp_dataEmp)) as qtd_reservas
        FROM EMPRESTIMO 
        WHERE emp_dataEmp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(emp_dataEmp)
        ORDER BY DATE(emp_dataEmp) DESC
    ";
    $resDiario = $conexao->query($sqlDiario)->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepare response data in the format expected by the JavaScript
    $response = [
        'success' => true,
        'data' => [
            'emprestimos_por_status' => $resEmprestimosStatus,
            'reservas_por_status' => $resReservasStatus,
            'totais' => [
                'total_emprestimos' => (int)$resTotais['total_emprestimos'],
                'total_reservas' => (int)$resTotais['total_reservas'],
                'emprestimos_ativos' => (int)$resTotais['emprestimos_ativos'],
                'reservas_abertas' => (int)$resTotais['reservas_abertas']
            ],
            'emprestimos_por_semana' => $resSemanal,
            'diario' => $resDiario
        ]
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>