document.addEventListener('DOMContentLoaded', () => {
    
    // --- Seleção dos Elementos do Modal ---
    const modalOverlay = document.getElementById('modal-novo-habito');
    const modalTitle = document.getElementById('modal-title');
    const form = document.getElementById('form-habito-modal');
    const hiddenHabitoID = document.getElementById('modal-habitoID');
    
    const abrirBtn = document.getElementById('btn-abrir-modal-habito');
    const voltarBtn = document.getElementById('btn-voltar-modal');
    const btnsEditar = document.querySelectorAll('.btn-editar'); // Pega TODOS os botões de editar

    const submitButton = document.getElementById('btn-salvar-modal');
    const submitButtonText = document.getElementById('btn-salvar-text');
    const imagemInput = document.getElementById('modal-imagem');
    const imagemText = document.getElementById('modal-imagem-text');

    // --- Funções de Controle do Modal ---
    const showModal = () => modalOverlay.classList.add('show');
    const hideModal = () => modalOverlay.classList.remove('show');

    // --- Função para Limpar o Formulário ---
    function resetForm() {
        form.reset();
        imagemText.textContent = 'Escolher arquivo...';
        hiddenHabitoID.value = ''; // Limpa o ID oculto
    }

    // --- Evento: Abrir Modal para CRIAR ---
    if (abrirBtn) {
        abrirBtn.addEventListener('click', () => {
            resetForm();
            modalTitle.textContent = 'NOVO HÁBITO';
            form.action = '../controles/cadastrar-habitobd.php'; // Ação de Cadastrar
            submitButtonText.textContent = 'Salvar';
            showModal();
        });
    }

    // --- Evento: Abrir Modal para EDITAR ---
    btnsEditar.forEach(btn => {
        btn.addEventListener('click', () => {
            // 1. Pega os dados guardados no botão (data-* attributes)
            const id = btn.dataset.id;
            const nome = btn.dataset.nome;
            const detalhes = btn.dataset.detalhes;
            const frequenciaId = btn.dataset.frequenciaId;
            // (Não pegamos a imagem, o usuário pode enviar uma nova se quiser)

            // 2. Preenche o formulário
            resetForm();
            modalTitle.textContent = 'EDITAR HÁBITO';
            form.action = '../controles/editar-habitobd.php'; // Ação de Editar
            submitButtonText.textContent = 'Atualizar';
            
            form.querySelector('#modal-nome').value = nome;
            form.querySelector('#modal-detalhes').value = detalhes;
            form.querySelector('#modal-frequencia').value = frequenciaId;
            hiddenHabitoID.value = id; // Define o ID oculto
            
            // 3. Mostra o modal
            showModal();
        });
    });

    // --- Evento: Fechar o Modal ---
    if (voltarBtn) {
        voltarBtn.addEventListener('click', hideModal);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) hideModal();
        });
    }

    // --- Evento: Mostrar nome do arquivo da imagem ---
    if (imagemInput && imagemText) {
        imagemInput.addEventListener('change', () => {
            if (imagemInput.files.length > 0) {
                imagemText.textContent = imagemInput.files[0].name;
            } else {
                imagemText.textContent = 'Escolher arquivo...';
            }
        });
    }

    // --- Evento: Envio do Formulário (AJAX/JSON) ---
    // (Este formulário agora lida tanto com 'Criar' quanto 'Editar')
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault(); 

            if (form.frequenciaID.value === "") {
                alert('Por favor, selecione uma frequência.');
                return;
            }

            const originalButtonText = submitButtonText.textContent;
            submitButton.disabled = true;
            submitButtonText.textContent = 'Salvando...';

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    alert(data.message);
                    hideModal();
                    location.reload(); // Recarrega a página para mostrar as mudanças
                } else {
                    alert(data.message); 
                }

            } catch (error) {
                console.error('Erro ao enviar o formulário:', error);
                alert('Erro de conexão. Tente novamente.');
            } finally {
                submitButton.disabled = false;
                submitButtonText.textContent = originalButtonText;
            }
        });
    }
});