<?php

//ROTEAMENTO DE AÇÕES
$acao = $_GET['acao']??null;
switch($acao) {
    case 'cadastrar_membro':
        cadastrarMembro();
        break;
    default:
        break;
}


//FUNÇÃO PARA CONEXÃO COM BANCO DE DADOS
function conectaBd() {
    require_once "conexao.php";
    $conexao = conectarBanco();
    return $conexao;
}

//FUNÇÃO DE LISTAGEM DOS DADOS ARMAZENADOS NO BANCO
function listar($tabela) {
    $conexao = conectaBd();
    $stmt = $conexao->prepare("SELECT * FROM $tabela");
    $stmt->execute();
    //$result = $stmt->get_result();
    $variavel = $stmt->fetchAll();
    return $variavel;
}

function contarTotal($tabela) {
    $conexao = conectaBd();
    $stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM $tabela");
    $stmt->execute();
    $result = $stmt->get_result();
    $variavel = $result->fetch_assoc();
    return $variavel;
}

//------------------------ FUNÇÕES MEMBROS ------------------------

function cadastrarMembro() {
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $conexao = conectaBd();

        $sql = "INSERT INTO membro(mem_nome, mem_cpf, mem_telefone, mem_email, mem_senha,  mem_dataInscricao, mem_status, fk_plan)
                VALUES (:mem_nome, :mem_cpf, :mem_telefone, :mem_email, :mem_senha, :mem_dataInscricao, :mem_status, :fk_plan)";

        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":mem_nome", $_POST["mem_nome"]);
        $stmt-> bindParam(":mem_cpf", $_POST["mem_cpf"]);
        $stmt-> bindParam(":mem_telefone", $_POST["mem_telefone"]);
        $stmt-> bindParam(":mem_email", $_POST["mem_email"]);
        $stmt-> bindParam(":mem_senha", $_POST["mem_senha"]);
        $stmt-> bindParam(":mem_dataInscricao", $_POST["mem_dataInscricao"]);
        $stmt-> bindParam(":mem_status", $_POST["mem_status"]);
        $stmt-> bindParam(":fk_plan", $_POST["fk_plan"], PDO::PARAM_INT);

        try {
            $stmt-> execute();
            echo "Membro cadastrado com sucesso!";
            header("Location: membro-gestao.php");
            exit();
        } catch (PDOException $e) {
            header("Location: membro-cadastro.php?erro=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: membro-cadastro.php");
        exit();
    }
}



?>