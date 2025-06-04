<?php

function conectaBd() {
    require_once "conexao.php";
    $conexao = conectarBanco();
    return $conexao;
}

//ADICIONAR VALIDAÇÃO --> SE TIVER EMPRÉSTIMO, NÃO PODE DELETAR

error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_clean();

$conexao = conectaBd();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $sql = "DELETE FROM membro WHERE pk_mem = :pk_mem";
    $stmt = $conexao-> prepare($sql);
    $stmt-> bindParam(":pk_mem", $id);
    
    if ($stmt-> execute()) {
        echo "<script> alert('Membro excluído com sucesso!'); </script>";
    } else {
        die("<script> alert('Erro ao excluir o membro: ".$stmt-> error."'; </script>");
    }

} else {
    echo "<script> alert('ID inválido!'); </script>";
}

header("Location: template/gestao/membro-gestao.php");
exit();

?>