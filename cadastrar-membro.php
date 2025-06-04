<?php

function conectaBd() {
    require_once "conexao.php";
    $conexao = conectarBanco();
    return $conexao;
}

if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $conexao = conectaBd();

        $nome = filter_input(INPUT_POST, 'mem_nome', FILTER_SANITIZE_STRING);
        $cpf = filter_input(INPUT_POST, 'mem_cpf', FILTER_SANITIZE_STRING);
        $telefone = filter_input(INPUT_POST, 'mem_telefone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'mem_email', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'mem_senha', FILTER_SANITIZE_STRING);
        $dataInscricao = filter_input(INPUT_POST, 'mem_dataInscricao', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'mem_status', FILTER_SANITIZE_STRING);
        $plano = filter_input(INPUT_POST, 'fk_plan', FILTER_SANITIZE_NUMBER_INT);
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO membro(mem_nome, mem_cpf, mem_telefone, mem_email, mem_senha,  mem_dataInscricao, mem_status, fk_plan)
                VALUES (:mem_nome, :mem_cpf, :mem_telefone, :mem_email, :mem_senha, :mem_dataInscricao, :mem_status, :fk_plan)";

        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":mem_nome", $nome);
        $stmt-> bindParam(":mem_cpf", $cpf);
        $stmt-> bindParam(":mem_telefone", $telefone);
        $stmt-> bindParam(":mem_email", $email);
        $stmt-> bindParam(":mem_senha", $senhaHash);
        $stmt-> bindParam(":mem_dataInscricao", $dataInscricao);
        $stmt-> bindParam(":mem_status", $status);
        $stmt-> bindParam(":fk_plan", $plano, PDO::PARAM_INT);

        try {
            $stmt-> execute();
            echo "<script> alert('Membro cadastrado com sucesso!'); </script>";
            header("Location: template/gestao/membro-gestao.php");
            exit();
        } catch (PDOException $e) {
            header("Location: template/cadastro/membro-cadastro.php?erro=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: template/cadastro/membro-cadastro.php");
        exit();
    }

?>