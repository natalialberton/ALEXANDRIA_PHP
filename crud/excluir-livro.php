<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_clean();

require_once "../funcoes.php";
$conexao = conectaBd();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

//ADICIONAR VALIDAÇÃO PARA VERIFICAR SE EXISTEM MULTAS PENDENTES

if ($id > 0) {
    $sqlCheck = "SELECT COUNT(*) FROM emprestimo WHERE fk_liv = :pk_liv";
    $stmtCheck = $conexao->prepare($sqlCheck);
    $stmtCheck->bindParam(":pk_liv", $id, PDO::PARAM_INT);
    $stmtCheck->execute();
    $emprestimos = $stmtCheck->fetchColumn();

    if ($emprestimos > 0) {
        echo "<script>
                alert('Não é possível excluir um livro registrado em empréstimo!');
                window.location.href = '../livro-gestao.php';
              </script>";
        exit();
    }

    $sql = "DELETE FROM livro WHERE pk_liv = :pk_liv";
    $stmt = $conexao-> prepare($sql);
    $stmt-> bindParam(":pk_liv", $id, PDO::PARAM_INT);
    
    if ($stmt-> execute()) {
        echo "<script> window.location.href = 'template/gestao/livro-gestao.php?erro=4';
                       alert('Livro excluído com sucesso!'); </script>";
        exit();
    } else {
        echo "<script> window.location.href = 'template/gestao/livro-gestao.php?erro=4';
                       alert('Erro ao excluir livro!'; </script>";
        exit();
    }

    header("Location: template/gestao/livro-gestao.php");
    exit();
}

?>