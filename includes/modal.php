<?php
// caso tenha mês selecionado ele é inserido nos inputs data
$ano = date('Y');
$mes = $m ?? date('m');
$dia = date('d');
?>
<!--modal-->
<div id="modal" class="hidden">
    <div id="form-container">
        <h2 id="modal-title" class="text-xl font-bold mb-4"></h2>
        <form id="modal-form" action="#" method="POST">
            <!--CSRF-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
            <?php
            if (isset($tipo_modal)):

                // modal de rendas
                if ($tipo_modal == 'rendas'): ?>

                    <!--conteudo do formulario-->
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="input-modal" placeholder="Ex: Salário" required>
                    <label for="valor">Valor</label>
                    <input type="text" name="valor" id="valor" class="input-modal" placeholder="0,00" inputmode="numeric">
                    <label for="recorrente">Recorrente</label>
                    <select class="input-modal" name="recorrente" id="recorrente">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                    <label for="data">Data</label>
                    <input class="input-modal" type="date" name="data" id="data"
                        value="<?= sprintf('%04d-%02d-%02d', $ano, $mes, $dia) ?>">

                <?php elseif

                // modal de despesas
                ($tipo_modal == 'despesas'): ?>

                    <!--conteudo do formulario-->
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="input-modal" placeholder="Ex: Conta de Luz"
                        required>
                    <label for="status">Status</label>
                    <select class="input-modal" name="status" id="status">
                        <option value="0">Pendente</option>
                        <option value="1">Pago</option>
                    </select>
                    <label for="valor">Valor</label>
                    <input type="text" name="valor" id="valor" class="input-modal" placeholder="0,00" inputmode="numeric">
                    <label for="recorrente">Recorrente</label>
                    <select class="input-modal" name="recorrente" id="recorrente">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                    <label for="categoria">Categoria</label>
                    <select class="input-modal" name="categoria_id" id="categoria_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php
                        // Verifica se existe uma categoria selecionada (caso de edição)
                        // Se não existir (caso de cadastro), definimos como null
                        $categoria_selecionada = isset($row['categoria_id']) ? $row['categoria_id'] : null;

                        $sql_todas_cat = "SELECT id, nome FROM categorias WHERE usuario_id = ? ORDER BY nome ASC";

                        if ($stmt_cat = $conexao->prepare($sql_todas_cat)) {
                            $stmt_cat->bind_param("i", $_SESSION['id']);
                            $stmt_cat->execute();
                            $result_cat = $stmt_cat->get_result();

                            if ($result_cat->num_rows > 0) {
                                while ($cat = $result_cat->fetch_assoc()) {
                                    // Comparamos o ID da categoria da despesa com o ID da lista de categorias
                                    $selected = ($cat['id'] == $categoria_selecionada) ? 'selected' : '';
                                    echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['nome']) . "</option>";
                                }
                            }
                            $stmt_cat->close();
                        } else {
                            // Log de erro caso a query falhe
                            echo "<option value=''>Erro ao carregar categorias</option>";
                        }
                        ?>
                    </select>
                    <label for="data">Data</label>
                    <input class="input-modal" type="date" name="data" id="data"
                        value="<?= sprintf('%04d-%02d-%02d', $ano, $mes, $dia) ?>">

                <?php elseif

                // modal de usuarios
                ($tipo_modal == 'usuarios'): ?>

                    <!-- conteudo do formulário -->
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="input-modal" placeholder="Nome do usuário" required>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="input-modal" placeholder="Email do usuário" required>
                    <label for="cargo">Cargo</label>
                    <select name="cargo" id="cargo" class="input-modal">
                        <option value="0">Comum</option>
                        <option value="1">Adm</option>
                    </select>
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" class="input-modal" placeholder="Sua senha">

                <?php elseif

                // modal de avisos
                ($tipo_modal == 'avisos'): ?>

                    <!-- conteudo do formulário -->
                    <label for="titulo">Titulo</label>
                    <input type="text" name="titulo" id="titulo" class="input-modal" placeholder="Digite o titulo do aviso"
                        required>
                    <label for="conteudo">Conteudo</label>
                    <textarea type="text" name="conteudo" id="conteudo" class="input-modal" placeholder="Conteudo do aviso"
                        required></textarea>

                <?php elseif

                // modal de categorias
                ($tipo_modal == 'categorias'): ?>

                    <!-- conteudo do formulário -->
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="input-modal" placeholder="Digite o nome da categoria"
                        required>

                <?php endif; ?>
            <?php endif; ?>
            <div class="grid grid-cols-2 gap-2 mt-5">
                <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn-submit">Enviar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // funções do modal

    // digitação dinamica valor
    const inputValor = document.getElementById('valor');

    inputValor.addEventListener('input', function() {
        // Remove tudo que não for número
        let valor = this.value.replace(/\D/g, '');

        // Divide por 100 pra ter as casas decimais
        valor = (valor / 100).toFixed(2) + '';

        // Troca o ponto por vírgula e adiciona separador de milhar
        valor = valor.replace('.', ',');
        valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Atualiza o campo
        this.value = valor;
    });

    // Capitaliza a primeira letra
    function capitalizarPrimeiraLetra(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function abrirCadastrarModal(tabela) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('modal-form');

        modal.classList.remove('hidden');
        document.getElementById('modal-title').textContent = `Cadastrar ${capitalizarPrimeiraLetra(tabela)}`;

        // limpa campos do form
        form.reset();

        // altera action do form para o PHP de cadastro
        form.action = `cadastrar_${tabela}`;
    }

    async function abrirEditarModal(tabela, id) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('modal-form');
        const modalTitle = document.getElementById('modal-title');

        // Mostrar modal imediatamente
        modal.classList.remove('hidden');

        // Coloca título temporário
        modalTitle.textContent = "Carregando...";

        // Altera action do form
        form.action = `editar_${tabela}?id=${id}`;

        try {
            // Busca os dados
            const resp = await fetch(`buscar_${tabela}?id=${id}`);
            const dados = await resp.json();

            // Preenche os campos do form
            for (const campo in dados) {
                if (form[campo]) form[campo].value = dados[campo];
            }

            // Atualiza título com o correto
            modalTitle.textContent = `Editar ${capitalizarPrimeiraLetra(tabela)}`;
        } catch (erro) {
            modalTitle.textContent = `Erro ao carregar ${capitalizarPrimeiraLetra(tabela)}`;
            console.error("Erro ao buscar dados:", erro);
        }
    }

    function fecharModal(tabela) {
        document.getElementById(`modal`).classList.add('hidden');
    }
</script>