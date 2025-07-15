<?php
// Ativar debug temporariamente
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Log para debug
error_log("API chamada - Session status: " . (isset($_SESSION['statusUser']) ? $_SESSION['statusUser'] : 'not set'));

try {
    // Verificar se o arquivo geral.php existe
    $geralPath = "../../geral.php";
    if (!file_exists($geralPath)) {
        throw new Exception("Arquivo geral.php não encontrado em: " . $geralPath);
    }
    
    require_once $geralPath;
    
    // Verificar autenticação - mais flexível para debug
    if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
        // Para debug, vamos logar e continuar (remover depois)
        error_log("Usuário não autenticado ou status inválido");
        
        // Descomente a linha abaixo para forçar erro de autenticação
        // http_response_code(401);
        // echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
        // exit;
    }
    
    // Tentar conectar ao banco
    $conexao = conectaBd();
    if (!$conexao) {
        throw new Exception("Falha na conexão com o banco de dados");
    }
    
    error_log("Conexão com banco estabelecida");
    
    // Query for loan statistics by status
    $sqlEmprestimosStatus = "
        SELECT 
            emp_status,
            COUNT(*) as quantidade
        FROM EMPRESTIMO
        GROUP BY emp_status
        ORDER BY quantidade DESC
    ";
    
    $resEmprestimosStatus = [];
    try {
        $stmt = $conexao->query($sqlEmprestimosStatus);
        $resEmprestimosStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Empréstimos por status: " . count($resEmprestimosStatus) . " registros");
    } catch (Exception $e) {
        error_log("Erro na query empréstimos status: " . $e->getMessage());
    }
    
    // Query for reservation statistics by status
    $sqlReservasStatus = "
        SELECT 
            res_status,
            COUNT(*) as quantidade
        FROM RESERVA
        GROUP BY res_status
        ORDER BY quantidade DESC
    ";
    
    $resReservasStatus = [];
    try {
        $stmt = $conexao->query($sqlReservasStatus);
        $resReservasStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Reservas por status: " . count($resReservasStatus) . " registros");
    } catch (Exception $e) {
        error_log("Erro na query reservas status: " . $e->getMessage());
    }
    
    // Query for totals - versão mais robusta
    $sqlTotais = "
        SELECT 
            (SELECT COUNT(*) FROM EMPRESTIMO) as total_emprestimos,
            (SELECT COUNT(*) FROM RESERVA) as total_reservas,
            (SELECT COUNT(*) FROM EMPRESTIMO WHERE emp_status = 'Empréstimo Ativo') as emprestimos_ativos,
            (SELECT COUNT(*) FROM RESERVA WHERE res_status = 'Aberta') as reservas_abertas
    ";
    
    $resTotais = [
        'total_emprestimos' => 0,
        'total_reservas' => 0,
        'emprestimos_ativos' => 0,
        'reservas_abertas' => 0
    ];
    
    try {
        $stmt = $conexao->query($sqlTotais);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            $resTotais = $resultado;
        }
        error_log("Totais carregados com sucesso");
    } catch (Exception $e) {
        error_log("Erro na query totais: " . $e->getMessage());
    }
    
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
    
    $resSemanal = [];
    try {
        $stmt = $conexao->query($sqlSemanal);
        $resSemanal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Dados semanais: " . count($resSemanal) . " registros");
    } catch (Exception $e) {
        error_log("Erro na query semanal: " . $e->getMessage());
    }
    
    // Daily data - versão mais robusta
    $sqlDiario = "
        SELECT 
            DATE(emp_dataEmp) as data_ref,
            COUNT(*) as qtd_emprestimos,
            0 as qtd_reservas
        FROM EMPRESTIMO 
        WHERE emp_dataEmp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(emp_dataEmp)
        
        UNION ALL
        
        SELECT 
            DATE(res_dataMarcada) as data_ref,
            0 as qtd_emprestimos,
            COUNT(*) as qtd_reservas
        FROM RESERVA 
        WHERE res_dataMarcada >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(res_dataMarcada)
        
        ORDER BY data_ref DESC
    ";
    
    $resDiario = [];
    try {
        $stmt = $conexao->query($sqlDiario);
        $dadosBrutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Consolidar dados por data
        $diarioConsolidado = [];
        foreach ($dadosBrutos as $dado) {
            $data = $dado['data_ref'];
            if (!isset($diarioConsolidado[$data])) {
                $diarioConsolidado[$data] = [
                    'data_ref' => $data,
                    'qtd_emprestimos' => 0,
                    'qtd_reservas' => 0
                ];
            }
            $diarioConsolidado[$data]['qtd_emprestimos'] += intval($dado['qtd_emprestimos']);
            $diarioConsolidado[$data]['qtd_reservas'] += intval($dado['qtd_reservas']);
        }
        
        $resDiario = array_values($diarioConsolidado);
        error_log("Dados diários: " . count($resDiario) . " registros");
        
    } catch (Exception $e) {
        error_log("Erro na query diário: " . $e->getMessage());
    }
    
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
        ],
        'debug' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'session_status' => $_SESSION['statusUser'] ?? 'não definido',
            'queries_executed' => 4
        ]
    ];
    
    error_log("Resposta preparada com sucesso");
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Erro PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'file' => __FILE__,
        'line' => __LINE__
    ]);
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'file' => __FILE__,
        'line' => __LINE__
    ]);
}
?>