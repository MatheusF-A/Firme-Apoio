document.addEventListener('DOMContentLoaded', () => {
    
    // Verifica se há dados para o gráfico
    if (typeof humorLabels === 'undefined' || typeof humorData === 'undefined' || humorData.length === 0) {
        return;
    }

    const ctx = document.getElementById('humorChart');
    if (!ctx) return;

    // Função para pegar as cores atuais do CSS (que mudam com o tema)
    const getThemeColors = () => {
        const styles = getComputedStyle(document.body);
        return {
            primary: styles.getPropertyValue('--cor-gradiente-texto').trim(),
            text: styles.getPropertyValue('--cor-texto-primaria').trim(),
            grid: styles.getPropertyValue('--cor-borda-clara').trim()
        };
    };

    // Pega as cores iniciais
    let colors = getThemeColors();
    
    // Cria o gráfico
    const humorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: humorLabels,
            datasets: [{
                label: 'Nível de Humor',
                data: humorData,
                borderColor: colors.primary,
                backgroundColor: 'rgba(0, 0, 0, 0.0)', // Transparente
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: colors.primary,
                pointBorderColor: colors.primary
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: {
                    min: 1,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        color: colors.text, // Cor dinâmica
                        font: { size: 11 },
                        callback: function(value) {
                            const map = {1: 'Péssimo', 2: 'Ruim', 3: 'Neutro', 4: 'Bom', 5: 'Ótimo'};
                            return map[value] || value;
                        }
                    },
                    grid: {
                        color: colors.grid, // Cor dinâmica
                        borderDash: [5, 5]
                    }
                },
                x: {
                    ticks: {
                        color: colors.text // Cor dinâmica
                    },
                    grid: {
                        display: false // Remove a grade vertical para limpar o visual
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            }
        }
    });

    // ----> A MÁGICA: Atualiza o gráfico quando o tema muda <----
    window.addEventListener('temaMudou', () => {
        // 1. Pega as novas cores (que agora são amarelo/preto no alto contraste)
        const newColors = getThemeColors();

        // 2. Aplica as novas cores nas propriedades do gráfico
        humorChart.data.datasets[0].borderColor = newColors.primary;
        humorChart.data.datasets[0].pointBackgroundColor = newColors.primary;
        humorChart.data.datasets[0].pointBorderColor = newColors.primary;
        
        humorChart.options.scales.y.ticks.color = newColors.text;
        humorChart.options.scales.x.ticks.color = newColors.text;
        humorChart.options.scales.y.grid.color = newColors.grid;

        // 3. Redesenha o gráfico
        humorChart.update();
    });

});