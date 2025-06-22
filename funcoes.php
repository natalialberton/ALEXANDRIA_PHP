<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//FUNÇÃO PARA CONEXÃO COM BANCO DE DADOS
function conectaBd() {
    require_once "conexao.php";
    $conexao = conectarBanco();
    return $conexao;
}

//FUNÇÃO ENVIAR MSG DE ALERTA PARA O JS (SWEET ALERT)
function enviarSweetAlert($local, $tipoMensagem, $mensagem) {
    echo "<script>
            window.location.href = '$local';
            sessionStorage.setItem('$tipoMensagem', '$mensagem');
         </script>";

    exit();
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
            enviarSweetAlert('index.php', 'erroAlerta', 'Senha ou Usuário incorretos!');
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
function retornoPesquisa($termoBusca, $tabela, $id, $string1, $string2) {
    $conexao = conectaBd();

    $termoBusca = '%' . $termoBusca . '%';
    $sql = "SELECT * FROM $tabela WHERE $id = :termoBusca
            OR $string1 LIKE :termoBusca
            OR $string2 LIKE :termoBusca
            ORDER BY 
                $id LIKE :termoBusca DESC,
                $string1 LIKE :termoBusca DESC,
                $string2 LIKE :termoBusca DESC";
            
    $stmt = $conexao-> prepare($sql);
    $stmt-> bindParam(":termoBusca", $termoBusca);
    $stmt-> execute();
    $membros = $stmt-> fetchAll(PDO::FETCH_ASSOC);
    return $membros;
}

//--------------------------------------------------- FUNÇÕES CRUD MEMBRO ---------------------------------------------------
function crudMembro($acao, $id) {
    $conexao = conectaBd();

    if($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
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
        $plano = $stmtPlano-> fetch(PDO::FETCH_ASSOC);
        
        if ($plano) {
            $fk_plan = $plano['pk_plan'];

        //CADASTRAMENTO
            if($acao == 1) {
                $msgSucesso = "Membro cadastrado com sucesso!";
                $sql = "INSERT INTO membro (mem_nome, mem_cpf, mem_telefone, mem_email, mem_senha, mem_dataInscricao, mem_status, fk_plan) 
                        VALUES (:nome, :cpf, :telefone, :email, :senhaHash, :dataInscricao, :status, :fk_plan)";
                $stmt = $conexao->prepare($sql);

        //ALTERAÇÃO
            } elseif($acao == 2 && $id > 0) {
                $stmtMembroAtual = $conexao-> prepare("SELECT * FROM membro WHERE pk_mem = :id");
                $stmtMembroAtual-> bindParam(":id", $id, PDO::PARAM_STR);
                $stmtMembroAtual-> execute();
                $membroAtual = $stmtMembroAtual-> fetch(PDO::FETCH_ASSOC);

                $msgSucesso = "Membro alterado com sucesso!";
                $sql = "UPDATE membro SET mem_nome=:nome, mem_cpf=:cpf, mem_telefone=:telefone, mem_email=:email, mem_senha=:senhaHash, 
                                    mem_dataInscricao=:dataInscricao, mem_status=:status, fk_plan=:fk_plan WHERE pk_mem=:pk_mem";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
            }

        //TRATAMENTO DE EXCEÇÕES
            $stmtCheckCpf = $conexao-> prepare("SELECT * FROM membro WHERE mem_cpf = :cpf");
            $stmtCheckCpf-> bindParam(":cpf", $cpf, PDO::PARAM_STR);
            $stmtCheckCpf-> execute();
            $cpfExistente = $stmtCheckCpf-> fetch(PDO::FETCH_ASSOC);

            $stmtCheckEmail = $conexao-> prepare("SELECT * FROM membro WHERE mem_email = :email");
            $stmtCheckEmail-> bindParam(":email", $email, PDO::PARAM_STR);
            $stmtCheckEmail-> execute();
            $emailExistente = $stmtCheckEmail-> fetch(PDO::FETCH_ASSOC);
                
            if($acao == 1 && $cpfExistente || $acao == 2 && $cpfExistente && $cpfExistente != htmlspecialchars($membroAtual['mem_cpf'])) {
                enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'CPF já cadastrado!');
            }

            if($emailExistente) {
                enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'Email já cadastrado!');
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
            enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'Plano não encontrado!');
        }

    //EXCLUSÃO E TRATAMENTO DE EXCEÇÕES
    } elseif($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Membro excluído com sucesso!";
            $stmtCheckEmp = $conexao->prepare("SELECT COUNT(*) FROM emprestimo WHERE fk_mem = :pk_mem");
            $stmtCheckEmp->bindParam(":pk_mem", $id, PDO::PARAM_INT);
            $stmtCheckEmp->execute();
            $emprestimos = $stmtCheckEmp->fetchColumn();

            if ($emprestimos > 0) {
                enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'Não é possível excluir um membro com empréstimos registrados!');
            }

            $sql = "DELETE FROM membro WHERE pk_mem = :pk_mem";
            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
        } catch(Exception $e) {
            echo "<script> window.location.href = 'membro-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }

    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('membro-gestao.php', 'sucessoAlerta', $msgSucesso);
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