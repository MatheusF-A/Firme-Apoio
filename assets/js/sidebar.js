document.addEventListener('DOMContentLoaded', () => {
    // Seleciona os elementos essenciais da interface do dashboard
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const overlay = document.getElementById('overlay');
    const sidebar = document.querySelector('.sidebar'); // Seleciona a sidebar

    // Verifica se todos os elementos (botão, overlay e sidebar) existem na página
    if (hamburgerBtn && overlay && sidebar) {
        
        const toggleSidebar = () => {
            document.body.classList.toggle('sidebar-open');
        };

        // Adiciona o evento de clique ao botão hamburger
        hamburgerBtn.addEventListener('click', toggleSidebar);

        // Adiciona o evento de clique ao overlay (para fechar o menu ao clicar fora)
        overlay.addEventListener('click', toggleSidebar);
    }
});