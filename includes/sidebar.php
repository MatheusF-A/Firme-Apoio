<?php
if (!isset($paginaAtiva)) {
    $paginaAtiva = ''; 
}
?>
<aside class="sidebar">
    <div class="logo">
        <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard_usuario.php" class="<?php echo ($paginaAtiva === 'inicio') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Início
        </a>
        <a href="conteudos.php" class="<?php echo ($paginaAtiva === 'conteudos') ? 'active' : ''; ?>">
            <i class="fas fa-book-open"></i> Conteúdos
        </a>
        <a href="auto-cuidado.php" class="<?php echo ($paginaAtiva === 'auto-cuidado') ? 'active' : ''; ?>">
            <i class="fas fa-heart"></i> Auto Cuidado
        </a>
        <a href="desabafo.php" class="<?php echo ($paginaAtiva === 'desabafo') ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots"></i> Desabafo
        </a>
        <a href="ajuda-externa.php" class="<?php echo ($paginaAtiva === 'ajuda-externa') ? 'active' : ''; ?>">
            <i class="fas fa-map-marked-alt"></i> Ajuda Externa
        </a>
    </nav>

    <div class="sidebar-footer">
        
        <hr class="footer-divider">
        
        <div class="contrast-toggle">
            <img src="../assets/img/contrast.png" alt="Ícone de contraste" class="contrast-icon">
            <span class="contrast-label">Alto Contraste</span>
            <label class="switch-toggle" for="contrast-toggle-checkbox">
                <input type="checkbox" id="contrast-toggle-checkbox">
                <span class="slider"></span>
            </label>
        </div>

        <div class="user-profile">
            <i class="fas fa-user-circle"></i>
            <div class="user-details">
                <span><?php echo htmlspecialchars($_SESSION['nome']); ?></span>
                <small>Online</small>
            </div>
        </div>
        
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </div>
</aside>