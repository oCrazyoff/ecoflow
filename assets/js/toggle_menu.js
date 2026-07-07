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

function toggleMenuGaveta() {
    const gaveta = document.getElementById('menu-gaveta');
    const overlay = document.getElementById('menu-gaveta-overlay');
    
    if (gaveta.classList.contains('translate-y-full')) {
        // Abrir gaveta
        overlay.classList.remove('hidden');
        // Usar setTimeout para permitir que o navegador processe a remoção do display:none antes de alterar a opacidade (transição suave)
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            gaveta.classList.remove('translate-y-full');
        }, 10);
    } else {
        // Fechar gaveta
        overlay.classList.add('opacity-0');
        gaveta.classList.add('translate-y-full');
        
        // Aguardar o tempo da transição para esconder o elemento
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, 300);
    }
}