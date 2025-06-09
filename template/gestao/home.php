<?php

require_once ('../funcoes.php');

if(isset($_SESSION['pk_user'])) {
    $id = $_SESSION['pk_user'];
}
verificarLogin('home', $id);

//PUXANDO O HEADER, NAV E DEFININDO VARIÁVEIS 
session_start();
$tituloPagina = "HOME";
$tituloH1= "Bem-vindo, " . $_SESSION['user_nome'];
include '../header.php';

?>