<div id="modal-extrato">
    <div class="container-modal">
        <div class="titulo-modal">
            <h2><i class="bi bi-file-earmark-text"></i> Importar Extrato Bancário</h2>
            <p>
                Cole o conteúdo do extrato ou envie um arquivo CSV/TXT. A IA irá identificar as transações
                automaticamente.
            </p>
        </div>
        <div class="info-modal">
            <i class="bi bi-info-circle"></i>
            <p>A I.A cadastrará apenas informações do ano atual. Ela pode cometer erros, portanto revise os dados
                gerados.</p>
        </div>
        <form class="form-container" action="processar_extrato" method="post">
            <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
            <div class="input-group">
                <label for="arquivo"><i class="bi bi-upload"></i> Enviar arquivo (CSV, TXT, OFX)</label>
                <input type="file" name="arquivo" id="arquivo" accept=".csv,.txt,.ofx">
            </div>

            <p>OU COLE O CONTEÚDO</p>

            <textarea id="conteudo_extrato" name="conteudo_extrato"
                placeholder="Cole o conteúdo do extrato aqui...&#10;&#10;Exemplo:&#10;01/03/2025 PIX RECEBIDO - JOÃO +1.500,00&#10;05/03/2025 IFOOD RESTAURANTE -32,00&#10;21/03/2025 NETFLIX -45,00"></textarea>
            <div class="container-btn">
                <button class="btn-cancelar" onclick="fecharModalExtrato()">Cancelar</button>
                <button type="submit" class="btn-submit">Analisar com IA</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const arquivoInput = document.getElementById("arquivo");
        const conteudoTextarea = document.getElementById("conteudo_extrato");

        if (!arquivoInput || !conteudoTextarea) return;

        arquivoInput.addEventListener("change", () => {
            const file = arquivoInput.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = () => {
                conteudoTextarea.value = reader.result || "";
            };
            reader.onerror = () => {
                console.error("Erro ao ler arquivo", reader.error);
            };
            reader.readAsText(file, "UTF-8");
        });
    });

    // lógica de abrir e fechar o modal
    function mostrarModalExtrato() {
        const modal = document.getElementById("modal-extrato");
        modal.classList.add("flex");
    }

    function fecharModalExtrato() {
        const modal = document.getElementById("modal-extrato");
        modal.classList.remove("flex");
    }
</script>