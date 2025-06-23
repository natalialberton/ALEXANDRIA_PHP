<?php

session_start();
require_once "../../geral.php";

$tituloPagina = "HOME";
$tituloH1 = "HOME | " . $_SESSION['user_nome'];
include '../header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alexandria - Sistema de Biblioteca</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="scripts.js"></script>

</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Dashboard </h1>
        </div>
        <div class="dashboard-grid">
            <div class="chart-container">
                <h3 class="chart-title">Empréstimos e Reservas por Mês</h3>
                <div class="chart-wrapper">
                    <div class="loading" id="loading-emprestimos">Carregando dados</div>
                    <canvas id="grafico-emprestimos-reservas" style="display: none;"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3 class="chart-title">Livros Mais Emprestados</h3>
                <div class="chart-wrapper">
                    <div class="loading" id="loading-livros">Carregando dados</div>
                    <canvas id="grafico-livros" style="display: none;"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3 class="chart-title">Categorias Mais Emprestadas</h3>
                <div class="chart-wrapper">
                    <div class="loading" id="loading-categorias">Carregando dados</div>
                    <canvas id="grafico-categorias" style="display: none;"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3 class="chart-title">Autores Mais Emprestados</h3>
                <div class="chart-wrapper">
                    <div class="loading" id="loading-autores">Carregando dados</div>
                    <canvas id="grafico-autores" style="display: none;"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <h3 class="chart-title">Multas Acumuladas por Mês</h3>
                <div class="chart-wrapper">
                    <div class="loading" id="loading-multas">Carregando dados</div>
                    <canvas id="grafico-multas" style="display: none;"></canvas>
                </div>
            </div>
        </div>
    </div>
</body>
</html>