// assets/js/validation.js

// Função centralizada para exibir SweetAlert
function showSweetAlert(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonColor: '#8A2BE2' // Cor do botão no SweetAlert
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();
            
            // 1. Impedir envio com campos vazios (Requisito ACME)
            if (email === '' || senha === '') {
                e.preventDefault(); // Impede o envio do formulário
                showSweetAlert('Campos Vazios', 'Por favor, preencha todos os campos para continuar.', 'warning');
                return;
            }
            
            // 2. Simulação de Detecção de XSS (Front-end básico)
            // Uma verificação simples de caracteres perigosos
            const xssPattern = /[<>"'`]/;
            if (xssPattern.test(email) || xssPattern.test(senha)) {
                e.preventDefault();
                showSweetAlert('Input Inválido', 'Tentativa de entrada inválida detectada. Remova caracteres especiais.', 'error');
                return;
            }

            // Se as validações passarem, o formulário é enviado para processa_login.php
        });
    }

    // Adicione a mesma lógica de validação para o formulário de Cadastro (se tiver um 'registerForm' no cadastro.php)
});