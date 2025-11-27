// assets/js/chat.js

document.addEventListener('DOMContentLoaded', function() {
    
    const chatWindow = document.getElementById('chat-window');
    const formChat = document.getElementById('form-chat');
    const inputMsg = document.getElementById('mensagem-input');
    const btnEnviar = document.getElementById('btn-enviar');

    let totalMensagensCarregadas = 0;

    // --- 1. FUNÇÃO DE ENVIAR ---
    if (formChat) {
        formChat.addEventListener('submit', function(e) {
            e.preventDefault(); // Impede recarregamento da página

            const texto = inputMsg.value.trim();
            if (texto === "") return;

            // Bloqueia input enquanto envia
            inputMsg.disabled = true;
            btnEnviar.disabled = true;

            const formData = new FormData();
            formData.append('mensagem', texto);

            fetch('../../controles/chat/enviar-mensagem.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    inputMsg.value = ''; // Limpa campo
                    carregarMensagens(); // Força atualização imediata
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                // Libera input
                inputMsg.disabled = false;
                btnEnviar.disabled = false;
                inputMsg.focus();
            });
        });
    }

    // --- 2. FUNÇÃO DE BUSCAR MENSAGENS (Polling) ---
    function carregarMensagens() {
        fetch('../../controles/chat/listar-mensagens.php')
        .then(response => response.json())
        .then(mensagens => {
            
            // Se o número de mensagens mudou, renderiza tudo de novo
            // (Para otimizar, poderíamos buscar só novos IDs, mas para este escopo, limpar e renderizar é seguro)
            if (mensagens.length > totalMensagensCarregadas) {
                
                chatWindow.innerHTML = ''; // Limpa container

                mensagens.forEach(msg => {
                    const divMsg = document.createElement('div');
                    
                    // Define a classe baseada em quem enviou (Minha x Outros)
                    const classeTipo = msg.sou_eu ? 'msg-minha' : 'msg-outros';
                    divMsg.classList.add('mensagem-box', classeTipo);

                    divMsg.innerHTML = `
                        <div class="msg-header">
                            <span class="msg-autor">${msg.autor}</span>
                            <span class="msg-hora">${msg.hora}</span>
                        </div>
                        <div class="msg-conteudo">
                            ${msg.texto}
                        </div>
                    `;

                    chatWindow.appendChild(divMsg);
                });

                // Rola para o final automaticamente
                chatWindow.scrollTop = chatWindow.scrollHeight;
                
                totalMensagensCarregadas = mensagens.length;
            }
        })
        .catch(err => console.error("Erro no polling:", err));
    }

    // Carrega a primeira vez
    carregarMensagens();

    // Roda a cada 3 segundos
    setInterval(carregarMensagens, 3000);
});