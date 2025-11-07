document.addEventListener('DOMContentLoaded', () => {
    
    // Este arquivo agora cuida APENAS da lógica de deletar.
    // O player de vídeo é um iframe embutido e não precisa de JS.

    // 1. Acha todos os botões de deletar
    const deleteButtons = document.querySelectorAll('.js-delete-video');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            // Impede o <button> de tentar enviar um formulário
            e.preventDefault(); 

            const id = button.dataset.id;
            const titulo = button.dataset.titulo;
            
            // 2. Confirmação
            if (!confirm(`Tem certeza que deseja deletar este vídeo?\n\n${titulo}`)) {
                return; // Cancela
            }

            // 3. Prepara o envio (AJAX/Fetch)
            const formData = new FormData();
            formData.append('id', id);

            try {
                // 4. Chama o backend (que agora espera POST)
                const response = await fetch('../controles/deletar-videobd.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // 5. Resposta do PHP
                if (data.status === 'success') {
                    alert(data.message); // Alerta de sucesso
                    
                    // 6. Remove o card da tela (sem recarregar)
                    const card = document.getElementById(`video-card-${id}`);
                    if (card) {
                        card.remove();
                    }
                } else {
                    alert(data.message); // Alerta de erro
                }

            } catch (error) {
                console.error('Erro ao deletar:', error);
                alert('Erro de conexão. Não foi possível deletar o vídeo.');
            }
        });
    });

});