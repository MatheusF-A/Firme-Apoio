document.addEventListener('DOMContentLoaded', () => {
    
    // --- Seleção dos Elementos ---
    const form = document.getElementById('form-auto-avaliacao');
    const submitButton = document.getElementById('btn-submit-avaliacao');
    const submitButtonText = document.getElementById('btn-submit-text');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            // 1. Impede o envio padrão
            e.preventDefault(); 

            // --- Validação Simples (Frontend) ---
            // Verifica se um humor foi selecionado
            const notaHumor = form.querySelector('input[name="notaHumor"]:checked');
            if (!notaHumor) {
                alert('Por favor, selecione como você está se sentindo hoje.');
                return;
            }
            
            // Verifica se as textareas não estão vazias
            if (form.perguntaUm.value.trim() === '' || 
                form.perguntaDois.value.trim() === '' || 
                form.perguntaTres.value.trim() === '') {
                alert('Por favor, responda todas as perguntas.');
                return;
            }

            // Desabilita o botão
            submitButton.disabled = true;
            submitButtonText.textContent = 'Enviando...';

            const formData = new FormData(form);

            try {
                // 2. Envia os dados (AJAX/Fetch)
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                // 3. Recebe a resposta JSON do PHP
                const data = await response.json();

                // 4. Mostra o alerta
                if (data.status === 'success') {
                    alert(data.message); // Alerta de Sucesso
                    form.reset(); // Limpa o formulário
                } else {
                    alert(data.message); // Alerta de Erro
                }

            } catch (error) {
                console.error('Erro ao enviar o formulário:', error);
                alert('Erro de conexão. Tente novamente.');
            } finally {
                // Reabilita o botão
                submitButton.disabled = false;
                submitButtonText.textContent = 'Enviar';
            }
        });
    }
});