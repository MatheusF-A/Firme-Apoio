document.addEventListener('DOMContentLoaded', function () {
    
    // --- Seleção dos Elementos ---
    const urlInput = document.getElementById('url_video');
    const thumbImg = document.getElementById('video-thumb');
    const videoIdInput = document.getElementById('video_id');
    const loading = document.getElementById('thumb-loading');
    const defaultText = document.getElementById('thumb-default-text');
    
    const titleInput = document.getElementById('titulo');
    const previewTitle = document.getElementById('preview-title');

    function showLoading(show) {
        if (loading) loading.style.display = show ? 'flex' : 'none';
    }
    function showDefaultText(show) {
        if (defaultText) defaultText.style.display = show ? 'flex' : 'none';
    }

    // --- Função: Extrair ID do YouTube ---
    function extractYouTubeID(url) {
        if (!url) return null;
        const patterns = [
            /(?:youtube\.com\/.*v=|youtube\.com\/v\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/,
            /(?:youtu\.be\/)([A-Za-z0-9_-]{11})/
        ];
        for (const re of patterns) {
            const m = url.match(re);
            if (m && m[1]) return m[1];
        }
        return null;
    }

    // --- Função: Atualizar a Thumbnail ---
    function updateThumbnail() {
        const url = urlInput.value.trim();
        const id = extractYouTubeID(url);
        
        if (!id) {
            thumbImg.src = ""; // Usa o placeholder (se a imagem quebrar)
            videoIdInput.value = '';
            showDefaultText(true);
            showLoading(false);
            return;
        }

        videoIdInput.value = id;
        showDefaultText(false);
        showLoading(true);
        
        const thumbUrl = `https://img.youtube.com/vi/${id}/hqdefault.jpg`;
        const testImg = new Image();
        
        testImg.onload = function () {
            // Se a thumbnail carregar, use-a
            thumbImg.src = thumbUrl;
            showLoading(false);
        };
        testImg.onerror = function () {
            // Se falhar (ex: vídeo privado), use o placeholder
            thumbImg.src = "";
            showLoading(false);
            showDefaultText(true);
        };
        testImg.src = thumbUrl;
    }

    // --- Evento: Atualizar o Título do Preview ---
    if (titleInput && previewTitle) {
        titleInput.addEventListener('input', () => {
            if (titleInput.value.trim() === '') {
                previewTitle.textContent = 'TITULO DO VÍDEO';
            } else {
                previewTitle.textContent = titleInput.value;
            }
        });
    }

    // --- Evento: Disparar busca da thumbnail ---
    if (urlInput) {
        let debounceTimer;
        urlInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateThumbnail, 400);
        });
        urlInput.addEventListener('paste', () => setTimeout(updateThumbnail, 100));
    }

    // ----> INÍCIO DA LÓGICA DE ENVIO AJAX (PADRÃO) <----
    
    const form = document.getElementById('form-cadastro-video');
    const submitButton = document.getElementById('btn-submit-video');
    const submitButtonText = document.getElementById('btn-submit-text');

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault(); // Impede o envio HTML

            submitButton.disabled = true;
            submitButtonText.textContent = 'Enviando...';

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alert(data.message); // Alerta de Sucesso
                    form.reset();
                    thumbImg.src = ""; // Reseta a thumbnail
                    videoIdInput.value = '';
                    previewTitle.textContent = 'TITULO DO VÍDEO';
                    showDefaultText(true);
                } else {
                    alert(data.message); // Alerta de Erro
                }

            } catch (error) {
                console.error('Erro ao enviar o formulário:', error);
                alert('Erro de conexão. Tente novamente.');
            } finally {
                submitButton.disabled = false;
                submitButtonText.textContent = 'Enviar';
            }
        });
    }
});