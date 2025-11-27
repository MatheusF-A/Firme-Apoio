document.addEventListener('DOMContentLoaded', function() {
    const btnAceitar = document.getElementById('btn-aceitar-termo');

    if (btnAceitar) {
        btnAceitar.addEventListener('click', function() {
            
            // Feedback visual imediato
            btnAceitar.innerText = "Processando...";
            btnAceitar.disabled = true;

            fetch('../../controles/chat/aceitar-termo.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Padrão B: Recarregar a página após sucesso
                    window.location.reload();
                } else {
                    alert('Erro: ' + data.message);
                    btnAceitar.innerText = "Tentar Novamente";
                    btnAceitar.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro de conexão.');
                btnAceitar.innerText = "Tentar Novamente";
                btnAceitar.disabled = false;
            });
        });
    }
});