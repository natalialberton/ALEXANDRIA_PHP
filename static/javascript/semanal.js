// semanal.js com gráficos em colunas aprimorados

// Use relative path since API and home are in same folder
const API_URL = './api_dados.php';

document.addEventListener('DOMContentLoaded', () => {

  const inputData = document.getElementById('dataSelecionada');
  const ctxGraficoDiario = document.getElementById('graficoDiario')?.getContext('2d');
  const ctxGraficoSemanal = document.getElementById('graficoSemanal')?.getContext('2d');
  const ctxEmprestimosChart = document.getElementById('emprestimosChart')?.getContext('2d');
  const ctxReservasChart = document.getElementById('reservasChart')?.getContext('2d');

  let graficoDiarioInstance = null;
  let graficoSemanalInstance = null;
  let emprestimosChartInstance = null;
  let reservasChartInstance = null;

  let dadosDiariosGlobal = [];
  let dadosSemanaisGlobal = [];
  let dadosEmprestimosStatus = [];
  let dadosReservasStatus = [];
  let totaisGlobal = {};

  // Configuração padrão para gráficos de coluna
  const colunaConfig = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false,
        position: 'top',
        labels: {
          usePointStyle: false,
          padding: 20,
          font: {
            size: 12
          }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: 'white',
        bodyColor: 'white',
        borderColor: 'rgba(255, 255, 255, 0.2)',
        borderWidth: 1,
        cornerRadius: 6,
        displayColors: true
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: 'rgba(0, 0, 0, 0.1)',
          lineWidth: 1
        },
        ticks: {
          stepSize: 1,
          font: {
            size: 11
          }
        }
      },
      x: {
        grid: {
          display: false
        },
        ticks: {
          font: {
            size: 11
          }
        }
      }
    },
    elements: {
      bar: {
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false
      }
    }
  };

  // Função para buscar dados da API
  async function carregarDados() {
    try {
      console.log('Carregando dados da API...');
      const response = await fetch(API_URL);
      
      console.log('Status da resposta:', response.status);
      console.log('Headers da resposta:', response.headers);
      
      if (!response.ok) {
        throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
      }

      const contentType = response.headers.get('content-type');
      console.log('Content-Type:', contentType);

      if (!contentType || !contentType.includes('application/json')) {
        const textResponse = await response.text();
        console.error('Resposta não é JSON:', textResponse);
        throw new Error('Resposta da API não é JSON válido');
      }

      const resultado = await response.json();
      console.log('Dados recebidos:', resultado);
      
      // Verificar se a resposta foi bem-sucedida
      if (!resultado.success) {
        throw new Error(resultado.error || 'Erro desconhecido na API');
      }

      // Verificar se os dados existem
      if (!resultado.data) {
        throw new Error('Dados não encontrados na resposta da API');
      }

      // Mapear os dados conforme retornados pela API PHP
      const dados = resultado.data;
      
      dadosDiariosGlobal = dados.diario || [];
      dadosSemanaisGlobal = dados.emprestimos_por_semana || [];
      dadosEmprestimosStatus = dados.emprestimos_por_status || [];
      dadosReservasStatus = dados.reservas_por_status || [];
      totaisGlobal = dados.totais || {};

      console.log('Dados processados:', {
        dadosDiariosGlobal: dadosDiariosGlobal.length,
        dadosSemanaisGlobal: dadosSemanaisGlobal.length,
        dadosEmprestimosStatus: dadosEmprestimosStatus.length,
        dadosReservasStatus: dadosReservasStatus.length,
        totaisGlobal
      });

      // Atualizar a interface
      atualizarCards();
      criarGraficoEmprestimos();
      criarGraficoReservas();
      criarGraficoSemanal();

      const dataInicial = inputData?.value || new Date().toISOString().slice(0, 10);
      if (inputData) inputData.value = dataInicial;
      criarGraficoDiario(dataInicial);

      mostrarDashboard();
      
    } catch (error) {
      console.error('Erro ao carregar dados:', error);
      console.error('Stack trace:', error.stack);
      mostrarErro(`Erro ao carregar dados: ${error.message}`);
    }
  }

  function atualizarCards() {
    try {
      document.getElementById('totalEmprestimos').textContent = totaisGlobal.total_emprestimos || 0;
      document.getElementById('totalReservas').textContent = totaisGlobal.total_reservas || 0;
      document.getElementById('emprestimosAtivos').textContent = totaisGlobal.emprestimos_ativos || 0;
      document.getElementById('reservasAbertas').textContent = totaisGlobal.reservas_abertas || 0;
      console.log('Cards atualizados com sucesso');
    } catch (error) {
      console.error('Erro ao atualizar cards:', error);
    }
  }

  function criarGraficoEmprestimos() {
    if (!ctxEmprestimosChart) {
      console.warn('Contexto do gráfico de empréstimos não encontrado');
      return;
    }
    
    try {
      if (emprestimosChartInstance) emprestimosChartInstance.destroy();

      if (!dadosEmprestimosStatus.length) {
        console.warn('Nenhum dado de empréstimos encontrado');
        // Criar gráfico vazio se não há dados
        emprestimosChartInstance = new Chart(ctxEmprestimosChart, {
          type: 'bar',
          data: {
            labels: ['Sem dados'],
            datasets: [{
              label: 'Empréstimos por Status',
              data: [0],
              backgroundColor: ['rgba(204, 204, 204, 0.6)'],
              borderColor: ['rgba(204, 204, 204, 1)'],
              borderWidth: 2
            }]
          },
          options: {
            ...colunaConfig,
            plugins: {
              ...colunaConfig.plugins,
              title: {
                display: true,
                text: 'Empréstimos por Status'
              }
            }
          }
        });
        return;
      }

      const labels = dadosEmprestimosStatus.map(item => item.emp_status || 'Sem Status');
      const valores = dadosEmprestimosStatus.map(item => parseInt(item.quantidade) || 0);

      // Cores vibrantes para as colunas
      const cores = [
        'rgba(255, 99, 132, 0.8)',   // Vermelho
        'rgba(54, 162, 235, 0.8)',   // Azul
        'rgba(255, 206, 86, 0.8)',   // Amarelo
        'rgba(75, 192, 192, 0.8)',   // Verde claro
        'rgba(153, 102, 255, 0.8)',  // Roxo
        'rgba(255, 159, 64, 0.8)'    // Laranja
      ];

      const coresBorda = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ];

      emprestimosChartInstance = new Chart(ctxEmprestimosChart, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Quantidade de Empréstimos',
            data: valores,
            backgroundColor: cores.slice(0, valores.length),
            borderColor: coresBorda.slice(0, valores.length),
            borderWidth: 2
          }]
        },
        options: {
          ...colunaConfig,
          plugins: {
            ...colunaConfig.plugins,
            title: {
              display: true,
              text: 'Empréstimos por Status',
              font: {
                size: 16,
                weight: 'bold'
              }
            }
          }
        }
      });
      
      console.log('Gráfico de empréstimos criado com sucesso');
    } catch (error) {
      console.error('Erro ao criar gráfico de empréstimos:', error);
    }
  }

  function criarGraficoReservas() {
    if (!ctxReservasChart) {
      console.warn('Contexto do gráfico de reservas não encontrado');
      return;
    }
    
    try {
      if (reservasChartInstance) reservasChartInstance.destroy();

      if (!dadosReservasStatus.length) {
        console.warn('Nenhum dado de reservas encontrado');
        // Criar gráfico vazio se não há dados
        reservasChartInstance = new Chart(ctxReservasChart, {
          type: 'bar',
          data: {
            labels: ['Sem dados'],
            datasets: [{
              label: 'Reservas por Status',
              data: [0],
              backgroundColor: ['rgba(204, 204, 204, 0.6)'],
              borderColor: ['rgba(204, 204, 204, 1)'],
              borderWidth: 2
            }]
          },
          options: {
            ...colunaConfig,
            plugins: {
              ...colunaConfig.plugins,
              title: {
                display: true,
                text: 'Reservas por Status'
              }
            }
          }
        });
        return;
      }

      const labels = dadosReservasStatus.map(item => item.res_status || 'Sem Status');
      const valores = dadosReservasStatus.map(item => parseInt(item.quantidade) || 0);

      // Cores específicas para reservas
      const cores = [
        'rgba(46, 204, 113, 0.8)',   // Verde
        'rgba(231, 76, 60, 0.8)',    // Vermelho
        'rgba(52, 152, 219, 0.8)',   // Azul
        'rgba(241, 196, 15, 0.8)',   // Amarelo
        'rgba(155, 89, 182, 0.8)',   // Roxo
        'rgba(26, 188, 156, 0.8)'    // Turquesa
      ];

      const coresBorda = [
        'rgba(46, 204, 113, 1)',
        'rgba(231, 76, 60, 1)',
        'rgba(52, 152, 219, 1)',
        'rgba(241, 196, 15, 1)',
        'rgba(155, 89, 182, 1)',
        'rgba(26, 188, 156, 1)'
      ];

      reservasChartInstance = new Chart(ctxReservasChart, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Quantidade de Reservas',
            data: valores,
            backgroundColor: cores.slice(0, valores.length),
            borderColor: coresBorda.slice(0, valores.length),
            borderWidth: 2
          }]
        },
        options: {
          ...colunaConfig,
          plugins: {
            ...colunaConfig.plugins,
            title: {
              display: true,
              text: 'Reservas por Status',
              font: {
                size: 16,
                weight: 'bold'
              }
            }
          }
        }
      });
      
      console.log('Gráfico de reservas criado com sucesso');
    } catch (error) {
      console.error('Erro ao criar gráfico de reservas:', error);
    }
  }

  function criarGraficoSemanal() {
    if (!ctxGraficoSemanal) {
      console.warn('Contexto do gráfico semanal não encontrado');
      return;
    }
    
    try {
      if (graficoSemanalInstance) graficoSemanalInstance.destroy();

      if (!dadosSemanaisGlobal.length) {
        console.warn('Nenhum dado semanal encontrado');
        // Criar gráfico vazio se não há dados
        graficoSemanalInstance = new Chart(ctxGraficoSemanal, {
          type: 'bar',
          data: {
            labels: ['Sem dados'],
            datasets: [{
              label: 'Empréstimos Semanais',
              data: [0],
              backgroundColor: 'rgba(255, 238, 135, 0.8)',
              borderColor: 'rgba(255, 238, 135, 1)',
              borderWidth: 2
            }]
          },
          options: {
            ...colunaConfig,
            plugins: {
              ...colunaConfig.plugins,
              title: {
                display: true,
                text: 'Empréstimos por Semana'
              }
            }
          }
        });
        return;
      }

      const labels = dadosSemanaisGlobal.map(item => `Semana ${item.semana}/${item.ano}`);
      const valores = dadosSemanaisGlobal.map(item => parseInt(item.quantidade) || 0);

      // Criar gradiente de cores para as semanas
      const backgroundColors = valores.map((_, index) => {
        const intensity = 0.3 + (index / valores.length) * 0.5;
        return `rgba(99, 132, 255, ${intensity})`;
      });

      const borderColors = valores.map((_, index) => {
        const intensity = 0.8 + (index / valores.length) * 0.2;
        return `rgba(99, 132, 255, ${intensity})`;
      });

      graficoSemanalInstance = new Chart(ctxGraficoSemanal, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Quantidade de Empréstimos',
            data: valores,
            backgroundColor: backgroundColors,
            borderColor: borderColors,
            borderWidth: 2
          }]
        },
        options: {
          ...colunaConfig,
          plugins: {
            ...colunaConfig.plugins,
            title: {
              display: true,
              text: 'Empréstimos por Semana',
              font: {
                size: 16,
                weight: 'bold'
              }
            }
          }
        }
      });
      
      console.log('Gráfico semanal criado com sucesso');
    } catch (error) {
      console.error('Erro ao criar gráfico semanal:', error);
    }
  }

  function criarGraficoDiario(dataEscolhida) {
    if (!ctxGraficoDiario) {
      console.warn('Contexto do gráfico diário não encontrado');
      return;
    }
    
    try {
      if (graficoDiarioInstance) graficoDiarioInstance.destroy();

      // Filtrar dados do dia escolhido
      const dadosDoDia = dadosDiariosGlobal.filter(item => item.data_ref === dataEscolhida);
      
      if (dadosDoDia.length === 0) {
        console.warn(`Nenhum dado encontrado para a data: ${dataEscolhida}`);
        // Se não há dados para o dia, criar gráfico vazio
        graficoDiarioInstance = new Chart(ctxGraficoDiario, {
          type: 'bar',
          data: {
            labels: ['Empréstimos', 'Reservas'],
            datasets: [{
              label: `Dados do dia ${dataEscolhida}`,
              data: [0, 0],
              backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)'
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)'
              ],
              borderWidth: 2
            }]
          },
          options: {
            ...colunaConfig,
            plugins: {
              ...colunaConfig.plugins,
              title: {
                display: true,
                text: `Dados do dia ${dataEscolhida}`
              }
            }
          }
        });
        return;
      }

      // Processar dados do dia
      const dadoProcessado = dadosDoDia[0];
      const labels = ['Empréstimos', 'Reservas'];
      const valores = [
        parseInt(dadoProcessado.qtd_emprestimos) || 0,
        parseInt(dadoProcessado.qtd_reservas) || 0
      ];

      graficoDiarioInstance = new Chart(ctxGraficoDiario, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: `Quantidade`,
            data: valores,
            backgroundColor: [
              'rgba(255, 99, 132, 0.8)',   // Vermelho para empréstimos
              'rgba(54, 162, 235, 0.8)'    // Azul para reservas
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          ...colunaConfig,
          plugins: {
            ...colunaConfig.plugins,
            title: {
              display: true,
              text: `Dados do dia ${dataEscolhida}`,
              font: {
                size: 16,
                weight: 'bold'
              }
            }
          }
        }
      });
      
      console.log(`Gráfico diário criado com sucesso para ${dataEscolhida}`);
    } catch (error) {
      console.error('Erro ao criar gráfico diário:', error);
    }
  }

  function mostrarDashboard() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('error').style.display = 'none';
    document.getElementById('dashboard').style.display = 'block';
    console.log('Dashboard exibido com sucesso');
  }

  function mostrarErro(msg) {
    document.getElementById('loading').style.display = 'none';
    const errorDiv = document.getElementById('error');
    errorDiv.style.display = 'block';
    errorDiv.textContent = msg;
    document.getElementById('dashboard').style.display = 'none';
    console.error('Erro exibido:', msg);
  }

  if (inputData) {
    inputData.addEventListener('change', () => {
      const dataEscolhida = inputData.value;
      console.log('Data selecionada:', dataEscolhida);
      criarGraficoDiario(dataEscolhida);
    });
  }

  // Inicializar
  console.log('Iniciando aplicação...');
  carregarDados();
});