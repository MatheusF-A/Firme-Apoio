/* assets/js/chat.js */

let usuarioAlvoId = null; // Controle global para o modal

document.addEventListener('DOMContentLoaded', function() {
    
    const chatWindow = document.getElementById('chat-window');
    const formChat = document.getElementById('form-chat');
    const inputMsg = document.getElementById('mensagem-input');
    const btnEnviar = document.getElementById('btn-enviar');

    let ultimoId = 0;
    let primeiroCarregamento = true; // <--- NOVA VARIÁVEL DE CONTROLE

    // --- ENVIAR ---
    if (formChat) {
        formChat.addEventListener('submit', function(e) {
            e.preventDefault();
            const txt = inputMsg.value.trim();
            if(!txt) return;

            inputMsg.disabled = true;
            btnEnviar.disabled = true;
            const fd = new FormData();
            fd.append('mensagem', txt);

            fetch(ROTA_ENVIAR, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') { inputMsg.value = ''; carregarMensagens(); }
            })
            .finally(() => { inputMsg.disabled = false; btnEnviar.disabled = false; inputMsg.focus(); });
        });
    }

    // --- LISTAR ---
    function carregarMensagens() {
        fetch(ROTA_LISTAR)
        .then(r => r.json())
        .then(lista => {
            
            // CORREÇÃO VISUAL:
            // Se for a primeira vez que conectamos com sucesso, removemos o "Carregando..."
            // independentemente se tem mensagens ou não.
            if (primeiroCarregamento) {
                const empty = chatWindow.querySelector('.empty-state');
                if (empty) {
                    if (lista.length === 0) {
                        // Se estiver vazio, muda o texto
                        empty.innerHTML = '<i class="fas fa-comment-slash"></i><p>Nenhuma mensagem ainda.<br>Seja o primeiro!</p>';
                    } else {
                        // Se tem mensagens, remove o aviso
                        empty.remove();
                    }
                }
                primeiroCarregamento = false;
            }

            // Filtra apenas as novas
            const novas = lista.filter(m => Number(m.id) > ultimoId);

            if (novas.length > 0) {
                // Garante que o placeholder sumiu se chegar mensagem nova
                const empty = chatWindow.querySelector('.empty-state');
                if(empty) empty.remove();

                novas.forEach(msg => {
                    const div = document.createElement('div');
                    let cssClass = msg.sou_eu ? 'msg-minha' : 'msg-outros';
                    if(msg.is_admin) cssClass += ' msg-admin-style';

                    div.className = `mensagem-box ${cssClass}`;
                    
                    // Clique para banir (Apenas Voluntário)
                    if (typeof EH_VOLUNTARIO !== 'undefined' && EH_VOLUNTARIO === true && !msg.is_admin) {
                        div.style.cursor = 'pointer';
                        div.title = 'Clique para ver dados e banir';
                        div.onclick = () => abrirModalDetalhes(msg.user_id_real);
                    }

                    div.innerHTML = `
                        <div class="msg-header">
                            <span class="msg-autor">
                                ${msg.is_admin ? '<i class="fas fa-shield-alt"></i> ' : ''}
                                ${msg.autor}
                            </span>
                            <span class="msg-hora">${msg.hora}</span>
                        </div>
                        <div class="msg-conteudo">${msg.texto}</div>
                    `;
                    chatWindow.appendChild(div);
                    if(Number(msg.id) > ultimoId) ultimoId = Number(msg.id);
                });
                
                chatWindow.scrollTo({ top: chatWindow.scrollHeight, behavior: 'smooth' });
            }
        })
        .catch(err => {
            console.error("Erro polling:", err);
        });
    }

    carregarMensagens();
    setInterval(carregarMensagens, 3000);
});

// --- FUNÇÕES GLOBAIS (MODAL) ---

function abrirModalDetalhes(idUsuario) {
    if(!idUsuario) return;
    const modal = document.getElementById('modal-detalhes');
    const corpo = document.getElementById('corpo-detalhes');
    
    usuarioAlvoId = idUsuario;
    modal.style.display = 'flex';
    corpo.innerHTML = '<p>Carregando dados...</p>';

    const fd = new FormData();
    fd.append('id_alvo', idUsuario);

    fetch(ROTA_DADOS, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(resp => {
        if(resp.status === 'success') {
            const d = resp.dados;
            corpo.innerHTML = `
                <p><strong>Nome:</strong> ${d.Nome}</p>
                <p><strong>Email:</strong> ${d.email}</p>
                <p><strong>Nick:</strong> ${d.chat_nickname}</p>
                <p><strong>Desde:</strong> ${d.dtCadastro}</p>
                <hr>
                <p style="color:red; font-size:0.9rem;">Banimento é imediato.</p>
            `;
        } else {
            corpo.innerHTML = '<p>Erro ao buscar dados.</p>';
        }
    });
}

function fecharModalDetalhes() {
    document.getElementById('modal-detalhes').style.display = 'none';
    usuarioAlvoId = null;
}

const btnConfirmarBan = document.getElementById('btn-confirmar-ban');
if(btnConfirmarBan) {
    btnConfirmarBan.addEventListener('click', function() {
        if(!usuarioAlvoId) return;
        if(confirm('Tem certeza?')) {
            const fd = new FormData();
            fd.append('id_alvo', usuarioAlvoId);
            fetch(ROTA_BANIR, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if(d.status === 'success') {
                    alert('Banido!');
                    fecharModalDetalhes();
                    window.location.reload(); 
                } else {
                    alert('Erro.');
                }
            });
        }
    });
}