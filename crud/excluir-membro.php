<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_clean();

require_once "../funcoes.php";
$conexao = conectaBd();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

//ADICIONAR VALIDAÇÃO PARA VERIFICAR SE EXISTEM MULTAS PENDENTES

if ($id > 0) {
    $sqlCheck = "SELECT COUNT(*) FROM emprestimo WHERE fk_mem = :pk_mem";
    $stmtCheck = $conexao->prepare($sqlCheck);
    $stmtCheck->bindParam(":pk_mem", $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    $emprestimos = $stmtCheck->fetchColumn();

    if ($emprestimos > 0) {
        echo "<script>
                alert('Não é possível excluir um membro com empréstimos registrados!');
                window.location.href = '../template/gestao/membro-gestao.php';
              </script>";
        exit();
    }

    $sql = "DELETE FROM membro WHERE pk_mem = :pk_mem";
    $stmt = $conexao-> prepare($sql);
    $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
    
    if ($stmt-> execute()) {
        echo "<script> window.location.href = '../template/gestao/membro-gestao.php';
              alert('Membro excluído com sucesso!'); </script>";
        exit();
    } else {
        echo "<script> window.location.href = '../template/gestao/membro-gestao.php?erro=4';
                       alert('Erro ao excluir membro!'; </script>";
        exit();
    }

    header("Location: ../template/gestao/membro-gestao.php");
    exit();
}

?>