<?php
// login.php - Arquivo para processar o login
session_start();

// Configuração do banco de dados
$host = 'localhost:3307';
$dbname = 'ALEXANDRIA';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    // Validação básica
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // Buscar usuário no banco
        $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar se usuário existe e senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            
            // Redirecionar para página principal
            header("Location: dashboard.php");
            exit();
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../static/css/login.css">
    <title>LOGIN</title>
</head>
<body>
    <main>
        <div class="LoginRight">
            <div class="img">
                <img src="img/darkmode.png" class="logoRight" alt="Descrição da Imagem">
            </div>
            <div class="formloginRight" id="formulario">
                <form class="login-container" id="login" action="login.php" method="POST">
                    <div class="top">
                        <h1>Login</h1>
                    </div>
                    
                    <?php if (isset($erro)): ?>
                        <div class="erro-msg" style="color: red; text-align: center; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="entrada">
                        <div class="input-box">
                            <img src="img/perfildarkmode.png" class="imgindex" alt="Ícone de perfil">
                            <input type="email" class="input-field" name="email" id="email" 
                                   placeholder="E-mail" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div class="input-box">
                            <img src="img/senhadarkmode.png" class="imgindex" alt="Icon de senha">
                            <input type="password" class="input-field" name="senha" id="senha" 
                                   placeholder="Senha" required>
                            <i id="toggleSenha" class="fa-solid fa-eye"></i>
                        </div>
                    </div>
                    <div class="esqueci-senha">
                        <a href="recuperarsenha.php" class="forgotpw">Esqueceu sua senha?</a>
                    </div>
                    <div class="right">
                        <button type="submit">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <script>
        const toggleSenha = document.getElementById("toggleSenha");
        const inputSenha = document.getElementById("senha");
        
        toggleSenha.addEventListener("click", function () {
            const tipo = inputSenha.type === "password" ? "text" : "password";
            inputSenha.type = tipo;
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    </script>
</body>
</html>