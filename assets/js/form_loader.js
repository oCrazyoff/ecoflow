document.addEventListener("DOMContentLoaded", () => {
    // Intercepta todos os formulários que não sejam interceptados pelo crud_ajax
    // (crud_ajax já impede a propagação com preventDefault, mas para evitar 
    // conflitos e duplos spinners, focamos em forms específicos ou que não sejam 'ajax')
    const forms = document.querySelectorAll("form:not([data-ajax='true'])");

    forms.forEach(form => {
        // Se for form de delete ou modal-form (tratados via AJAX pelo crud_ajax.js), ignora.
        if (form.querySelector('.btn-deleta') || form.id === 'modal-form' || form.dataset.ajax === 'true') return;

        form.addEventListener("submit", function (e) {
            // Em HTML5, se o formulário for inválido, o evento submit não é acionado
            // a não ser que tenha novalidate. Assumindo que chegou aqui, o form será enviado.

            const btnSubmit = form.querySelector('button[type="submit"]');
            if (btnSubmit && !btnSubmit.dataset.loading) {
                // Marca o botão para evitar cliques duplos visuais
                btnSubmit.dataset.loading = "true";
                const originalContent = btnSubmit.innerHTML;
                
                // Se o formulário tiver validação JS customizada que previna o submit, 
                // não devemos mexer no botão. Como os forms de Login/Cadastro/Extrato 
                // não previnem default, o navegador vai navegar de página imediatamente.
                
                btnSubmit.innerHTML = `<i class="bi bi-arrow-repeat inline-block animate-spin"></i> ` + originalContent;
                btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
                // Impede múltiplos cliques
                btnSubmit.style.pointerEvents = "none";
                
                // O navegador cuidará do redirecionamento após o POST.
                // Se a página for cancelada ou não recarregar, o botão fica assim.
                // Mas num submit normal (POST -> recarregamento), está seguro.
            }
        });
    });
});
