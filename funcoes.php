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
function pesquisar($tabela, $busca, $coluna1, $coluna2) {
    $conexao = conectaBd();
    $sql = "SELECT * FROM $tabela WHERE $coluna1 LIKE :busca OR $coluna2 LIKE :busca ORDER BY $coluna1";
    $stmt = $conexao-> prepare($sql);
    $busca = "%$busca%";
    $stmt-> bindParam(":busca", $busca, PDO::PARAM_STR);
    $stmt-> execute();
    $variavel = $stmt-> fetchAll(PDO::FETCH_ASSOC);
    return $variavel;

}

//--------------------------------------------------- FUNÇÕES MEMBRO ---------------------------------------------------

function cadastrarMembro() {
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $conexao = conectaBd();

        $nome = filter_input(INPUT_POST, 'mem_nome');
        $cpf = filter_input(INPUT_POST, 'mem_cpf');
        $telefone = filter_input(INPUT_POST, 'mem_telefone');
        $email = filter_input(INPUT_POST, 'mem_email');
        $senha = filter_input(INPUT_POST, 'mem_senha');
        $dataInscricao = filter_input(INPUT_POST, 'mem_dataInscricao');
        $status = 'Ativo';
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
            echo "<script> window.location.href = 'template/gestao/membro-gestao.php';
                            alert('Membro cadastrado com sucesso!'); 
                  </script>";
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

function editarMembro($id) {
    $conexao = conectaBd();

    $nome = filter_input(INPUT_POST, 'mem_nome');
    $cpf = filter_input(INPUT_POST, 'mem_cpf');
    $telefone = filter_input(INPUT_POST, 'mem_telefone');
    $email = filter_input(INPUT_POST, 'mem_email');
    $senha = filter_input(INPUT_POST, 'mem_senha');
    $dataInscricao = filter_input(INPUT_POST, 'mem_dataInscricao');
    $status = filter_input(INPUT_POST, 'mem_status');
    $plano = filter_input(INPUT_POST, 'fk_plan', FILTER_SANITIZE_NUMBER_INT);
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    if ($id > 0) {
        $sql = "UPDATE membro SET mem_nome=:mem_nome, mem_cpf=:mem_cpf, mem_telefone=:mem_telefone, mem_email=:mem_email, mem_senha=:mem_senha, 
                                  mem_dataInscricao=:mem_dataInscricao, mem_status=:mem_status, fk_plan=:fk_plan WHERE pk_mem=:pk_mem";
        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
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
            echo "<script> window.location.href = '../gestao/membro-gestao.php';
                            alert('Membro alterado com sucesso!'); 
                  </script>";
            exit();
        } catch (PDOException $e) {
            echo "<script> window.location.href = 'template/gestao/membro-gestao.php?erro= " . urlencode($e->getMessage()) . "';
                            alert('Erro ao realizar alteração!'); 
                  </script>";
            exit();
        }
    } else {
        echo "<script> window.location.href = 'template/gestao/membro-gestao.php';
                       alert('ID Inválido!'); 
              </script>";
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
            echo "<script> window.location.href = 'template/gestao/livro-gestao.php';
                            alert('Livro cadastrado com sucesso!'); 
                  </script>";
            exit();
        } catch (PDOException $e) {
            header("Location: template/cadastro/livro-cadastro.php?erro=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: template/cadastro/livro-cadastro.php");
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