<link rel="stylesheet" href="../assets/css/seletor_data.css?v=<?php echo time(); ?>">
<div class="data-container">
    <button id="botao_mes"></button>
    <ul id="lista_meses">
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

        $resultado_meses = $conn->query($sql_meses);

        // Verifica e exibe os meses encontrados
        if ($resultado_meses && $resultado_meses->num_rows > 0) {
            while ($linha_meses = $resultado_meses->fetch_assoc()) {
                $mes = (int)$linha_meses['mes'];
                $classe = ($mes === $mes_selecionado - 1) ? ' class="selecionado"' : '';
                echo "<li data-mes=\"$mes\"$classe>{$meses[$mes]}</li>";
            }
        } else {
            echo "<li style='color: gray;'>Nenhum mês encontrado</li>";
        }
        ?>
    </ul>

</div>
<script defer>
    const mes_selecionado_php = <?php echo $mes_selecionado - 1; ?>;
    const nomes_meses = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    const parametros_url = new URLSearchParams(window.location.search);
    const mes_selecionado = parametros_url.has('mes') ? parseInt(parametros_url.get('mes')) : mes_selecionado_php;

    const botao_mes = document.getElementById('botao_mes');
    const lista_meses = document.getElementById('lista_meses');
    const itens_meses = lista_meses.querySelectorAll('li');

    botao_mes.innerHTML =
        `<i class="bi bi-caret-down-fill"></i> ${nomes_meses[mes_selecionado_php]}`;

    botao_mes.addEventListener('click', () => {
        lista_meses.style.display = lista_meses.style.display === 'none' ? 'block' : 'none';
    });

    document.addEventListener('click', (evento) => {
        if (!botao_mes.contains(evento.target) && !lista_meses.contains(evento.target)) {
            lista_meses.style.display = 'none';
        }
    });

    itens_meses.forEach((item) => {
        item.addEventListener('click', () => {
            const mes_selecionado = item.getAttribute('data-mes');
            botao_mes.innerHTML =
                `<i class="bi bi-caret-down-fill"></i> ${nomes_meses[mes_selecionado]}`;
            lista_meses.style.display = 'none';

            const url = new URL(window.location.href);
            url.searchParams.set('mes', parseInt(mes_selecionado) + 1);
            window.location.href = url.toString();
        });
    });
</script>