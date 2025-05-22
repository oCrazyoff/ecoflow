<link rel="stylesheet" href="../assets/css/seletor_data.css?v=<?php echo time(); ?>">
<div class="data-container">
    <button id="monthButton"></button>
    <ul id="monthList">
        <?php

        $meses = [
            "Janeiro",
            "Fevereiro",
            "Março",
            "Abril",
            "Maio",
            "Junho",
            "Julho",
            "Agosto",
            "Setembro",
            "Outubro",
            "Novembro",
            "Dezembro"
        ];

        $sql_meses = "SELECT DISTINCT mes FROM (
                        SELECT MONTH(data) - 1 AS mes FROM rendas
                        UNION
                        SELECT MONTH(data) - 1 AS mes FROM despesas
                        UNION
                        SELECT MONTH(data) - 1 AS mes FROM investimentos
                    ) AS todos_os_meses
                    ORDER BY mes;
                    ";

        $result_meses = $conn->query($sql_meses);

        // Verifica e exibe os meses encontrados
        if ($result_meses && $result_meses->num_rows > 0) {
            while ($row_meses = $result_meses->fetch_assoc()) {
                $mes = (int)$row_meses['mes'];
                $classe = ($mes === $selectedMonth - 1) ? ' class="selected"' : '';
                echo "<li data-month=\"$mes\"$classe>{$meses[$mes]}</li>";
            }
        } else {
            echo "<li style='color: gray;'>Nenhum mês encontrado</li>";
        }
        ?>
    </ul>

</div>
<script defer>
    const phpSelectedMonth = <?php echo $selectedMonth - 1; ?>;
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
    monthButton.innerHTML =
        `<i class="bi bi-caret-down-fill"></i> ${monthNames[phpSelectedMonth]}`;


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