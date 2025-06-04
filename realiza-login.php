<?php

function conectaBd() {
    require_once "conexao.php";
    $conexao = conectarBanco();
    return $conexao;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $conexao = conectaBd();

    $email = $_POST['email'];

    //Prepara a consulta SQL segura
    $stmt = $conexao-> prepare("SELECT * FROM usuario WHERE user_email=:user_email");
    $stmt-> bindParam(":user_email", $email);
    $stmt-> execute();
    $usuario = $stmt-> fetch(PDO::FETCH_ASSOC);

    //Se houver resultado, o login é considerado bem sucedido
    if($usuario) {
        header("Location: template/gestao/home.php");
        exit();
    } else {
        echo "<script> alert('Email não encontrado!'); </script>";
    }
}

?>