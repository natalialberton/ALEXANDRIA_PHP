<?php

session_start();
require_once '../geral.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redefinirSenha();
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
    <title>REDEFINIR SENHA</title>
</head>
<body>
    <main>
        <div class="loginRight" id="LoginLeft">
            <div class="img">
                <img src="../static/img/darkmode.png" class="logoRight" id="LogoLeft" alt="Descrição da Imagem">
            </div>
            <div class="formloginRight" id="formLoginLeft">
                <form class="login-container" id="login" method="POST">
                    <div class="top">
                        <h1>Redefinir sua Senha</h1>
                    </div>
                    <div class="entrada">
                        <div class="input-box">
                            <i class="fas fa-user"></i>
                            <input type="text" class="input-field" name="email" id="email" placeholder="E-mail">
                        </div>
                        <div class="input-box">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="input-field senhaLeft" name="senha" id="senha" placeholder="Nova Senha">
                            <i id="toggleSenha" class="fa-solid fa-eye"></i>
                        </div>
                        <div class="input-box">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="input-field senhaLeft" name="confirma_senha" id="confirmaSenha" placeholder="Confirme sua senha">
                            <i id="toggleConfirmaSenha" class="fa-solid fa-eye"></i>
                        </div>
                        <div class="right" id="left">
                            <button type="submit">Redefinir senha</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
       
        function togglePasswordVisibility(toggleId, inputId) {
            const toggleSenha = document.getElementById(toggleId);
            const inputSenha = document.getElementById(inputId);

            toggleSenha.addEventListener("click", function () {
                const tipo = inputSenha.type === "password" ? "text" : "password";
                inputSenha.type = tipo;
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            });
        }

    
        togglePasswordVisibility("toggleSenha", "senha");
        togglePasswordVisibility("toggleConfirmaSenha", "confirmaSenha");
    </script>
</body>
</html>