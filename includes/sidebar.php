<?php
/*
 * Este componente espera que session_start() já tenha sido chamado
 * na página que o incluiu.
 */

if (!isset($paginaAtiva)) {
    $paginaAtiva = ''; // Padrão
}

// ----> INÍCIO DA LÓGICA DO LINK DINÂMICO (ATUALIZADA) <----
// 1. Define o perfil (padrão 'usuario')
$perfil = $_SESSION['perfil'] ?? 'usuario';

// 2. Define o link de "Início"
// (Volta ao dashboard de cards para o voluntário)
$linkInicio = ($perfil === 'voluntario') ? 'dashboard-voluntario.php' : 'dashboard-usuario.php';

// 3. Define o link de "Auto Cuidado" (que vira "Acompanhamento" para voluntários)
$linkAutoCuidado = ($perfil === 'voluntario') ? 'acompanhamento.php' : 'auto-cuidado.php';
// ----> FIM DA LÓGICA DO LINK DINÂMICO <----

?>
<aside class="sidebar">
    <div class="logo">
        <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
    </div>

    <nav class="sidebar-nav">
        
        <!-- Link "Início" corrigido -->
        <a href="<?php echo $linkInicio; ?>" class="<?php echo ($paginaAtiva === 'inicio') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Início
        </a>
        
        <a href="conteudos.php" class="<?php echo ($paginaAtiva === 'conteudos') ? 'active' : ''; ?>">
            <i class="fas fa-book-open"></i> Conteúdos
        </a>
        
        <!-- Link "Auto Cuidado" agora é dinâmico -->
        <a href="<?php echo $linkAutoCuidado; ?>" class="<?php echo ($paginaAtiva === 'auto-cuidado') ? 'active' : ''; ?>">
            <i class="fas fa-heart"></i> 
            <!-- O texto também muda -->
            <?php echo ($perfil === 'voluntario') ? 'Acompanhamento' : 'Auto Cuidado'; ?>
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
            <img src="../assets/img/contrast.png" alt="Ícone de contraste" class="contrast-icon" id="contrast-icon-img">
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