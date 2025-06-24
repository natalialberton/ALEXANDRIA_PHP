<?php
// Desabilitar exibição de erros na saída
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Headers JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Função para conectar ao banco
function conectarBanco() {
    $dsn = "mysql:host=localhost:3307;dbname=alexandria;charset=utf8";
    $usuario = "root";
    $senha = "";

    try {
        $conn = new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);  
        return $conn;
    } catch (PDOException $e) {
        error_log("Erro ao conectar ao banco: " . $e->getMessage());
        die(json_encode(['success' => false, 'error' => 'Erro ao conectar ao banco']));
    }
}

// Função para retornar erro em JSON
function retornarErro($mensagem) {
    echo json_encode(['success' => false, 'error' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Conectar ao banco
    $pdo = conectarBanco();
    
    // Inicializar arrays
    $emprestimos = [];
    $reservas = [];
    $totais = [
        'total_emprestimos' => 0,
        'total_reservas' => 0,
        'emprestimos_ativos' => 0,
        'reservas_abertas' => 0
    ];
    
    // Consulta para empréstimos por status
    $sqlEmprestimos = "SELECT emp_status, COUNT(*) as quantidade FROM EMPRESTIMO GROUP BY emp_status ORDER BY quantidade DESC";
    
    try {
        $stmtEmp = $pdo->prepare($sqlEmprestimos);
        $stmtEmp->execute();
        $emprestimos = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar empréstimos: " . $e->getMessage());
        // Manter array vazio em caso de erro
    }
    
    // Consulta para reservas por status
    $sqlReservas = "SELECT res_status, COUNT(*) as quantidade FROM RESERVA GROUP BY res_status ORDER BY quantidade DESC";
    
    try {
        $stmtRes = $pdo->prepare($sqlReservas);
        $stmtRes->execute();
        $reservas = $stmtRes->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar reservas: " . $e->getMessage());
        // Manter array vazio em caso de erro
    }
    
    // Totais gerais
    try {
        $sqlTotais = "
            SELECT 
                (SELECT COUNT(*) FROM EMPRESTIMO) as total_emprestimos,
                (SELECT COUNT(*) FROM RESERVA) as total_reservas,
                (SELECT COUNT(*) FROM EMPRESTIMO WHERE emp_status = 'Empréstimo Ativo') as emprestimos_ativos,
                (SELECT COUNT(*) FROM RESERVA WHERE res_status = 'Aberta') as reservas_abertas
        ";
        
        $stmtTotais = $pdo->prepare($sqlTotais);
        $stmtTotais->execute();
        $resultado = $stmtTotais->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            $totais = $resultado;
        }
    } catch (Exception $e) {
        error_log("Erro ao buscar totais: " . $e->getMessage());
        // Manter valores padrão se der erro
    }
    
    // Preparar resposta
    $response = [
        'success' => true,
        'data' => [
            'emprestimos_por_status' => $emprestimos,
            'reservas_por_status' => $reservas,
            'totais' => $totais
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro PDO: " . $e->getMessage());
    retornarErro('Erro de conexão com banco de dados');
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    retornarErro('Erro interno do servidor');
}
?>