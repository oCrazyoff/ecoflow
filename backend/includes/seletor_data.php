<link rel="stylesheet" href="../assets/css/seletor_data.css?v=<?php echo time(); ?>">
<div class="data-container">
    <button id="monthButton"><i class="bi bi-caret-down-fill"></i> </button>
    <ul id="monthList" style="display: none;">
        <li data-month="0">Janeiro</li>
        <li data-month="1">Fevereiro</li>
        <li data-month="2">Março</li>
        <li data-month="3">Abril</li>
        <li data-month="4">Maio</li>
        <li data-month="5">Junho</li>
        <li data-month="6">Julho</li>
        <li data-month="7">Agosto</li>
        <li data-month="8">Setembro</li>
        <li data-month="9">Outubro</li>
        <li data-month="10">Novembro</li>
        <li data-month="11">Dezembro</li>
    </ul>
</div>
<script defer>
    const phpSelectedMonth = <?php echo $selectedMonth; ?>;
    // Obter o mês atual
    const monthNames = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    // Verificar se o parâmetro 'month' está presente na URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectedMonth = urlParams.has('month') ? parseInt(urlParams.get('month')) : phpSelectedMonth;

    const monthButton = document.getElementById('monthButton');
    const monthList = document.getElementById('monthList');
    const monthItems = monthList.querySelectorAll('li');

    // Exibir o mês selecionado no botão
    monthButton.innerHTML = `<i class="bi bi-caret-down-fill"></i> ${monthNames[phpSelectedMonth - 1]}`;

    // Alternar a exibição da lista de meses ao clicar no botão
    monthButton.addEventListener('click', () => {
        monthList.style.display = monthList.style.display === 'none' ? 'block' : 'none';
    });

    // Fechar a lista ao clicar fora dela
    document.addEventListener('click', (event) => {
        if (!monthButton.contains(event.target) && !monthList.contains(event.target)) {
            monthList.style.display = 'none';
        }
    });

    // Tornar os itens da lista clicáveis e enviar o mês selecionado ao backend
    monthItems.forEach((item) => {
        item.addEventListener('click', () => {
            const selectedMonth = item.getAttribute('data-month');
            monthButton.innerHTML =
                `<i class="bi bi-caret-down-fill"></i> ${monthNames[selectedMonth]}`;
            monthList.style.display = 'none';

            // Atualizar a página com o mês selecionado
            const url = new URL(window.location.href);
            url.searchParams.set('month', parseInt(selectedMonth) + 1); // Ajuste para PHP
            window.location.href = url.toString();
        });
    });
</script>