<?php

session_start();
require_once '../geral.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../static/javascript/geral.js?v=<?= time() ?>"></script>
    <title>LOGIN</title>
</head>

<body>
<head>
    <link id="favicon" rel="icon" type="image/png" href="../../static/img/favicon-dark.png">
        <link id="favicon" rel="icon" type="image/png" href="../../static/img/favicon-light.png">
    </head>
    <!-- resto do site -->

    <script>
        function setFavicon(theme) {
            const favicon = document.getElementById('favicon');
            if (theme === 'dark') {
                favicon.href = '../../static/img/favicon-dark.png';
            } else {
                favicon.href = '../../static/img/favicon-light.png';
            }
        }

        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            setFavicon('dark');
        } else {
            setFavicon('light');
        }

        // Para detectar mudanças no tema em tempo real (ex: usuário muda o tema do SO):
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            setFavicon(e.matches ? 'dark' : 'light');
        });
    </script>
    <main>
        <div class="LoginRight">
            <div class="img">
                <img src="../static/img/darkmode.png" class="logoRight" alt="Descrição da Imagem">
            </div>
            <div class="formloginRight" id="formulario">
                <form method="POST" action="index.php" class="login-container" id="login">
                    <div class="top">
                        <h1>Login</h1>
                    </div>

                    <div class="entrada">
                        <div class="input-box">
                            <i class="fas fa-user"></i>
                            <input type="text" class="input-field" name="usuario" id="usuario" placeholder="Usuário" required>
                        </div>
                        <div class="input-box">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="input-field" name="senha" id="senha" placeholder="Senha" required>
                            <i id="toggleSenha" class="fa-solid fa-eye"></i>
                        </div>
                    </div>
                    <div class="esqueci-senha">
                        <a href="recuperar-senha.php" class="forgotpw">Esqueceu sua senha?</a>
                    </div>
                    <div class="right">
                        <button type="submit" id="login_btn">Entrar</button>
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