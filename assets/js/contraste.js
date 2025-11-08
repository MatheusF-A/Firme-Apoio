document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Encontra os elementos
    const toggleSwitch = document.getElementById('contrast-toggle-checkbox');
    const body = document.body;
    const storageKey = 'firmeApoioHighContrast';

    const contrastIcon = document.getElementById('contrast-icon-img'); 
    const iconPathDefault = '../assets/img/contrast.png'; 
    const iconPathHighContrast = '../assets/img/contrastBranco.png';

    // 2. Função para aplicar o estado
    function setContrastState(is_enabled) {
        if (is_enabled) {
            body.classList.add('high-contrast');
            localStorage.setItem(storageKey, 'true');

            if (contrastIcon) {
                contrastIcon.src = iconPathHighContrast;
            }

        } else {
            body.classList.remove('high-contrast');
            localStorage.setItem(storageKey, 'false');

            if (contrastIcon) {
                contrastIcon.src = iconPathDefault;
            }

        }
    }

    // 3. Verifica no carregamento da página
    // Verifica se o estado salvo é 'true'
    const savedState = localStorage.getItem(storageKey) === 'true';
    if (savedState) {
        setContrastState(true);
        if (toggleSwitch) {
            toggleSwitch.checked = true;
        }
    }

    // 4. Adiciona o 'click' (change event)
    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', () => {
            // 'toggleSwitch.checked' nos diz o novo estado (ligado ou desligado)
            setContrastState(toggleSwitch.checked);
        });
    }

});