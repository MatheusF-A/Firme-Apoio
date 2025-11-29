<?php

if (!isset($path)) {
    $path = '../';
}

if (!isset($paginaAtiva)) {
    $paginaAtiva = ''; // Padrão
}

$perfil = $_SESSION['perfil'] ?? 'usuario';
$linkInicio = ($perfil === 'voluntario') ? 'dashboard-voluntario.php' : 'dashboard-usuario.php';
$linkAutoCuidado = ($perfil === 'voluntario') ? 'acompanhamento.php' : 'auto-cuidado.php';

?>
<aside class="sidebar">
    <div class="logo">
        <img src="<?php echo $path; ?>/assets/img/logoLado.png" alt="Firme Apoio Logo">
    </div>

    <nav class="sidebar-nav">
        
        <!-- Link "Início" corrigido -->
        <a href="<?php echo $path; ?>paginas/<?php echo $linkInicio; ?>" class="<?php echo ($paginaAtiva === 'inicio') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Início
        </a>
        
        
        <!-- Link "Auto Cuidado" agora é dinâmico -->
        <a href="<?php echo $path; ?>paginas/<?php echo $linkAutoCuidado; ?>" class="<?php echo ($paginaAtiva === 'auto-cuidado') ? 'active' : ''; ?>">
            <i class="fas fa-heart"></i> 
            <!-- O texto também muda -->
            <?php echo ($perfil === 'voluntario') ? 'Acompanhamento' : 'Auto Cuidado'; ?>
        </a>
        
        <a href="<?php echo $path; ?>paginas/ajuda-externa.php" class="<?php echo ($paginaAtiva === 'ajuda-externa') ? 'active' : ''; ?>">
            <i class="fas fa-map-marked-alt"></i> Ajuda Externa
        </a>

        <a href="<?php echo $path; ?>paginas/conteudos.php" class="<?php echo ($paginaAtiva === 'conteudos') ? 'active' : ''; ?>">
            <i class="fas fa-book-open"></i> Conteúdos
        </a>

        <a href="<?php echo $path; ?>paginas/chat/chat.php" class="<?php echo ($paginaAtiva === 'chat') ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots"></i> Chat
        </a>
        
    </nav>

    <div class="sidebar-footer">
        
        <hr class="footer-divider">
        
        <div class="contrast-toggle">
            <img src="<?php echo $path; ?>assets/img/contrast.png"data-path="<?php echo $path; ?>"alt="Ícone de contraste" class="contrast-icon" id="contrast-icon-img">
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
        
        <a href="<?php echo $path; ?>logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </div>
</aside>