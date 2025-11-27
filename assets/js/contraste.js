document.addEventListener('DOMContentLoaded', () => {
    
    const toggleSwitch = document.getElementById('contrast-toggle-checkbox');
    const body = document.body;
    const storageKey = 'firmeApoioHighContrast';
    const contrastIcon = document.getElementById('contrast-icon-img'); 

    // 2. Lógica Inteligente de Caminho
    // Pega o caminho correto do PHP (via atributo data-path) ou usa '../' como reserva
    const basePath = contrastIcon ? (contrastIcon.getAttribute('data-path') || '../') : '../';

    // Define os caminhos das imagens usando a base correta
    const iconPathDefault = basePath + 'assets/img/contrast.png';
    const iconPathHighContrast = basePath + 'assets/img/contrastBranco.png';

    // 3. Função para aplicar o estado
    function setContrastState(is_enabled) {
        if (is_enabled) {
            body.classList.add('high-contrast');
            localStorage.setItem(storageKey, 'true');

            // Troca para ícone branco se existir
            if (contrastIcon) {
                contrastIcon.src = iconPathHighContrast;
            }

        } else {
            body.classList.remove('high-contrast');
            localStorage.setItem(storageKey, 'false');

            // Troca para ícone normal se existir
            if (contrastIcon) {
                contrastIcon.src = iconPathDefault;
            }

        }
        window.dispatchEvent(new Event('temaMudou'));
    }
    
    // (Mantive sua função original de recarregar gráfico, caso use futuramente)
    function recarregarPanorama() {
        fetch(window.location.href)
            .then(response => {
                if (!response.ok) throw new Error('Erro ao carregar página');
                return response.text();
            })
            .then(htmlTexto => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlTexto, 'text/html');
                const divAtual = document.getElementById('panorana-chart-container');
                if(divAtual) {
                    const novoConteudo = doc.getElementById('panorana-chart-container').innerHTML;
                    divAtual.innerHTML = novoConteudo;
                }
            })
            .catch(err => console.error('Erro:', err));
    }

    // 4. Verifica no carregamento da página
    const savedState = localStorage.getItem(storageKey) === 'true';
    
    // Aplica o estado inicial (Check visual no toggle e na classe)
    if (toggleSwitch) {
        toggleSwitch.checked = savedState;
    }
    setContrastState(savedState);

    // 5. Adiciona o evento de clique
    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', () => {
            setContrastState(toggleSwitch.checked);
        });
    }
});