<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

// CARREGA ARQUIVO .ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

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
        $sql = "SELECT * FROM usuario WHERE user_login = :user_login";
        $stmt = $conexao-> prepare($sql);
        $stmt-> bindParam(":user_login", $usuario);
        $stmt-> execute();
        $usuario = $stmt-> fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['user_senha'])) {
            $_SESSION['pk_user'] = $usuario['pk_user'];
            $_SESSION['user_nome'] = $usuario['user_nome'];
            $_SESSION['tipoUser'] = $usuario['user_tipoUser'];
            $_SESSION['statusUser'] = $usuario['user_status'];

            header("Location: gestao/home.php");
            exit();
        } else {
            enviarSweetAlert('index.php', 'erroAlerta', 'Senha ou Usuário incorretos!');
        }
    }
}

//FUNÇÃO PARA REALIZAR LOGOUT
function logout() {
    session_destroy();
    enviarSweetAlert('../index.php', 'sucessoAlerta', 'Até a próxima!');
}

//FUNÇÃO PARA ENVIAR EMAIL PARA MEMBRO COM EMPRÉSTIMO ATRASADO
function enviarEmailAtraso($emailDestino, $nomeMembro, $nomeLivro, $dataEmp, $dataDev) {
    $phpmailer = new PHPMailer(true);
    try {
        // Configurações do servidor SMTP (Mailtrap)
        $phpmailer->isSMTP();
        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->AuthType = 'LOGIN';
        $phpmailer->Port = 2525;
        $phpmailer->Username = $_ENV['SMTP_USERNAME'];
        $phpmailer->Password = $_ENV['SMTP_PASSWORD'];;

        // Configurações de codificação
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Encoding = 'base64';
        $phpmailer->ContentType = 'text/html; charset=UTF-8';

        // Remetente e destinatário
        $phpmailer->setFrom('no-reply@alexandria.com', 'Alexandria');
        $phpmailer->addAddress($emailDestino);

        // Conteúdo do e-mail
        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Empréstimo Atrasado';
        $phpmailer->Body    = "Olá $nomeMembro! <br><br>
                               Verificou-se que o <b>empréstimo</b> do livro <b>$nomeLivro</b>, realizado em $dataEmp e com devolução para $dataDev está <b>atrasado</b>. Por favor, regularize sua situação o mais rápido possível. <br><br>
                               Atenciosamente, <br> Equipe Alexandria";

        $phpmailer->send();
        return true;
    } catch (Exception $e) {
        echo "<script> console.log('Erro ao enviar e-mail:' . {$phpmailer->ErrorInfo}); </script>";
        exit();
    }
}

//FUNÇÕES PARA RECUPERAÇÃO E REDEFINIÇÃO DE SENHA
function enviarEmailRecuperacao($emailDestino, $token) {
    $phpmailer = new PHPMailer(true);
    try {
        // Configurações do servidor SMTP (Mailtrap)
        $phpmailer->isSMTP();
        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->AuthType = 'LOGIN';
        $phpmailer->Port = 2525;
        $phpmailer->Username = $_ENV['SMTP_USERNAME'];
        $phpmailer->Password = $_ENV['SMTP_PASSWORD'];;

        // Configurações de codificação
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Encoding = 'base64';
        $phpmailer->ContentType = 'text/html; charset=UTF-8';

        // Configurações de codificação
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Encoding = 'base64';
        $phpmailer->ContentType = 'text/html; charset=UTF-8';

        // Remetente e destinatário
        $phpmailer->setFrom('no-reply@alexandria.com', 'Alexandria');
        $phpmailer->addAddress($emailDestino);

        // Conteúdo do e-mail
        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Recuperar Senha';
        $phpmailer->Body    = "<img src=''><br><br>
                               Seu token para redefinir a senha: <br><b>$token</b>";

        $phpmailer->send();
        return true;
    } catch (Exception $e) {
        echo "<script> console.log('Erro ao enviar e-mail:' . {$phpmailer->ErrorInfo}); </script>";
        exit();
    }
}

function recuperarSenha() {
    $conexao = conectaBd();
    $email = $_POST['email'];

    $stmt = $conexao-> prepare("SELECT * FROM usuario WHERE user_email = :email");
    $stmt-> bindParam(':email', $email, PDO::PARAM_STR);
    $stmt-> execute();
    $usuario = $stmt-> fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $token = bin2hex(random_bytes(15));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $conexao-> prepare("INSERT INTO recupera_senha (fk_user, rs_token, rs_expiracao) VALUES (:fk_user, :token, :expiracao)");
        $stmt-> bindParam(":fk_user", $usuario['pk_user']);
        $stmt-> bindParam(":token", $token);
        $stmt-> bindParam(":expiracao", $expiracao);
        $stmt-> execute();

        enviarEmailRecuperacao($email, $token);
        enviarSweetAlert('confirmar-recuperacao.php', 'sucessoAlerta', 'O e-mail com código de recuperação foi enviado para ' . $email . '!');
    } else {
        enviarSweetAlert('recuperar-senha.php', 'erroAlerta', 'E-mail não registrado no sistema!');
    }
}

function verificarToken() {
    $conexao = conectaBd();
    $token = $_POST['token'] ?? '';

    if (!empty($token)) {
        $stmt = $conexao-> prepare("SELECT * FROM recupera_senha WHERE rs_token = :token AND rs_usado = 0");
        $stmt-> bindParam(":token", $token);
        $stmt-> execute();
        $recuperacao = $stmt-> fetch(PDO::FETCH_ASSOC);

        $_SESSION['recuperacaoSenhaId'] = $recuperacao['pk_rs'];

        if (!$recuperacao || strtotime($recuperacao['rs_expiracao']) < time()) {
            enviarSweetAlert('confirmar-recuperacao.php', 'erroAlerta', 'Código de recuperação inválido ou expirado!');
        } else {
            enviarSweetAlert('redefinir-senha.php', 'sucessoAlerta', 'Tudo certo! Redefina sua senha, e não esqueça novamente!');
        }
    }
}

function redefinirSenha() {
    $conexao = conectaBd();

    $idToken = $_SESSION['recuperacaoSenhaId'];
    $novaSenha = validarSenha('redefinir-senha.php', $_POST['senha']);
    $confirmaSenha = $_POST['confirma_senha'];

    if ($idToken) {
        if ($novaSenha !== $confirmaSenha) {
            enviarSweetAlert('redefinir-senha.php', 'erroAlerta', 'As senhas não coincidem!');
        } else {
            $stmt = $conexao-> prepare("SELECT * FROM recupera_senha WHERE pk_rs = :id");
            $stmt-> bindParam(":id", $idToken);
            $stmt-> execute();
            $recuperacao = $stmt-> fetch(PDO::FETCH_ASSOC);

            $stmt = $conexao-> prepare("UPDATE recupera_senha SET rs_usado = 1 WHERE pk_rs = :id");
            $stmt-> bindParam(":id", $idToken);
            $stmt-> execute();

            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $conexao-> prepare("UPDATE usuario SET user_senha = :senha WHERE pk_user = :id");
            $stmt-> bindParam(":senha", $senhaHash);
            $stmt-> bindParam(":id", $recuperacao['fk_user']);

            try {
                $stmt-> execute();
                enviarSweetAlert('index.php', 'sucessoAlerta', 'Senha redefinida! Você pode fazer login em paz agora!');
            } catch (PDOException $e) {
                echo "<script> window.location.href = 'redefinir-senha.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
                exit();
            }
        }
    } else {
        enviarSweetAlert('redefinir-senha.php', 'erroAlerta', 'E-mail não registrado no sistema!');
    }
}

//FUNÇÕES AUXILIARES
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

function contarTotalCondicional($tabela, $condicao) {
    $conexao = conectaBd();
    $stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM $tabela WHERE $condicao");
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

function atualizarEstoqueLivro($idLivro, $novoEstoque) {
    $conexao = conectaBd();
    $stmt = $conexao->prepare("UPDATE livro SET liv_estoque = :estoque WHERE pk_liv = :id");
    $stmt->bindParam(':id', $idLivro, PDO::PARAM_INT);
    $stmt->bindParam(':estoque', $novoEstoque);
    return $stmt->execute();
}

function validarSenha($local, $senha) {
    if(strlen($senha) < 8) {
        enviarSweetAlert($local, 'erroAlerta', 'Sua senha deve conter no mínimo 8 caracteres!');
    } elseif(!preg_match("#[0-9]+#", $senha)) {
        enviarSweetAlert($local, 'erroAlerta', 'Sua senha deve conter no mínimo 1 número!');
    } elseif(!preg_match("#[A-Z]+#", $senha)) {
        enviarSweetAlert($local, 'erroAlerta', 'Sua senha deve conter no mínimo 1 letra maiúscula!');
    } elseif(!preg_match("#[a-z]+#", $senha)) {
        enviarSweetAlert($local, 'erroAlerta', 'Sua senha deve conter no mínimo 1 letra minúscula!');
    } else {
        return $senha;
    }
}

//FUNÇÃO PARA PESQUISAR DADOS
function retornoPesquisa($termoBusca, $tabela, $id, $string1, $string2) {
    $conexao = conectaBd();

    if(strlen($termoBusca) === 1) {
        $termoBusca = $termoBusca . '%';
    } else {
        $termoBusca = '%' . $termoBusca . '%';
    }

    if(!empty($string2)) {
        $sql = "SELECT * FROM $tabela WHERE $id LIKE :termoBusca
                OR $string1 LIKE :termoBusca
                OR $string2 LIKE :termoBusca
                ORDER BY 
                    $id LIKE :termoBusca DESC,
                    $string1 LIKE :termoBusca DESC,
                    $string2 LIKE :termoBusca DESC";
    } else {
        $sql = "SELECT * FROM $tabela WHERE $id LIKE :termoBusca
                OR $string1 LIKE :termoBusca
                ORDER BY 
                    $id LIKE :termoBusca DESC,
                    $string1 LIKE :termoBusca DESC";
    }
            
    $stmt = $conexao-> prepare($sql);
    $stmt-> bindParam(":termoBusca", $termoBusca);
    $stmt-> execute();
    $dados = $stmt-> fetchAll(PDO::FETCH_ASSOC);
    return $dados;
}

//--------------------------------------------------- CRUD MEMBRO ---------------------------------------------------
function crudMembro($acao, $id) {
    $conexao = conectaBd();

    if($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $nome = filter_input(INPUT_POST, 'mem_nome');
        $cpf = filter_input(INPUT_POST, 'mem_cpf');
        $telefone = filter_input(INPUT_POST, 'mem_telefone');
        $email = filter_input(INPUT_POST, 'mem_email');
        $senha = filter_input(INPUT_POST, 'mem_senha');
        $status = filter_input(INPUT_POST, 'mem_status') ?? 'Ativo';
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckCpf = $conexao-> prepare("SELECT mem_cpf FROM membro WHERE mem_cpf = :cpf");
        $stmtCheckCpf-> bindParam(":cpf", $cpf, PDO::PARAM_STR);
        $stmtCheckCpf-> execute();
        $cpfExistente = $stmtCheckCpf-> fetchColumn();

        $stmtCheckEmail = $conexao-> prepare("SELECT mem_email FROM membro WHERE mem_email = :email");
        $stmtCheckEmail-> bindParam(":email", $email, PDO::PARAM_STR);
        $stmtCheckEmail-> execute();
        $emailExistente = $stmtCheckEmail-> fetchColumn();

        if ($acao === 1 && $cpfExistente !== false) {
            enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'CPF já cadastrado!');
            exit();
        }

        if ($acao === 1 && $emailExistente !== false) {
            enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'Email já cadastrado!');
            exit();
        }

        if ($acao === 2) {
            $membroAtual = selecionarPorId('membro', $id, 'pk_mem');
            $cpfAtual = $membroAtual['mem_cpf'];
            $emailAtual = $membroAtual['mem_email'];
            
            if ($cpfExistente !== false && $cpfExistente !== $cpfAtual) {
                enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'CPF já cadastrado!');
                exit();
            }

            if ($emailExistente !== false && $emailExistente !== $emailAtual) {
                enviarSweetAlert('membro-gestao.php', 'erroAlerta', 'Email já cadastrado!');
                exit();
            }
        }

    //CADASTRAMENTO
        if($acao == 1) {
            $msgSucesso = "Membro cadastrado com sucesso!";
            $sql = "INSERT INTO membro (mem_nome, mem_cpf, mem_telefone, mem_email, mem_senha, mem_status) 
                    VALUES (:nome, :cpf, :telefone, :email, :senhaHash, :status)";
            $stmt = $conexao->prepare($sql);

    //ALTERAÇÃO
        } elseif($acao == 2 && $id > 0) {
            $msgSucesso = "Membro alterado com sucesso!";
            $sql = "UPDATE membro SET mem_nome=:nome, mem_cpf=:cpf, mem_telefone=:telefone, mem_email=:email, mem_senha=:senhaHash, 
                    mem_status=:status WHERE pk_mem=:pk_mem";
            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":pk_mem", $id, PDO::PARAM_INT);
        }
    
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senhaHash', $senhaHash);
        $stmt->bindParam(':status', $status);

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



//--------------------------------------------------- CRUD FORNECEDOR ---------------------------------------------------
function crudFornecedor($acao, $id) {
    $conexao = conectaBd();

    if($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $nome = filter_input(INPUT_POST, 'forn_nome');
        $cnpj = filter_input(INPUT_POST, 'forn_cnpj');
        $telefone = filter_input(INPUT_POST, 'forn_telefone');
        $email = filter_input(INPUT_POST, 'forn_email');
        $endereco = filter_input(INPUT_POST, 'forn_endereco');

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckCnpj = $conexao-> prepare("SELECT forn_cnpj FROM fornecedor WHERE forn_cnpj = :cnpj");
        $stmtCheckCnpj-> bindParam(":cnpj", $cnpj, PDO::PARAM_STR);
        $stmtCheckCnpj-> execute();
        $cnpjExistente = $stmtCheckCnpj-> fetchColumn();

        $stmtCheckEmail = $conexao-> prepare("SELECT forn_email FROM fornecedor WHERE forn_email = :email");
        $stmtCheckEmail-> bindParam(":email", $email, PDO::PARAM_STR);
        $stmtCheckEmail-> execute();
        $emailExistente = $stmtCheckEmail-> fetchColumn();

        if ($acao === 1 && $cnpjExistente !== false) {
            enviarSweetAlert('fornecedor-gestao.php', 'erroAlerta', 'CNPJ já cadastrado!');
            exit();
        }

        if ($acao === 1 && $emailExistente !== false) {
            enviarSweetAlert('fornecedor-gestao.php', 'erroAlerta', 'Email já cadastrado!');
            exit();
        }

        if ($acao === 2) {
            $fornecedorAtual = selecionarPorId('fornecedor', $id, 'pk_forn');
            $cnpjAtual = $fornecedorAtual['forn_cnpj'];
            $emailAtual = $fornecedorAtual['forn_email'];
            
            if ($cnpjExistente !== false && $cnpjExistente !== $cnpjAtual) {
                enviarSweetAlert('fornecedor-gestao.php', 'erroAlerta', 'CNPJ já cadastrado!');
                exit();
            }

            if ($emailExistente !== false && $emailExistente !== $emailAtual) {
                enviarSweetAlert('fornecedor-gestao.php', 'erroAlerta', 'Email já cadastrado!');
                exit();
            }
        }

    //CADASTRAMENTO
        if($acao == 1) {
            $msgSucesso = "Fornecedor cadastrado com sucesso!";
            $sql = "INSERT INTO fornecedor (forn_nome, forn_cnpj, forn_telefone, forn_email, forn_endereco) 
                    VALUES (:nome, :cnpj, :telefone, :email, :endereco)";
            $stmt = $conexao->prepare($sql);

        //ALTERAÇÃO
            } elseif($acao == 2 && $id > 0) {
                $msgSucesso = "Fornecedor alterado com sucesso!";
                $sql = "UPDATE fornecedor SET forn_nome=:nome, forn_cnpj=:cnpj, forn_telefone=:telefone, forn_email=:email,
                        forn_endereco=:endereco WHERE pk_forn=:pk_forn";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_forn", $id, PDO::PARAM_INT);
            }
    
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cnpj', $cnpj);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':endereco', $endereco);

    //EXCLUSÃO E TRATAMENTO DE EXCEÇÕES
    } elseif($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Fornecedor excluído com sucesso!";
            $stmtCheckRemessa = $conexao->prepare("SELECT COUNT(*) FROM remessa WHERE fk_forn = :pk_forn");
            $stmtCheckRemessa->bindParam(":pk_forn", $id, PDO::PARAM_INT);
            $stmtCheckRemessa->execute();
            $remessas = $stmtCheckRemessa->fetchColumn();

            if ($remessas > 0) {
                enviarSweetAlert('fornecedor-gestao.php', 'erroAlerta', 'Não é possível excluir um fornecedor com remessas registradas!');
            }

            $sql = "DELETE FROM fornecedor WHERE pk_forn = :pk_forn";
            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":pk_forn", $id, PDO::PARAM_INT);
        } catch(Exception $e) {
            echo "<script> window.location.href = 'fornecedor-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }

    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('fornecedor-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'fornecedor-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}


//--------------------------------------------------- CRUD AUTOR ---------------------------------------------------
function crudAutor($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $nome = filter_input(INPUT_POST, 'aut_nome');
        $dataNascimento = filter_input(INPUT_POST, 'aut_dataNascimento');
        $categoriaNome = filter_input(INPUT_POST, 'cat_nome');

    //PUXANDO CHAVE ESTRANGEIRA
        $stmtCategoria = $conexao-> prepare("SELECT * FROM categoria WHERE cat_nome = :nome");
        $stmtCategoria-> bindParam(":nome", $categoriaNome);
        $stmtCategoria-> execute();
        $categoria = $stmtCategoria-> fetch(PDO::FETCH_ASSOC);

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckNome = $conexao-> prepare("SELECT aut_nome FROM autor WHERE aut_nome = :nome");
        $stmtCheckNome-> bindParam(":nome", $nome, PDO::PARAM_STR);
        $stmtCheckNome-> execute();
        $nomeExistente = $stmtCheckNome-> fetchColumn();

        $stmtCheckDataN = $conexao-> prepare("SELECT aut_dataNascimento FROM autor WHERE aut_dataNascimento = :dataNascimento");
        $stmtCheckDataN-> bindParam(":dataNascimento", $dataNascimento, PDO::PARAM_STR);
        $stmtCheckDataN-> execute();
        $dataNExistente = $stmtCheckDataN-> fetchColumn();

        if ($acao === 1 && $nomeExistente !== false && $dataNExistente !== false) {
            enviarSweetAlert('autor-gestao.php', 'erroAlerta', 'Autor já cadastrado!');
            exit();
        }

        if ($acao === 2) {
            $autorAtual = selecionarPorId('autor', $id, 'pk_aut');
            $nomeAtual = $autorAtual['aut_nome'];
            $dataNAtual = $autorAtual['aut_dataNascimento'];
            
            if ($nomeExistente !== false && $nomeExistente !== $nomeAtual && 
                $dataNExistente !== false && $dataNExistente !== $dataNAtual) {
                enviarSweetAlert('autor-gestao.php', 'erroAlerta', 'Autor já cadastrado!');
                exit();
            }
        }
    
        if ($categoria) {
            $fk_cat = $categoria['pk_cat'];

        //CADASTRAMENTO
            if ($acao == 1) {
                $msgSucesso = "Autor cadastrado com sucesso!";
                $sql = "INSERT INTO autor (aut_nome, aut_dataNascimento, fk_cat)
                        VALUES (:nome, :dataNascimento, :fk_cat)";
                $stmt = $conexao-> prepare($sql);
        
        //ALTERAÇÃO
            } elseif ($acao == 2 && $id > 0) {
                $msgSucesso = "Autor alterado com sucesso!";
                $sql = "UPDATE autor SET aut_nome=:nome, aut_dataNascimento=:dataNascimento, fk_cat=:fk_cat
                        WHERE pk_aut=:pk_aut";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_aut", $id, PDO::PARAM_INT);
            }

            $stmt-> bindParam(":nome", $nome);
            $stmt-> bindParam(":dataNascimento", $dataNascimento);
            $stmt-> bindParam(":fk_cat", $fk_cat, PDO::PARAM_INT);

        } else {
            enviarSweetAlert('autor-gestao.php', 'erroAlerta', 'Categoria não encontrada!');
        }
    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Autor excluído com sucesso!";
            $stmtCheckLivro = $conexao-> prepare("SELECT COUNT(*) FROM livro WHERE fk_aut=:pk_aut");
            $stmtCheckLivro-> bindParam(":pk_aut", $id, PDO::PARAM_INT);
            $stmtCheckLivro-> execute();
            $livros = $stmtCheckLivro-> fetchColumn();

            if ($livros > 0) {
                enviarSweetAlert('autor-gestao.php', 'erroAlerta', 'Não é possível excluir um autor com livros cadastrados!');
            }

            $stmt = $conexao-> prepare("DELETE FROM autor WHERE pk_aut = :pk_aut");
            $stmt-> bindParam(":pk_aut", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'autor-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('autor-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'autor-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }

}


//--------------------------------------------------- CRUD CATEGORIA ---------------------------------------------------
function crudCategoria($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $nome = filter_input(INPUT_POST, 'cat_nome');

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckNome = $conexao-> prepare("SELECT cat_nome FROM categoria WHERE cat_nome = :nome");
        $stmtCheckNome-> bindParam(":nome", $nome, PDO::PARAM_STR);
        $stmtCheckNome-> execute();
        $nomeExistente = $stmtCheckNome-> fetchColumn();

        if ($acao === 1 && $nomeExistente !== false) {
            enviarSweetAlert('categoria-gestao.php', 'erroAlerta', 'Categoria já cadastrada!');
            exit();
        }

        if ($acao === 2) {
            $categoriaAtual = selecionarPorId('categoria', $id, 'pk_cat');
            $nomeAtual = $categoriaAtual['cat_nome'];
            
            if ($nomeExistente !== false && $nomeExistente !== $nomeAtual) {
                enviarSweetAlert('categoria-gestao.php', 'erroAlerta', 'Categoria já cadastrada!');
                exit();
            }
        }
    
    //CADASTRAMENTO
        if ($acao == 1) {
            $msgSucesso = "Categoria cadastrada com sucesso!";
            $sql = "INSERT INTO categoria (cat_nome)
                    VALUES (:nome)";
            $stmt = $conexao-> prepare($sql);
        
    //ALTERAÇÃO
        } elseif ($acao == 2 && $id > 0) {
            $msgSucesso = "Categoria alterada com sucesso!";
            $sql = "UPDATE categoria SET cat_nome=:nome
                    WHERE pk_cat=:id";
            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":id", $id, PDO::PARAM_INT);
        }

        $stmt-> bindParam(":nome", $nome);

    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Categoria excluída com sucesso!";
            $stmtCheckLivro = $conexao-> prepare("SELECT COUNT(*) FROM livro WHERE fk_cat=:pk_cat");
            $stmtCheckLivro-> bindParam(":pk_cat", $id, PDO::PARAM_INT);
            $stmtCheckLivro-> execute();
            $livros = $stmtCheckLivro-> fetchColumn();

            if ($livros > 0) {
                enviarSweetAlert('categoria-gestao.php', 'erroAlerta', 'Não é possível excluir uma categoria com livros cadastrados!');
            }

            $stmt = $conexao-> prepare("DELETE FROM categoria WHERE pk_cat = :id");
            $stmt-> bindParam(":id", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'categoria-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('categoria-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'categoria-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}


//--------------------------------------------------- CRUD LIVRO ---------------------------------------------------
function crudLivro($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $titulo = filter_input(INPUT_POST, 'liv_titulo');
        $isbn = filter_input(INPUT_POST, 'liv_isbn');
        $autorNome = filter_input(INPUT_POST, 'aut_nome');
        $categoriaNome = filter_input(INPUT_POST, 'cat_nome');
        $edicao = filter_input(INPUT_POST, 'liv_edicao');
        $anoPublicacao = filter_input(INPUT_POST, 'liv_anoPublicacao');
        $dataAlteracaoEstoque = filter_input(INPUT_POST, 'liv_dataAlteracaoEstoque');
        $estoque = filter_input(INPUT_POST, 'liv_estoque');
        $idioma = filter_input(INPUT_POST, 'liv_idioma');
        $numPaginas = filter_input(INPUT_POST, 'liv_num_paginas');
        $sinopse = filter_input(INPUT_POST, 'liv_sinopse');

    //PUXANDO CHAVE ESTRANGEIRA
        $stmtCategoria = $conexao-> prepare("SELECT * FROM categoria WHERE cat_nome = :nome");
        $stmtCategoria-> bindParam(":nome", $categoriaNome);
        $stmtCategoria-> execute();
        $categoria = $stmtCategoria-> fetch(PDO::FETCH_ASSOC);

        $stmtAutor = $conexao-> prepare("SELECT * FROM autor WHERE aut_nome = :nome");
        $stmtAutor-> bindParam(":nome", $autorNome);
        $stmtAutor-> execute();
        $autor = $stmtAutor-> fetch(PDO::FETCH_ASSOC);

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckIsbn = $conexao-> prepare("SELECT liv_isbn FROM livro WHERE liv_isbn = :isbn");
        $stmtCheckIsbn-> bindParam(":isbn", $isbn, PDO::PARAM_STR);
        $stmtCheckIsbn-> execute();
        $isbnExistente = $stmtCheckIsbn-> fetchColumn();

        if ($acao === 1 && $isbnExistente !== false) {
            enviarSweetAlert('livro-gestao.php', 'erroAlerta', 'Livro já cadastrado!');
            exit();
        }

        if ($acao === 2) {
            $livroAtual = selecionarPorId('livro', $id, 'pk_liv');
            $isbnAtual = $livroAtual['liv_isbn'];
            
            if ($isbnExistente !== false && $isbnExistente !== $isbnAtual) {
                enviarSweetAlert('livro-gestao.php', 'erroAlerta', 'Livro já cadastrado!');
                exit();
            }
        }
    
        if ($categoria && $autor) {
        //CADASTRAMENTO
            if ($acao == 1) {
                $msgSucesso = "Livro cadastrado com sucesso!";
                $sql = "INSERT INTO livro(liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse,  liv_estoque,
                    liv_dataAlteracaoEstoque, liv_idioma, liv_num_paginas, fk_aut, fk_cat)
                    VALUES (:liv_titulo, :liv_isbn, :liv_edicao, :liv_anoPublicacao, :liv_sinopse, :liv_estoque, 
                    :liv_dataAlteracaoEstoque, :liv_idioma, :liv_num_paginas, :fk_aut, :fk_cat)";

                $stmt = $conexao-> prepare($sql);
            
        //ALTERAÇÃO
            } elseif ($acao == 2 && $id > 0) {
                $msgSucesso = "Livro alterado com sucesso!";
                $sql = "UPDATE livro SET liv_titulo=:liv_titulo, liv_isbn=:liv_isbn, liv_edicao=:liv_edicao, liv_anoPublicacao=:liv_anoPublicacao, liv_sinopse=:liv_sinopse, liv_estoque=:liv_estoque, 
                        liv_dataAlteracaoEstoque=:liv_dataAlteracaoEstoque, liv_idioma=:liv_idioma, liv_num_paginas=:liv_num_paginas, fk_aut=:fk_aut, fk_cat=:fk_cat WHERE pk_liv=:id";

                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":id", $id, PDO::PARAM_INT);
            }

            $stmt-> bindParam(":liv_titulo", $titulo);
            $stmt-> bindParam(":liv_isbn", $isbn);
            $stmt-> bindParam(":liv_edicao", $edicao);
            $stmt-> bindParam(":liv_anoPublicacao", $anoPublicacao);
            $stmt-> bindParam(":liv_sinopse", $sinopse);
            $stmt-> bindParam(":liv_estoque", $estoque);
            $stmt-> bindParam(":liv_dataAlteracaoEstoque", $dataAlteracaoEstoque);
            $stmt-> bindParam(":liv_idioma", $idioma);
            $stmt-> bindParam(":liv_num_paginas", $numPaginas);
            $stmt-> bindParam(":fk_aut", $autor['pk_aut'], PDO::PARAM_INT);
            $stmt-> bindParam(":fk_cat", $categoria['pk_cat'], PDO::PARAM_INT);
        } else {
            enviarSweetAlert('livro-gestao.php', 'erroAlerta', 'Categoria ou Autor não encontrados!');
        }

    //EXCLUSÃO E TRATAMENTO DE EXCEÇÕES
    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Livro excluído com sucesso!";
            $stmtCheckEmp = $conexao-> prepare("SELECT COUNT(*) FROM emprestimo WHERE fk_liv=:pk_liv");
            $stmtCheckEmp-> bindParam(":pk_liv", $id, PDO::PARAM_INT);
            $stmtCheckEmp-> execute();
            $emprestimos = $stmtCheckEmp-> fetchColumn();

            if ($emprestimos > 0) {
                enviarSweetAlert('livro-gestao.php', 'erroAlerta', 'Não é possível excluir um livro registrado em um empréstimo!');
            }

            $stmt = $conexao-> prepare("DELETE FROM livro WHERE pk_liv = :id");
            $stmt-> bindParam(":id", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'livro-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('livro-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'livro-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}


//--------------------------------------------------- CRUD FUNCIONÁRIO ---------------------------------------------------
function crudFuncionario($acao, $id) {
    $conexao = conectaBd();

    if($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $nome = filter_input(INPUT_POST, 'user_nome');
        $cpf = filter_input(INPUT_POST, 'user_cpf');
        $telefone = filter_input(INPUT_POST, 'user_telefone');
        $email = filter_input(INPUT_POST, 'user_email');
        $senha = !empty($senha) ? validarSenha('', filter_input(INPUT_POST, 'user_senha')) : '';
        $login = filter_input(INPUT_POST, 'user_login');
        $admissao = filter_input(INPUT_POST, 'user_dataAdmissao') ?? null;
        $demissao = filter_input(INPUT_POST, 'user_dataDemissao') ?? null;
        $tipoUser = filter_input(INPUT_POST, 'user_tipoUser');
        $status = filter_input(INPUT_POST, 'user_status') ?? 'Ativo';
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    //TRATAMENTO DE EXCEÇÕES
        if ($admissao === '') {
            $admissao = null;
        }

        if ($demissao === '') {
            $demissao = null;
        }

        $stmtCheckCpf = $conexao-> prepare("SELECT user_cpf FROM usuario WHERE user_cpf = :cpf");
        $stmtCheckCpf-> bindParam(":cpf", $cpf, PDO::PARAM_STR);
        $stmtCheckCpf-> execute();
        $cpfExistente = $stmtCheckCpf-> fetchColumn();

        $stmtCheckEmail = $conexao-> prepare("SELECT user_email FROM usuario WHERE user_email = :email");
        $stmtCheckEmail-> bindParam(":email", $email, PDO::PARAM_STR);
        $stmtCheckEmail-> execute();
        $emailExistente = $stmtCheckEmail-> fetchColumn();

        if ($acao === 1 && $cpfExistente !== false) {
            enviarSweetAlert('funcionario-gestao.php', 'erroAlerta', 'CPF já cadastrado!');
            exit();
        }

        if ($acao === 1 && $emailExistente !== false) {
            enviarSweetAlert('funcionario-gestao.php', 'erroAlerta', 'Email já cadastrado!');
            exit();
        }

        if ($acao === 2) {
            $usuarioAtual = selecionarPorId('usuario', $id, 'pk_user');
            $cpfAtual = $usuarioAtual['user_cpf'];
            $emailAtual = $usuarioAtual['user_email'];
            if($_SESSION['pk_user'] === $usuarioAtual['pk_user']) {
                $login = $usuarioAtual['user_login'];
                $tipoUser = $usuarioAtual['user_tipoUser'];
                $senhaHash = $usuarioAtual['user_senha'];
            }
            
            if ($cpfExistente !== false && $cpfExistente !== $cpfAtual) {
                enviarSweetAlert('funcionario-gestao.php', 'erroAlerta', 'CPF já cadastrado!');
                exit();
            }

            if ($emailExistente !== false && $emailExistente !== $emailAtual) {
                enviarSweetAlert('funcionario-gestao.php', 'erroAlerta', 'Email já cadastrado!');
                exit();
            }
        }
        
    //CADASTRAMENTO
        if($acao == 1) {
            $msgSucesso = "Funcionário cadastrado com sucesso!";
            $sql = "INSERT INTO usuario (user_nome, user_cpf, user_telefone, user_email, user_senha, user_login, user_dataAdmissao, user_dataDemissao, user_tipoUser, user_status) 
                    VALUES (:nome, :cpf, :telefone, :email, :senhaHash, :login, :admissao, :demissao, :tipoUser, :status)";
            $stmt = $conexao->prepare($sql);

    //ALTERAÇÃO
        } elseif($acao == 2 && $id > 0) {
            $msgSucesso = "Funcionário alterado com sucesso!";
            $sql = "UPDATE usuario SET user_nome=:nome, user_cpf=:cpf, user_telefone=:telefone, user_email=:email, user_senha=:senhaHash, 
                    user_login=:login, user_dataAdmissao=:admissao, user_dataDemissao=:demissao, user_tipoUser=:tipoUser, user_status=:status  WHERE pk_user=:pk_user";
            $stmt = $conexao-> prepare($sql);
            $stmt-> bindParam(":pk_user", $id, PDO::PARAM_INT);
        }
    
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senhaHash', $senhaHash);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':admissao', $admissao);
        $stmt->bindParam(':demissao', $demissao);
        $stmt->bindParam(':tipoUser', $tipoUser);
        $stmt->bindParam(':status', $status);
    } 
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('funcionario-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'funcionario-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}


//--------------------------------------------------- CRUD EMPRÉSTIMO ---------------------------------------------------
function crudEmprestimo($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $prazo = 7;
        $dataEmp = filter_input(INPUT_POST, 'emp_dataEmp');
        $dataDev = date('Y-m-d', strtotime("$dataEmp + $prazo days"));
        $dataDevReal = !empty(filter_input(INPUT_POST, 'emp_dataDevReal')) ? filter_input(INPUT_POST, 'emp_dataDevReal') : null;
        $status = filter_input(INPUT_POST, 'emp_status') ?? 'Empréstimo Ativo';
        $membroNome = filter_input(INPUT_POST, 'fk_mem');
        $livroNome = filter_input(INPUT_POST, 'fk_liv');
        $usuario = $_SESSION['pk_user'];
        $membroSenha = filter_input(INPUT_POST, 'mem_senha') ?? null;

    //PUXANDO CHAVE ESTRANGEIRA
        $stmtMembro = $conexao-> prepare("SELECT * FROM membro WHERE mem_cpf = :cpf");
        $stmtMembro-> bindParam(":cpf", $membroNome);
        $stmtMembro-> execute();
        $membro = $stmtMembro-> fetch(PDO::FETCH_ASSOC);

        $stmtLivro = $conexao-> prepare("SELECT * FROM livro WHERE liv_isbn = :isbn");
        $stmtLivro-> bindParam(":isbn", $livroNome);
        $stmtLivro-> execute();
        $livro = $stmtLivro-> fetch(PDO::FETCH_ASSOC);

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckMembro = $conexao-> prepare("SELECT pk_mul FROM multa WHERE mul_status = 'Aberta' AND fk_mem = :nome");
        $stmtCheckMembro-> bindParam(":nome", $membro['pk_mem'], PDO::PARAM_STR);
        $stmtCheckMembro-> execute();
        $membroComMulta = $stmtCheckMembro-> fetchColumn();

        if ($acao === 1 && $membroComMulta !== false) {
            enviarSweetAlert('emprestimo-gestao.php', 'erroAlerta', 'O membro selecionado tem multas pendentes!');
        }

        if($livro['liv_estoque'] = 0) {
            enviarSweetAlert('emprestimo-gestao.php', 'erroAlerta', 'O livro selecionado não está disponível!');
        }

        if(!empty($membroSenha)) {
            if(!password_verify($membroSenha, $membro['mem_senha'])) {
                enviarSweetAlert('emprestimo-gestao.php', 'erroAlerta', 'Senha incorreta!');
            }
        }
    
        if ($membro && $livro) {

        //CADASTRAMENTO
            if ($acao == 1) {
                $msgSucesso = "Empréstimo registrado com sucesso!";
                $sql = "INSERT INTO emprestimo (emp_prazo, emp_dataEmp, emp_dataDev, emp_dataDevReal, emp_status, fk_mem, fk_user, fk_liv)
                        VALUES (:prazo, :dataEmp, :dataDev, :dataDevReal, :status, :fk_mem, :fk_user, :fk_liv)";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":dataEmp", $dataEmp);

                $novoEstoque = $livro['liv_estoque'] - 1;
        
        //ALTERAÇÃO
            } elseif ($acao == 2 && $id > 0) {
                $msgSucesso = "Empréstimo alterado com sucesso!";
                $emprestimoAtual = selecionarPorId('emprestimo', $id, 'pk_emp');
                $sql = "UPDATE emprestimo SET emp_prazo=:prazo, emp_dataDev=:dataDev, emp_dataDevReal=:dataDevReal,
                        emp_status=:status, fk_mem=:fk_mem, fk_user=:fk_user, fk_liv=:fk_liv 
                        WHERE pk_emp=:pk_emp";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_emp", $id, PDO::PARAM_INT);

                $statusAnterior = $emprestimoAtual['emp_status'];
                $novoStatus = filter_input(INPUT_POST, 'emp_status') ?? 'Empréstimo Ativo';
                
                // SE MUDOU PARA FINALIZADO, AUMENTA ESTOQUE LIVRO
                if ($statusAnterior !== 'Finalizado' && $novoStatus === 'Finalizado') {
                    $novoEstoque = $livro['liv_estoque'] + 1;
                    atualizarEstoqueLivro($livro['pk_liv'], $novoEstoque);
                }
            }

            $stmt-> bindParam(":prazo", $prazo);
            $stmt-> bindParam(":dataDev", $dataDev);
            $stmt-> bindParam(":dataDevReal", $dataDevReal);
            $stmt-> bindParam(":status", $status);
            $stmt-> bindParam(":fk_mem", $membro['pk_mem'], PDO::PARAM_INT);
            $stmt-> bindParam(":fk_user", $usuario, PDO::PARAM_INT);
            $stmt-> bindParam(":fk_liv", $livro['pk_liv'], PDO::PARAM_INT);

        } else {
            enviarSweetAlert('emprestimo-gestao.php', 'erroAlerta', 'Membro ou livro não encontrados!');
        }
    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Empréstimo excluído com sucesso!";
            $emprestimo = selecionarPorId('emprestimo', $id, 'pk_emp');

            if ($emprestimo['emp_status'] === 'Empréstimo Atrasado' || 
                $emprestimo['emp_status'] === 'Empréstimo Ativo' || 
                $emprestimo['emp_status'] === 'Renovação Atrasada' || 
                $emprestimo['emp_status'] === 'Renovação Ativa') {
                enviarSweetAlert('emprestimo-gestao.php', 'erroAlerta', 'Não é possível excluir um empréstimo não finalizado!');
            }

            $stmt = $conexao-> prepare("DELETE FROM emprestimo WHERE pk_emp = :pk_emp");
            $stmt-> bindParam(":pk_emp", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'emprestimo-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        if ($stmt->execute()) {
            if ($acao == 1) {
                atualizarEstoqueLivro($livro['pk_liv'], $novoEstoque);
            }

             // SE MUDOU PARA ATRASADO, ENVIA EMAIL DE AVISO PARA O MEMBRO
            if($acao == 2) {
                $emprestimoAtual = selecionarPorId('emprestimo', $id, 'pk_emp');
                $statusAnterior = $emprestimoAtual['emp_status'];
                $novoStatus = filter_input(INPUT_POST, 'emp_status') ?? 'Empréstimo Ativo';
                $dataEmp = date('d/m/Y', strtotime($dataEmp));
                $dataDev = date('d/m/Y', strtotime($dataDev));
            
                if ($statusAnterior !== 'Empréstimo Atrasado' ||
                    $statusAnterior !== 'Renovação Atrasada' && 
                    $novoStatus === 'Empréstimo Atrasado' || 
                    $novoStatus === 'Renovação Atrasada') {
                    enviarEmailAtraso($membro['mem_email'], $membro['mem_nome'], $livro['liv_titulo'], $dataEmp, $dataDev);
                    enviarSweetAlert('emprestimo-gestao.php', 'sucessoAlerta', 'Um email avisando do atraso foi enviado para ' . $membro['mem_email'] . '!');
                }
            }
            
            enviarSweetAlert('emprestimo-gestao.php', 'sucessoAlerta', $msgSucesso);
        }
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'emprestimo-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}

//--------------------------------------------------- CRUD RESERVA ---------------------------------------------------
function crudReserva($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $prazo = 7;
        $dataMarcada = filter_input(INPUT_POST, 'res_dataMarcada');
        $dataVencimento = date('Y-m-d', strtotime("$dataMarcada + $prazo days"));
        $dataFinalizada = filter_input(INPUT_POST, 'res_dataFinalizada') ?? null;
        $status = filter_input(INPUT_POST, 'res_status');
        $membroNome = filter_input(INPUT_POST, 'fk_mem');
        $livroNome = filter_input(INPUT_POST, 'fk_liv');
        $usuario = $_SESSION['pk_user'];

    //PUXANDO CHAVE ESTRANGEIRA
        $stmtMembro = $conexao-> prepare("SELECT * FROM membro WHERE mem_cpf = :cpf");
        $stmtMembro-> bindParam(":cpf", $membroNome);
        $stmtMembro-> execute();
        $membro = $stmtMembro-> fetch(PDO::FETCH_ASSOC);

        $stmtLivro = $conexao-> prepare("SELECT * FROM livro WHERE liv_isbn = :isbn");
        $stmtLivro-> bindParam(":isbn", $livroNome);
        $stmtLivro-> execute();
        $livro = $stmtLivro-> fetch(PDO::FETCH_ASSOC);

    //TRATAMENTO DE EXCEÇÕES
        $stmtCheckMembro = $conexao-> prepare("SELECT pk_mul FROM multa WHERE mul_status = 'Aberta' AND fk_mem = :nome");
        $stmtCheckMembro-> bindParam(":nome", $membro['pk_mem'], PDO::PARAM_STR);
        $stmtCheckMembro-> execute();
        $membroComMulta = $stmtCheckMembro-> fetchColumn();

        if ($acao === 1 && $membroComMulta !== false) {
            enviarSweetAlert('reserva-gestao.php', 'erroAlerta', 'O membro selecionado tem multas pendentes!');
            exit();
        }
    
        if ($membro && $livro) {

        //CADASTRAMENTO
            if ($acao == 1) {
                $msgSucesso = "Reserva registrada com sucesso!";
                $sql = "INSERT INTO reserva (res_prazo, res_dataMarcada, res_dataVencimento, res_dataFinalizada, res_status, fk_mem, fk_user, fk_liv)
                        VALUES (:prazo, :dataMarcada, :dataVencimento, :dataFinalizada, :status, :fk_mem, :fk_user, :fk_liv)";
                $stmt = $conexao-> prepare($sql);
        
        //ALTERAÇÃO
            } elseif ($acao == 2 && $id > 0) {
                $msgSucesso = "Reserva alterada com sucesso!";
                $sql = "UPDATE reserva SET res_prazo=:prazo, res_dataMarcada=:dataMarcada, res_dataVencimento=:dataVencimento, res_dataFinalizada=:dataFinalizada,
                        res_status=:status, fk_mem=:fk_mem, fk_user=:fk_user, fk_liv=:fk_liv 
                        WHERE pk_res=:pk_res";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_res", $id, PDO::PARAM_INT);
            }

            $stmt-> bindParam(":prazo", $prazo);
            $stmt-> bindParam(":dataMarcada", $dataMarcada);
            $stmt-> bindParam(":dataVencimento", $dataVencimento);
            $stmt-> bindParam(":dataFinalizada", $dataFinalizada);
            $stmt-> bindParam(":status", $status);
            $stmt-> bindParam(":fk_mem", $membro['pk_mem'], PDO::PARAM_INT);
            $stmt-> bindParam(":fk_user", $usuario, PDO::PARAM_INT);
            $stmt-> bindParam(":fk_liv", $livro['pk_liv'], PDO::PARAM_INT);

        } else {
            enviarSweetAlert('reserva-gestao.php', 'erroAlerta', 'Membro ou livro não encontrados!');
        }
    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Reserva excluída com sucesso!";
            $reserva = selecionarPorId('reserva', $id, 'pk_res');

            if ($reserva['res_status'] === 'Aberta' || 
                $reserva['res_status'] === 'Atrasada') {
                enviarSweetAlert('reserva-gestao.php', 'erroAlerta', 'Não é possível excluir uma reserva não finalizada!');
            }

            $stmt = $conexao-> prepare("DELETE FROM reserva WHERE pk_res = :pk_res");
            $stmt-> bindParam(":pk_res", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'reserva-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('reserva-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'reserva-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}

//--------------------------------------------------- CRUD REMESSA ---------------------------------------------------
function crudRemessa($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $data = filter_input(INPUT_POST, 'rem_data');
        $qtd = filter_input(INPUT_POST, 'rem_qtd');
        $fornecedorNome = filter_input(INPUT_POST, 'fk_forn');
        $livroNome = filter_input(INPUT_POST, 'fk_liv');
        $usuario = $_SESSION['pk_user'];

    //PUXANDO CHAVE ESTRANGEIRA
        $stmtFornecedor= $conexao-> prepare("SELECT * FROM fornecedor WHERE forn_cnpj = :cnpj");
        $stmtFornecedor-> bindParam(":cnpj", $fornecedorNome);
        $stmtFornecedor-> execute();
        $fornecedor = $stmtFornecedor-> fetch(PDO::FETCH_ASSOC);

        $stmtLivro = $conexao-> prepare("SELECT * FROM livro WHERE liv_isbn = :isbn");
        $stmtLivro-> bindParam(":isbn", $livroNome);
        $stmtLivro-> execute();
        $livro = $stmtLivro-> fetch(PDO::FETCH_ASSOC);
    
        if ($fornecedor && $livro) {

        //CADASTRAMENTO
            if ($acao == 1) {
                $msgSucesso = "Remessa registrada com sucesso!";
                $sql = "INSERT INTO remessa (rem_data, rem_qtd, fk_forn, fk_user, fk_liv)
                        VALUES (:data, :qtd, :fk_forn, :fk_user, :fk_liv)";
                $stmt = $conexao-> prepare($sql);
                atualizarEstoqueLivro($livro['pk_liv'], $qtd);
        
        //ALTERAÇÃO
            } elseif ($acao == 2 && $id > 0) {
                $msgSucesso = "Remessa alterada com sucesso!";
                $sql = "UPDATE remessa SET rem_data=:data, rem_qtd=:qtd, fk_forn=:fk_forn, fk_user=:fk_user, fk_liv=:fk_liv 
                        WHERE pk_rem=:pk_rem";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_rem", $id, PDO::PARAM_INT);
            }

            $stmt-> bindParam(":data", $data);
            $stmt-> bindParam(":qtd", $qtd);
            $stmt-> bindParam(":fk_forn", $fornecedor['pk_forn'], PDO::PARAM_INT);
            $stmt-> bindParam(":fk_user", $usuario, PDO::PARAM_INT);
            $stmt-> bindParam(":fk_liv", $livro['pk_liv'], PDO::PARAM_INT);

        } else {
            enviarSweetAlert('remessa-gestao.php', 'erroAlerta', 'Forncedor ou livro não encontrados!');
        }
    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Remessa excluída com sucesso!";

            $stmt = $conexao-> prepare("DELETE FROM remessa WHERE pk_rem = :pk_rem");
            $stmt-> bindParam(":pk_rem", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'remessa-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('remessa-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'remessa-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}

//--------------------------------------------------- CRUD MULTA ---------------------------------------------------
function crudMulta($acao, $id) {
    $conexao = conectaBd();

    if ($acao == 1 || $acao == 2) {
    //PUXANDO DADOS VIA POST
        $valor = filter_input(INPUT_POST, 'mul_valor');
        $qtdDias = filter_input(INPUT_POST, 'mul_qtdDias');
        $status = filter_input(INPUT_POST, 'mul_status') ?? 'Aberta';
        $membroNome = filter_input(INPUT_POST, 'fk_mem');
        $idEmp = filter_input(INPUT_POST, 'fk_emp');

    //PUXANDO CHAVE ESTRANGEIRA
        $stmtMembro = $conexao-> prepare("SELECT * FROM membro WHERE mem_cpf = :cpf");
        $stmtMembro-> bindParam(":cpf", $membroNome);
        $stmtMembro-> execute();
        $membro = $stmtMembro-> fetch(PDO::FETCH_ASSOC);

    //TRATAMENTO DE EXCEÇÕES
        $stmtEmp = $conexao-> prepare("SELECT * FROM emprestimo WHERE pk_emp = :id");
        $stmtEmp-> bindParam(":id", $idEmp);
        $stmtEmp-> execute();
        $emprestimo = $stmtEmp-> fetch(PDO::FETCH_ASSOC);

        $stmtMul = $conexao-> prepare("SELECT * FROM multa WHERE fk_emp = :id");
        $stmtMul-> bindParam(":id", $idEmp);
        $stmtMul-> execute();
        $multa = $stmtMul-> fetch(PDO::FETCH_ASSOC);

        if ($acao == 1 && $emprestimo['emp_status'] !== 'Empréstimo Atrasado') {
            enviarSweetAlert('multa-gestao.php', 'erroAlerta', 'Não é possível registrar uma multa para um empréstimo que não esteja atrasado!');
        }

        if($acao == 1 && $multa) {
            enviarSweetAlert('multa-gestao.php', 'erroAlerta', 'O empréstimo selecionado já tem multa registrada!');
        }
    
        if ($membro) {

        //CADASTRAMENTO
            if ($acao == 1) {
                $msgSucesso = "Multa registrada com sucesso!";
                $sql = "INSERT INTO multa (mul_valor, mul_qtdDias, mul_status, fk_mem, fk_emp)
                        VALUES (:valor, :qtdDias, :status, :fk_mem, :fk_emp)";
                $stmt = $conexao-> prepare($sql);
        
        //ALTERAÇÃO
            } elseif ($acao == 2 && $id > 0) {
                $msgSucesso = "Multa alterada com sucesso!";
                $sql = "UPDATE multa SET mul_valor=:valor, mul_qtdDias=:qtdDias, mul_status=:status, fk_mem=:fk_mem, fk_emp=:fk_emp 
                        WHERE pk_mul=:pk_mul";
                $stmt = $conexao-> prepare($sql);
                $stmt-> bindParam(":pk_mul", $id, PDO::PARAM_INT);
            }

            $stmt-> bindParam(":valor", $valor);
            $stmt-> bindParam(":qtdDias", $qtdDias);
            $stmt-> bindParam(":status", $status);
            $stmt-> bindParam(":fk_mem", $membro['pk_mem'], PDO::PARAM_INT);
            $stmt-> bindParam(":fk_emp", $idEmp, PDO::PARAM_INT);

        } else {
            enviarSweetAlert('multa-gestao.php', 'erroAlerta', 'Membro não encontrado!');
        }
    } elseif ($acao == 3 && $id > 0) {
        try {
            $msgSucesso = "Multa excluída com sucesso!";
            $multa = selecionarPorId('multa', $id, 'pk_mul');

            if ($multa['mul_status'] === 'Aberta') {
                enviarSweetAlert('multa-gestao.php', 'erroAlerta', 'Multas em aberto não podem ser excluídas!');
            }

            $stmt = $conexao-> prepare("DELETE FROM multa WHERE pk_mul = :pk_mul");
            $stmt-> bindParam(":pk_mul", $id, PDO::PARAM_INT);
        } catch (Exception $e) {
            echo "<script> window.location.href = 'multa-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
            exit();
        }
    }
    //EXECUÇÃO DO COMANDO NO BANCO DE DADOS
    try {
        $stmt-> execute();
        enviarSweetAlert('multa-gestao.php', 'sucessoAlerta', $msgSucesso);
    } catch (PDOException $e) {
        echo "<script> window.location.href = 'multa-gestao.php?erro= ". urlencode($e->getMessage()) . "'; </script>";
        exit();
    }
}

?>