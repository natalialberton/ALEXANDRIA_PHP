
    
        const API_URL = 'api_dados.php'; 

        let emprestimosChart, reservasChart;

        async function carregarDados() {
            try {
                document.getElementById('loading').style.display = 'block';
                document.getElementById('dashboard').style.display = 'none';
                document.getElementById('error').style.display = 'none';

                console.log('Fazendo requisição para:', API_URL);

                const response = await fetch(API_URL);
                console.log('Status da resposta:', response.status);

                // Verificar se a resposta é válida
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
                }

                // Verificar se a resposta é JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const texto = await response.text();
                    console.error('Resposta não é JSON:', texto.substring(0, 200));
                    throw new Error(`API retornou ${contentType || 'tipo desconhecido'} ao invés de JSON. Verifique se há erros PHP.`);
                }

                const result = await response.json();
                console.log('Dados recebidos:', result);

                if (!result.success) {
                    throw new Error(result.error || 'Erro desconhecido da API');
                }

                const dados = result.data;
                
                // Atualizar cards com verificação de dados
                document.getElementById('totalEmprestimos').textContent = dados.totais?.total_emprestimos || 0;
                document.getElementById('totalReservas').textContent = dados.totais?.total_reservas || 0;
                document.getElementById('emprestimosAtivos').textContent = dados.totais?.emprestimos_ativos || 0;
                document.getElementById('reservasAbertas').textContent = dados.totais?.reservas_abertas || 0;

                // Criar gráficos
                criarGraficoEmprestimos(dados.emprestimos_por_status || []);
                criarGraficoReservas(dados.reservas_por_status || []);

                document.getElementById('loading').style.display = 'none';
                document.getElementById('dashboard').style.display = 'block';

            } catch (error) {
                console.error('Erro completo:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').style.display = 'block';
                document.getElementById('error').innerHTML = `
                    <strong>Erro ao carregar dados:</strong><br>
                    ${error.message}<br><br>
                    <small>
                    <strong>Possíveis soluções:</strong><br>
                    1. Verifique se o arquivo 'api_dados.php' existe e está no local correto<br>
                    2. Teste a API diretamente no navegador: <a href="${API_URL}" target="_blank" style="color: white;">${API_URL}</a><br>
                    3. Abra o Console do navegador (F12) para ver erros detalhados<br>
                    4. Verifique se o servidor PHP está rodando<br>
                    5. Verifique as credenciais do banco de dados
                    </small>
                `;
            }
        }

        function criarGraficoEmprestimos(dados) {
            const ctx = document.getElementById('emprestimosChart').getContext('2d');
            
            if (emprestimosChart) {
                emprestimosChart.destroy();
            }

            // Dados de fallback se não houver dados reais
            if (!dados || dados.length === 0) {
                dados = [
                    { emp_status: 'Ativo', quantidade: 15 },
                    { emp_status: 'Devolvido', quantidade: 23 },
                    { emp_status: 'Atrasado', quantidade: 7 },
                    { emp_status: 'Renovado', quantidade: 12 }
                ];
            }

            const labels = dados.map(item => item.emp_status);
            const values = dados.map(item => item.quantidade);
            const cores = ['#3498db', '#27ae60', '#e74c3c', '#f39c12', '#9b59b6'];

            emprestimosChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade',
                        data: values,
                        backgroundColor: cores.slice(0, labels.length),
                        borderColor: cores.slice(0, labels.length),
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function criarGraficoReservas(dados) {
            const ctx = document.getElementById('reservasChart').getContext('2d');
            
            if (reservasChart) {
                reservasChart.destroy();
            }

            // Dados de fallback se não houver dados reais
            if (!dados || dados.length === 0) {
                dados = [
                    { res_status: 'Aberta', quantidade: 8 },
                    { res_status: 'Atendida', quantidade: 18 },
                    { res_status: 'Cancelada', quantidade: 3 },
                    { res_status: 'Expirada', quantidade: 5 }
                ];
            }

            const labels = dados.map(item => item.res_status);
            const values = dados.map(item => item.quantidade);
            const cores = ['#27ae60', '#3498db', '#95a5a6', '#e74c3c'];

            reservasChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade',
                        data: values,
                        backgroundColor: cores.slice(0, labels.length),
                        borderColor: cores.slice(0, labels.length),
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Carregar dados quando a página carrega
        document.addEventListener('DOMContentLoaded', carregarDados);

        // Atualizar dados a cada 5 minutos
        setInterval(carregarDados, 300000);
