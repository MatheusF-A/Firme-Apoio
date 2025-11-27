/* --- Seleção dos Elementos do DOM --- */
document.addEventListener('DOMContentLoaded', () => {

    const senhaInput = document.getElementById('senha');
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    const erroConfirmarSenha = document.getElementById('erro-confirmar-senha');
    const cpfInput = document.getElementById('cpf');
    const telefoneInput = document.getElementById('telefone');
    const toggleSenhaBtn = document.getElementById('toggle-senha');
    const toggleConfirmarSenhaBtn = document.getElementById('toggle-confirmar-senha');

    /* --- Funções de Validação --- */
    function verificaSenhas() {
        if (confirmarSenhaInput.value.length === 0 && senhaInput.value.length === 0) {
            erroConfirmarSenha.textContent = '';
            return;
        }
        if (senhaInput.value !== confirmarSenhaInput.value) {
            erroConfirmarSenha.textContent = 'As senhas não coincidem.';
        } else {
            erroConfirmarSenha.textContent = '';
        }
    }

    /* --- Funções de Formatação --- */
    function formatarTelefone(e) {
        let v = e.target.value.replace(/\D/g, '');
        v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
        v = v.replace(/(\d{5})(\d)/, '$1-$2');
        v = v.slice(0, 15);
        e.target.value = v;
    }

    function formatarCPF(e) {
        let v = e.target.value.replace(/\D/g, '');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        v = v.slice(0, 14);
        e.target.value = v;
    }

    /* --- Funções de Toggle (Visibilidade da Senha) --- */
    function toggleSenha(input, icon) {
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    /* --- Event Listeners --- */
    senhaInput.addEventListener('input', verificaSenhas);
    confirmarSenhaInput.addEventListener('input', verificaSenhas);

    if (cpfInput) {
        cpfInput.addEventListener('input', formatarCPF);
    }
    if (telefoneInput) {
        telefoneInput.addEventListener('input', formatarTelefone);
    }

    if (toggleSenhaBtn) {
        toggleSenhaBtn.addEventListener('click', () => {
            toggleSenha(senhaInput, toggleSenhaBtn.querySelector('i'));
        });
    }

    if (toggleConfirmarSenhaBtn) {
        toggleConfirmarSenhaBtn.addEventListener('click', () => {
            toggleSenha(confirmarSenhaInput, toggleConfirmarSenhaBtn.querySelector('i'));
        });
    }
});