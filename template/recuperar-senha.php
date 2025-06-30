<?php

session_start();
require_once '../geral.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    recuperarSenha();
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
    <title>RECUPERAÇÃO SENHA</title>
</head>
<body>
<head>
    <link id="favicon" rel="icon" type="image/png" href="../../static/img/favicon-dark.png">
        <link id="favicon" rel="icon" type="image/png" href="../../static/img/favicon-light.png">
    </head>
    
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
        <div class="loginRight" id="LoginLeft">
            <div class="img">
                <img src="../static/img/darkmode.png" class="logoRight" id="LogoLeft" alt="Descrição da Imagem">
            </div>
            <div class="formloginRight" id="formLoginLeft">
                <form class="login-container" id="login"  method="POST">
                    <div class="top">
                        <h1>Recuperar sua Senha</h1>
                    </div>
                    <div class="entrada">
                        <div class="input-box">
                            <i class="fas fa-user"></i>
                            <input type="text" class="input-field" name="email" id="email" placeholder="E-mail">
                        </div>
                        <div class="right" id="left">
                            <button type="submit">Enviar Código</button>
                          <button type="button" class="cancel" onclick="location.href='index.php'">Cancelar</button>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>