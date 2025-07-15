<?php

session_start();
require_once "../../geral.php";

$tituloPagina = "DASHBOARD";
$tituloH1 = "Dashboard";
include '../header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alexandria - Sistema de Biblioteca</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script src="../../static/javascript/scripts.js"></script>

</head>

<body>
    <div class="container">
        <div class="header">
            <h1> Dashboard </h1>
        </div>
        <div class="dashboard-grid">
            <div class="filtro-dashboard">
                <label for="filtro-mes">Mês:</label>
                <select id="filtro-mes">
                    <option value="">Todos</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>

                <label for="filtro-ano">Ano:</label>
                <select id="filtro-ano">
                    <?php
                    $anoAtual = date('Y');
                    for ($i = $anoAtual; $i >= 2023; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>

                <button onclick="aplicarFiltro()">Filtrar</button>
                <button onclick="limparFiltro()">Limpar</button>

            </div>
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