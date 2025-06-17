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
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if (!empty($usuario) && !empty($senha)) {
        $conexao = conectaBd();
        $sql = "SELECT pk_user, user_login, user_senha, user_nome FROM usuario WHERE user_login = :user_login";
        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":user_login", $usuario);
        $stmt-> execute();
        $usuario = $stmt-> fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['user_senha'])) {
            $_SESSION['pk_user'] = $usuario['pk_user'];
            $_SESSION['user_nome'] = $usuario['user_nome'];

            header("Location: gestao/home.php");
            exit();
        } else {
            echo "<script> window.location.href = 'index.php?erro=1'; 
                           alert('Senha ou Usuário incorretos!');
                  </script>";
            exit();
        }
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

function selecionarPorId($tabela, $id, $chavePrimaria) {
    $conexao = conectaBd();
    $stmt = $conexao-> prepare("SELECT * FROM $tabela WHERE $chavePrimaria = :id");
    $stmt-> bindParam(":id", $id, PDO::PARAM_INT);
    $stmt-> execute();
    $variavel = $stmt-> fetch(PDO::FETCH_ASSOC);
    return $variavel;
}

//FUNÇÃO PARA PESQUISAR DADOS
if (isset($_GET['termoBusca']) || $_GET['termoBusca']) {
    $conexao = conectaBd();
    $termoBusca = isset($_GET['termo']) ? $_GET['termo'] : '';
    $termoBusca = '%' . $termoBusca . '%';
    $sql = "SELECT * FROM membro WHERE mem_nome LIKE :termoBusca";
    $stmt = $conexao-> prepare($sql);
    $stmt-> bindParam(":termoBusca", $termoBusca, PDO::PARAM_STR);
    $stmt-> execute();
    $membros = $stmt-> fetchAll(PDO::FETCH_ASSOC);
    return $membros;
}

//--------------------------------------------------- FUNÇÕES MEMBRO ---------------------------------------------------
function membro($acao, $id) {
    $conexao = conectaBd();

    $nome = filter_input(INPUT_POST, 'mem_nome');
    $cpf = filter_input(INPUT_POST, 'mem_cpf');
    $telefone = filter_input(INPUT_POST, 'mem_telefone');
    $email = filter_input(INPUT_POST, 'mem_email');
    $senha = filter_input(INPUT_POST, 'mem_senha');
    $dataInscricao = filter_input(INPUT_POST, 'mem_dataInscricao');
    $status = filter_input(INPUT_POST, 'mem_status') ?? 'Ativo';
    $planoNome = filter_input(INPUT_POST, 'plan_nome');
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sqlPlano = "SELECT pk_plan FROM plano WHERE plan_nome LIKE :planoNome";
    $stmtPlano = $conexao->prepare($sqlPlano);
    $stmtPlano->bindParam(':planoNome', $planoNome);
    $stmtPlano->execute();
    $plano = $stmtPlano->fetch(PDO::FETCH_ASSOC);

    if($acao == 1 || $acao == 2) {
        if ($plano) {
            $fk_plan = $plano['pk_plan'];

            if($acao == 1) {
                $msgSucesso = "Membro cadastrado com sucesso!";
                $sql = "INSERT INTO membro (mem_nome, mem_cpf, mem_telefone, mem_email, mem_senha, mem_dataInscricao, mem_status, fk_plan) 
                        VALUES (:nome, :cpf, :telefone, :email, :senhaHash, :dataInscricao, :status, :fk_plan)";
                $stmt = $conexao->prepare($sql);
            } elseif($acao == 2 && $id > 0) {
                $msgSucesso = "Membro alterado com sucesso!";
                $sql = "UPDATE membro SET mem_nome=:nome, mem_cpf=:cpf, mem_telefone=:telefone, mem_email=:email, mem_senha=:senhaHash, 
                                    mem_dataInscricao=:dataInscricao, mem_status=:status, fk_plan=:fk_plan WHERE pk_mem=:pk_mem";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
            }

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senhaHash', $senhaHash);
            $stmt->bindParam(':dataInscricao', $dataInscricao);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':fk_plan', $fk_plan, PDO::PARAM_INT);

        } else {
        echo "<script> window.location.href = 'membro-gestao.php';
                        alert('Plano não encontrado!'); 
                </script>";
        exit();
        }

    } elseif($acao == 3 && $id > 0) {
        $msgSucesso = "Membro excluído com sucesso!";
        $sqlCheck = "SELECT COUNT(*) FROM emprestimo WHERE fk_mem = :pk_mem";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bindParam(":pk_mem", $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $emprestimos = $stmtCheck->fetchColumn();

        if ($emprestimos > 0) {
            echo "<script>
                    alert('Não é possível excluir um membro com empréstimos registrados!');
                    window.location.href = 'membro-gestao.php';
                </script>";
            exit();
        }

        $sql = "DELETE FROM membro WHERE pk_mem = :pk_mem";
        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
    }
    try {
        $stmt-> execute();
        echo "<script> window.location.href = 'membro-gestao.php';
                       alert('$msgSucesso'); 
              </script>";
        exit();
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'membro-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}



//--------------------------------------------------- FUNÇÕES LIVRO ---------------------------------------------------
function cadastrarLivro() {
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $conexao = conectaBd();

        $titulo = filter_input(INPUT_POST, 'liv_titulo');
        $isbn = filter_input(INPUT_POST, 'liv_isbn');
        $autor = filter_input(INPUT_POST, 'fk_aut');
        $categoria = filter_input(INPUT_POST, 'fk_cat');
        $edicao = filter_input(INPUT_POST, 'liv_edicao');
        $anoPublicacao = filter_input(INPUT_POST, 'liv_anoPublicacao');
        $dataAlteracaoEstoque = filter_input(INPUT_POST, 'liv_dataAlteracaoEstoque');
        $estoque = filter_input(INPUT_POST, 'liv_estoque');
        $idioma = filter_input(INPUT_POST, 'liv_idioma');
        $numPaginas = filter_input(INPUT_POST, 'liv_num_paginas');
        $sinopse = filter_input(INPUT_POST, 'liv_sinopse');

        $sql = "INSERT INTO livro(liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse,  liv_estoque,
                liv_dataAlteracaoEstoque, liv_idioma, liv_num_paginas, fk_aut, fk_cat)
                VALUES (:liv_titulo, :liv_isbn, :liv_edicao, :liv_anoPublicacao, :liv_sinopse, :liv_estoque, 
                :liv_dataAlteracaoEstoque, :liv_idioma, :liv_num_paginas, :fk_aut, :fk_cat)";

        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":liv_titulo", $titulo);
        $stmt-> bindParam(":liv_isbn", $isbn);
        $stmt-> bindParam(":liv_edicao", $edicao);
        $stmt-> bindParam(":liv_anoPublicacao", $anoPublicacao);
        $stmt-> bindParam(":liv_sinopse", $sinopse);
        $stmt-> bindParam(":liv_estoque", $estoque);
        $stmt-> bindParam(":liv_dataAlteracaoEstoque", $dataAlteracaoEstoque);
        $stmt-> bindParam(":liv_idioma", $idioma);
        $stmt-> bindParam(":liv_num_paginas", $numPaginas);
        $stmt-> bindParam(":fk_aut", $autor, PDO::PARAM_INT);
        $stmt-> bindParam(":fk_cat", $categoria, PDO::PARAM_INT);

        try {
            $stmt-> execute();
            echo "<script> window.location.href = '../gestao/livro-gestao.php';
                            alert('Livro cadastrado com sucesso!'); 
                  </script>";
            exit();
        } catch (PDOException $e) {
            header("Location: livro-cadastro.php?erro=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: livro-cadastro.php");
        exit();
    }
}

function editarLivro($id) {
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $conexao = conectaBd();

        $titulo = filter_input(INPUT_POST, 'liv_titulo');
        $isbn = filter_input(INPUT_POST, 'liv_isbn');
        $autor = filter_input(INPUT_POST, 'fk_aut');
        $categoria = filter_input(INPUT_POST, 'fk_cat');
        $edicao = filter_input(INPUT_POST, 'liv_edicao');
        $anoPublicacao = filter_input(INPUT_POST, 'liv_anoPublicacao');
        $dataAlteracaoEstoque = filter_input(INPUT_POST, 'liv_dataAlteracaoEstoque');
        $estoque = filter_input(INPUT_POST, 'liv_estoque');
        $idioma = filter_input(INPUT_POST, 'liv_idioma');
        $numPaginas = filter_input(INPUT_POST, 'liv_num_paginas');
        $sinopse = filter_input(INPUT_POST, 'liv_sinopse');

        if ($id > 0) {
            $sql = "UPDATE livro SET liv_titulo=:liv_titulo, liv_isbn=:liv_isbn, liv_edicao=:liv_edicao, liv_anoPublicacao=:liv_anoPublicacao, liv_sinopse=:liv_sinopse, liv_estoque=:liv_estoque, 
                    liv_dataAlteracaoEstoque=:liv_dataAlteracaoEstoque, liv_idioma=:liv_idioma, liv_num_paginas=:liv_num_paginas, fk_aut=:fk_aut, fk_cat=:fk_cat WHERE pk_liv=:pk_liv";

            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":pk_liv", $id, PDO::PARAM_INT);
            $stmt-> bindParam(":liv_titulo", $titulo);
            $stmt-> bindParam(":liv_isbn", $isbn);
            $stmt-> bindParam(":liv_edicao", $edicao);
            $stmt-> bindParam(":liv_anoPublicacao", $anoPublicacao);
            $stmt-> bindParam(":liv_sinopse", $sinopse);
            $stmt-> bindParam(":liv_estoque", $estoque);
            $stmt-> bindParam(":liv_dataAlteracaoEstoque", $dataAlteracaoEstoque);
            $stmt-> bindParam(":liv_idioma", $idioma);
            $stmt-> bindParam(":liv_num_paginas", $numPaginas);
            $stmt-> bindParam(":fk_aut", $autor, PDO::PARAM_INT);
            $stmt-> bindParam(":fk_cat", $categoria, PDO::PARAM_INT);

            try {
            $stmt-> execute();
            echo "<script> window.location.href = '../gestao/livro-gestao.php';
                            alert('Livro alterado com sucesso!'); 
                  </script>";
            exit();
            } catch (PDOException $e) {
                echo "<script> window.location.href = 'template/gestao/livro-gestao.php?erro= " . urlencode($e->getMessage()) . "';
                               alert('Erro ao realizar alteração!'); 
                    </script>";
                exit();
            }
        } else {
            echo "<script> window.location.href = 'template/gestao/livro-gestao.php';
                        alert('ID Inválido!'); 
                </script>";
            exit();
        }
    } else {
        header("Location: template/gestao/livro-gestao.php");
        exit();
    }
}

?>