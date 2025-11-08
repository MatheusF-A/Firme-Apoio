document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Encontra os elementos
    const toggleButton = document.getElementById('toggle-contrast'); // O BOTÃO
    const body = document.body;
    const storageKey = 'firmeApoioHighContrast';

    const contrastIcon = document.getElementById('contrast-icon-img');
    const contrastIconAlt = document.getElementById('contrast-icon-img-alt');
    
    const iconPathDefault = 'assets/img/contrast.png'; 
    const iconPathHighContrast = 'assets/img/contrastBranco.png';
    const iconPathHighContrastAlt = '../assets/img/contrastBranco.png';
    const iconPathDefaultAlt = '../assets/img/contrast.png';

    // 2. Função para aplicar o estado
    function setContrastState(is_enabled) {
        if (is_enabled) {
            body.classList.add('high-contrast');
            localStorage.setItem(storageKey, 'true');

             if (contrastIcon) {
                contrastIcon.src = iconPathHighContrast;
            }

            if (contrastIconAlt) {
                contrastIconAlt.src = iconPathHighContrastAlt;
            }
            
        } else {
            body.classList.remove('high-contrast');
            localStorage.setItem(storageKey, 'false');

            if (contrastIcon) {
                contrastIcon.src = iconPathDefault;
            }

            if (contrastIconAlt) {
                contrastIconAlt.src = iconPathDefaultAlt;
            }     
        }
    }

    // 3. Verifica no carregamento da página
    // Verifica se o estado salvo é 'true'
    const savedState = localStorage.getItem(storageKey) === 'true';
    if (savedState) {
        setContrastState(true);
    }

    // 4. Adiciona o 'click' no botão
    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            // Pega o estado ATUAL e inverte
            const isCurrentlyEnabled = body.classList.contains('high-contrast');
            setContrastState(!isCurrentlyEnabled); // Seta o oposto
        });
    }

});