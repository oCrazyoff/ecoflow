document.addEventListener('DOMContentLoaded', function () {

    // Seleciona os elementos do menu
    const toggleButton = document.getElementById('toggle-menu-btn');
    const menu = document.querySelector('.menu');

    // Chave para salvar o estado no localStorage
    const storageKey = 'menu_retraido';

    // Verifica se o estado 'retraido' está salvo no localStorage
    if (localStorage.getItem(storageKey) === 'true') {
        // Se estiver, aplica a classe ao menu
        menu.classList.add('retraido');
    }

    if (toggleButton && menu) {
        toggleButton.addEventListener('click', function () {
            // Alterna a classe visualmente
            menu.classList.toggle('retraido');

            // Agora, atualiza o localStorage com o novo estado
            if (menu.classList.contains('retraido')) {
                // Se o menu AGORA TEM a classe, salva 'true'
                localStorage.setItem(storageKey, 'true');
                console.log('Menu retraído e estado salvo.');
            } else {
                // Se o menu NÃO TEM mais a classe, remove a chave do storage
                localStorage.removeItem(storageKey);
                console.log('Menu expandido e estado limpo.');
            }
        });
    }
});