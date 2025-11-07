document.addEventListener('DOMContentLoaded', () => {
    
    // --- Seleção dos Elementos ---
    const imagemInput = document.getElementById('imagem');
    const previewContainer = document.getElementById('imagem-preview');
    const previewText = previewContainer.querySelector('.imagem-preview-text');
    const previewIcon = previewContainer.querySelector('.default-icon');
    
    // --- Função: Resetar o Preview da Imagem ---
    function resetPreview() {
        if (previewContainer) previewContainer.style.backgroundImage = 'none';
        if (previewText) previewText.style.display = 'block';
        if (previewIcon) previewIcon.style.display = 'block';
    }

    // --- Evento: Preview da Imagem (on change) ---
    // Esta é a única lógica que este arquivo deve ter
    if (imagemInput) {
        imagemInput.addEventListener('change', function() {
            const file = this.files[0]; 
            if (file) {
                // Validação de Tamanho
                if (file.size > 5 * 1024 * 1024) { // 5MB
                    alert('Erro: A imagem é muito grande (Máx 5MB).');
                    this.value = ''; 
                    resetPreview();
                    return;
                }
                
                // Validação de Tipo
                if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                    alert('Erro: Tipo de arquivo inválido (Apenas JPEG, PNG, GIF).');
                    this.value = ''; 
                    resetPreview();
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewContainer.style.backgroundImage = `url(${e.target.result})`;
                    previewText.style.display = 'none';
                    previewIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                resetPreview();
            }
        });
    }
});