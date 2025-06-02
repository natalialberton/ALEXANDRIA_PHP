<?php

//ROTEAMENTO DE AÇÕES
$acao = $_GET['acao'] ?? '';
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
    $result = $stmt->get_result();
    $variavel = $result->fetch_all(MYSQLI_ASSOC);
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

        $nome = filter_input(INPUT_POST, 'mem_nome', FILTER_SANITIZE_STRING);
        $cpf= filter_input(INPUT_POST, 'mem_cpf', FILTER_SANITIZE_STRING);
        $telefone = filter_input(INPUT_POST, 'mem_telefone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'mem_email', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'mem_senha', FILTER_SANITIZE_STRING);
        $dataInscricao = filter_input(INPUT_POST, 'mem_dataInscricao', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'mem_status', FILTER_SANITIZE_STRING);
        $plano = filter_input(INPUT_POST, 'fk_plan', FILTER_SANITIZE_NUMBER_INT);

        $sql = "INSERT INTO membro (mem_nome, mem_cpf, mem_telefone, mem_email, mem_senha,  mem_dataInscricao, mem_status, fk_plan)
                VALUES (:mem_nome, :mem_cpf, :mem_telefone, :mem_email, :mem_senha, :mem_dataInscricao, :mem_status, :fk_plan)";

        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":mem_nome", $nome);
        $stmt-> bindParam(":mem_cpf", $cpf);
        $stmt-> bindParam(":mem_telefone", $telefone);
        $stmt-> bindParam(":mem_email", $email);
        $stmt-> bindParam(":mem_senha", $senha);
        $stmt-> bindParam(":mem_dataInscricao", $dataInscricao);
        $stmt-> bindParam(":mem_status", $status);
        $stmt-> bindParam(":fk_plan", $plano, PDO::PARAM_INT);

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