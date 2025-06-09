<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//FUNÇÃO PARA CONEXÃO COM BANCO DE DADOS
function conectaBd() {
    require_once "conexao.php";
    $conexao = conectarBanco();
    return $conexao;
}

//FUNÇÃO PARA REALIZAR LOGIN
function login() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if(!empty($usuario) && !empty($senha)) {
            $conexao = conectaBd();
            $sql = "SELECT pk_user, user_login, user_senha FROM usuario WHERE user_login = :user_login";
            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":user_login", $usuario);
            $stmt-> execute();
            $usuario = $stmt-> fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['user_senha'])) {
                $_SESSION['pk_user'] = $usuario['pk_user'];
                $_SESSION['user_login'] = $usuario['user_login'];

                header("Location: template/gestao/home.php");
                exit();
            } else {
                header("Location: template/index.php?erro=1");
                echo "<script> alert('Senha ou Usuário incorretos!'); </script>";
                exit();
            }
        } else {
            header("Location: template/index.php?erro=1");
            echo "<script> alert('Preencha todos os campos!'); </script>";
            exit();
        }
    }
}

//FUNÇÃO PARA VERIFICAR SE O USUÁRIO ESTÁ LOGADO
function verificarLogin($pagina, $id) {
    if(isset($id)) {
        header('Location: template/gestao/'.$pagina.'.php');
        exit;
    } else {
        header('Location: template/index.php?erro=2');
        echo "<script> alert('Você precisa estar logado para acessar esta página!'); </script>";
        exit();
    }
}

//FUNÇÃO DE LISTAGEM DOS DADOS ARMAZENADOS NO BANCO
function listar($tabela) {
    $conexao = conectaBd();
    $stmt = $conexao->prepare("SELECT * FROM $tabela");
    $stmt->execute();
    $variavel = $stmt->fetchAll();
    return $variavel;
}

function contarTotal($tabela) {
    $conexao = conectaBd();
    $stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM $tabela");
    $stmt->execute();
    $variavel = $stmt->fetch(PDO::FETCH_ASSOC);
    return $variavel;
}

//------------------------ FUNÇÕES CADASTRAMENTO ------------------------

function cadastrarMembro() {
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
}

?>