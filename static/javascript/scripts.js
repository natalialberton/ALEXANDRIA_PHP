// Configurações globais do Chart.js
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.color = '#fff';

// Função para fazer requisições AJAX
async function fetchData(tipo) {
    try {
        const response = await fetch(`dados_grafico.php?tipo=${tipo}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Erro ao carregar dados de ${tipo}:`, error);
        throw error;
    }
}

// Função para esconder loading e mostrar gráfico
function showChart(loadingId, canvasId) {
    document.getElementById(loadingId).style.display = 'none';
    document.getElementById(canvasId).style.display = 'block';
}

// Função para mostrar erro
function showError(loadingId, message) {
    const loadingElement = document.getElementById(loadingId);
    loadingElement.innerHTML = `<div class="error">❌ Erro: ${message}</div>`;
}

// Paleta de cores
const colors = {
    primary: '#667eea',
    secondary: '#764ba2',
    success: '#00b894',
    danger: '#e17055',
    warning: '#fdcb6e',
    info: '#74b9ff',
    purple: '#a29bfe',
    pink: '#fd79a8',
    orange: '#e17055',
    teal: '#00cec9'
};

// Gráfico de Empréstimos e Reservas por Mês
async function criarGraficoEmprestimosReservas() {
    try {
        const dados = await fetchData('emprestimos_reservas_mes');
        const labels = dados.map(item => {
            const [ano, mes] = item.mes.split('-');
            const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 
                          'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            return `${meses[parseInt(mes) - 1]}/${ano}`;
        });
        const emprestimos = dados.map(item => parseInt(item.emprestimos));
        const reservas = dados.map(item => parseInt(item.reservas));

        showChart('loading-emprestimos', 'grafico-emprestimos-reservas');

        const ctx = document.getElementById('grafico-emprestimos-reservas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Empréstimos',
                    data: emprestimos,
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Reservas',
                    data: reservas,
                    borderColor: colors.secondary,
                    backgroundColor: colors.secondary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { display: true, title: { display: true, text: 'Mês/Ano' } },
                    y: { display: true, title: { display: true, text: 'Quantidade' }, beginAtZero: true }
                }
            }
        });
    } catch (error) {
        showError('loading-emprestimos', 'Não foi possível carregar os dados de empréstimos e reservas');
    }
}

// Gráfico de Livros Mais Emprestados
async function criarGraficoLivros() {
    try {
        const dados = await fetchData('livros_mais_emprestados');
        const labels = dados.map(item => item.titulo.length > 30 ? item.titulo.substring(0, 30) + '...' : item.titulo);
        const valores = dados.map(item => parseInt(item.total_emprestimos));

        showChart('loading-livros', 'grafico-livros');

        const ctx = document.getElementById('grafico-livros').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Empréstimos',
                    data: valores,
                    backgroundColor: [
                        colors.primary, colors.secondary, colors.success, 
                        colors.danger, colors.warning, colors.info,
                        colors.purple, colors.pink, colors.orange, colors.teal
                    ],
                    borderColor: [
                        colors.primary, colors.secondary, colors.success, 
                        colors.danger, colors.warning, colors.info,
                        colors.purple, colors.pink, colors.orange, colors.teal
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { ticks: { maxRotation: 45, minRotation: 45 } },
                    y: { beginAtZero: true, title: { display: true, text: 'Número de Empréstimos' } }
                }
            }
        });
    } catch (error) {
        showError('loading-livros', 'Não foi possível carregar os dados dos livros');
    }
}

// Gráfico de Categorias Mais Emprestadas
async function criarGraficoCategorias() {
    try {
        const dados = await fetchData('categorias_mais_emprestadas');
        const labels = dados.map(item => item.categoria);
        const valores = dados.map(item => parseInt(item.total_emprestimos));

        showChart('loading-categorias', 'grafico-categorias');

        const ctx = document.getElementById('grafico-categorias').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: [
                        colors.primary, colors.secondary, colors.success, 
                        colors.danger, colors.warning, colors.info,
                        colors.purple, colors.pink
                    ],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        showError('loading-categorias', 'Não foi possível carregar os dados das categorias');
    }
}

// Gráfico de Autores Mais Emprestados
async function criarGraficoAutores() {
    try {
        const dados = await fetchData('autores_mais_emprestados');
        const labels = dados.map(item => item.autor);
        const valores = dados.map(item => parseInt(item.total_emprestimos));

        showChart('loading-autores', 'grafico-autores');

        const ctx = document.getElementById('grafico-autores').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Empréstimos',
                    data: valores,
                    backgroundColor: colors.success,
                    borderColor: colors.success,
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, title: { display: true, text: 'Número de Empréstimos' } }
                }
            }
        });
    } catch (error) {
        showError('loading-autores', 'Não foi possível carregar os dados dos autores');
    }
}

// Gráfico de Multas por Mês
async function criarGraficoMultas() {
    try {
        const dados = await fetchData('multas_mes');
        const labels = dados.map(item => {
            const [ano, mes] = item.mes.split('-');
            const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 
                          'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            return `${meses[parseInt(mes) - 1]}/${ano}`;
        });
        const valores = dados.map(item => parseFloat(item.total_multas));

        showChart('loading-multas', 'grafico-multas');

        const ctx = document.getElementById('grafico-multas').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valor das Multas (R$)',
                    data: valores,
                    backgroundColor: colors.danger + '80',
                    borderColor: colors.danger,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `R$ ${context.parsed.y.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Valor (R$)' }, ticks: { callback: value => 'R$ ' + value.toFixed(2) } },
                    x: { title: { display: true, text: 'Mês/Ano' } }
                }
            }
        });
    } catch (error) {
        showError('loading-multas', 'Não foi possível carregar os dados das multas');
    }
}

// Inicializar todos os gráficos
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Alexandria carregado');
    criarGraficoEmprestimosReservas();
    criarGraficoLivros();
    criarGraficoCategorias();
    criarGraficoAutores();
    criarGraficoMultas();
});

// Função para recarregar
function recarregarDashboard() {
    location.reload();
}