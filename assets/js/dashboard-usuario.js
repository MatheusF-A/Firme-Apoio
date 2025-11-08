document.addEventListener('DOMContentLoaded', () => {
    
    // NOTA: As variáveis humorLabels e humorData são passadas pelo PHP
    if (typeof humorLabels === 'undefined' || humorData.length === 0) {
        console.warn('Dados de humor insuficientes para desenhar o gráfico.');
        return;
    }

    const ctx = document.getElementById('humorChart');
    
    // Função para obter o valor de uma variável CSS (útil para o Alto Contraste)
    const getCssVar = (name) => getComputedStyle(document.body).getPropertyValue(name).trim();

    // Define as cores dinamicamente
    const primaryColor = getCssVar('--cor-gradiente-texto'); 
    const textColor = getCssVar('--cor-texto-primaria'); 
    const borderColor = getCssVar('--cor-borda-clara');
    
    // Cria o gráfico
    const humorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: humorLabels, // Datas (ex: 15/08)
            datasets: [{
                label: 'Nível de Humor (1-5)',
                data: humorData, // Pontuações (1 a 5)
                borderColor: primaryColor,
                backgroundColor: 'rgba(0, 123, 255, 0.1)', // Um fundo leve
                borderWidth: 3,
                tension: 0.4, // Suaviza a linha
                pointRadius: 5,
                pointBackgroundColor: primaryColor
            }]
        },
        options: {
            maintainAspectRatio: false, // Permite definir altura
            responsive: true,
            scales: {
                y: {
                    min: 1,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        color: textColor,
                        callback: function(value) {
                            // Converte a nota 1-5 em um rótulo de humor (para ser mais intuitivo)
                            switch(value) {
                                case 5: return 'Ótimo';
                                case 4: return 'Bom';
                                case 3: return 'Neutro';
                                case 2: return 'Ruim';
                                case 1: return 'Péssimo';
                                default: return value;
                            }
                        }
                    },
                    grid: {
                        color: borderColor
                    }
                },
                x: {
                    ticks: {
                        color: textColor
                    },
                    grid: {
                        color: borderColor
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                }
            }
        }
    });

});