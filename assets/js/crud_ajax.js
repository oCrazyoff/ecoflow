/**
 * CRUD AJAX - Intercepta formulários de cadastro, edição e exclusão
 * para enviar via fetch sem recarregar a página.
 */
document.addEventListener("DOMContentLoaded", () => {

    // ========== UTILITÁRIOS ==========

    /**
     * Mostra a mensagem de resposta (reutiliza o div-erro existente)
     */
    function mostrarMensagem(mensagem) {
        // Remove div de erro existente se houver
        const existente = document.getElementById("div-erro");
        if (existente) existente.remove();

        // Cria novo div de mensagem
        const div = document.createElement("div");
        div.id = "div-erro";
        div.style.zIndex = "9999";
        div.innerHTML = `<i class="bi bi-info-circle-fill"></i> ${mensagem}`;
        document.body.appendChild(div);

        // Força reflow para a animação funcionar
        div.offsetHeight;
        div.classList.add("show");

        // Remove após 5 segundos
        setTimeout(() => {
            div.classList.remove("show");
            setTimeout(() => div.remove(), 400);
        }, 5000);
    }

    /**
     * Re-inicializa os event listeners dos toggle buttons do modal
     */
    function reattachToggleListeners() {
        document.querySelectorAll('.toggle-group').forEach(group => {
            group.querySelectorAll('.toggle-btn').forEach(btn => {
                // Clona e substitui para remover listeners antigos
                const novoBotao = btn.cloneNode(true);
                btn.parentNode.replaceChild(novoBotao, btn);
            });

            group.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    group.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const hiddenInput = group.previousElementSibling;
                    if (hiddenInput && hiddenInput.type === 'hidden') {
                        hiddenInput.value = this.dataset.value;
                    }

                    // Lógica condicional: Parcelado <-> Recorrente
                    if (group.id === 'toggle-parcelado') {
                        const containerRecorrente = document.getElementById('container-recorrente');
                        const containerParcelas = document.getElementById('container-parcelas');
                        if (this.dataset.value === '1') {
                            if (containerRecorrente) containerRecorrente.classList.add('hidden');
                            if (containerParcelas) containerParcelas.classList.remove('hidden');
                            // Reseta recorrente para 0
                            const recorrenteInput = document.getElementById('recorrente');
                            if (recorrenteInput) recorrenteInput.value = '0';
                            window.atualizarPreviewParcelasCadastro?.();
                        } else {
                            if (containerRecorrente) containerRecorrente.classList.remove('hidden');
                            if (containerParcelas) containerParcelas.classList.add('hidden');
                            const numParcelas = document.getElementById('num_parcelas');
                            if (numParcelas) numParcelas.value = '';
                            const previewParcelas = document.getElementById('preview-parcelas');
                            if (previewParcelas) previewParcelas.classList.add('hidden');
                        }
                    }

                    if (group.id === 'toggle-recorrente') {
                        const containerParcelado = document.getElementById('container-parcelado-wrapper');
                        const containerParcelas = document.getElementById('container-parcelas');
                        if (this.dataset.value === '1') {
                            if (containerParcelado) containerParcelado.classList.add('hidden');
                            if (containerParcelas) containerParcelas.classList.add('hidden');
                            const parceladoInput = document.getElementById('parcelado');
                            if (parceladoInput) parceladoInput.value = '0';
                        } else {
                            if (containerParcelado) containerParcelado.classList.remove('hidden');
                        }
                    }
                });
            });
        });
    }

    /**
     * Recarrega o conteúdo da página atual sem reload completo
     */
    async function recarregarConteudo() {
        try {
            const resp = await fetch(window.location.href);
            const html = await resp.text();

            // Parse o HTML retornado
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            // Atualiza o conteúdo principal (a tag <main>)
            const mainAtual = document.querySelector("main");
            const mainNovo = doc.querySelector("main");

            if (mainAtual && mainNovo) {
                mainAtual.innerHTML = mainNovo.innerHTML;
            }

            // Atualiza o modal (se existir)
            const modalAtual = document.getElementById("modal");
            const modalNovo = doc.getElementById("modal");

            if (modalAtual && modalNovo) {
                // Substitui apenas o innerHTML do modal para manter o nó no DOM
                modalAtual.innerHTML = modalNovo.innerHTML;
                // Garante que o modal fique escondido após a atualização
                modalAtual.classList.add("hidden");
            }

            // Atualiza os modais de visualização (avisos admin)
            doc.querySelectorAll(".modal-visualizar").forEach(modalVisualizarNovo => {
                const id = modalVisualizarNovo.id;
                const modalExistente = document.getElementById(id);
                if (modalExistente) {
                    modalExistente.parentNode.replaceChild(modalVisualizarNovo.cloneNode(true), modalExistente);
                } else {
                    // Se é um modal novo, adiciona ao body
                    const mainEl = document.querySelector("main");
                    if (mainEl && mainEl.nextSibling) {
                        mainEl.parentNode.insertBefore(modalVisualizarNovo.cloneNode(true), mainEl.nextSibling);
                    }
                }
            });

            // Re-attach os event listeners de delete nos novos forms
            attachDeleteListeners();
            
            // Re-attach o listener do modal form no novo modal
            attachModalFormListener();

            // Re-attach os toggle listeners do modal
            reattachToggleListeners();

        } catch (erro) {
            console.error("Erro ao recarregar conteúdo:", erro);
            // Fallback: recarrega a página inteira
            window.location.reload();
        }
    }



    // ========== INTERCEPTAÇÃO DO MODAL FORM (CADASTRAR / EDITAR) ==========

    /**
     * Intercepta o submit do modal form
     */
    function attachModalFormListener() {
        const modalForm = document.getElementById("modal-form");
        if (!modalForm) return;

        modalForm.removeEventListener("submit", handleModalFormSubmit);
        modalForm.addEventListener("submit", handleModalFormSubmit);
    }

    /**
     * Handler para submit do modal form
     */
    async function handleModalFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const action = form.action;
        const formData = new FormData(form);

        // Desabilita o botão de submit para evitar duplo clique e adiciona spinner
        const btnSubmit = form.querySelector('button[type="submit"]');
        let originalBtnContent = "";
        if (btnSubmit) {
            if (btnSubmit.disabled) return;

            // Remove qualquer spinner residual se houver para evitar duplicação
            const tempDiv = document.createElement("div");
            tempDiv.innerHTML = btnSubmit.innerHTML;
            tempDiv.querySelectorAll(".bi-arrow-repeat.animate-spin").forEach(icon => icon.remove());
            originalBtnContent = tempDiv.innerHTML.trim();

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i class="bi bi-arrow-repeat inline-block animate-spin"></i> ` + originalBtnContent;
            btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
        }

        try {
            const resp = await fetch(action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formData
            });

            const data = await resp.json();

            // Mostra a mensagem
            if (data.mensagem) {
                mostrarMensagem(data.mensagem);
            }

            if (data.sucesso) {
                // Fecha o modal
                const modal = document.getElementById("modal");
                if (modal) modal.classList.add("hidden");

                // Recarrega o conteúdo
                await recarregarConteudo();
            }

        } catch (erro) {
            console.error("Erro no envio do formulário:", erro);
            mostrarMensagem("Erro ao processar a requisição.");
        } finally {
            // Re-habilita o botão
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnContent;
                btnSubmit.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        }
    }

    // ========== INTERCEPTAÇÃO DOS FORMULÁRIOS DE DELETE ==========

    /**
     * Intercepta os formulários de exclusão (botão lixeira nas tabelas)
     */
    function attachDeleteListeners() {
        // Seleciona todos os forms que contenham um botão .btn-deleta
        const deleteForms = document.querySelectorAll("form:has(.btn-deleta)");

        deleteForms.forEach(form => {
            // Remove listener antigo para evitar duplicação
            form.removeEventListener("submit", handleDeleteSubmit);
            form.addEventListener("submit", handleDeleteSubmit);
        });
    }

    /**
     * Handler para submit de delete
     */
    async function handleDeleteSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const action = form.action;
        const formData = new FormData(form);

        // Desabilita o botão e adiciona spinner
        const btnDeleta = form.querySelector(".btn-deleta");
        let originalBtnDeletaContent = "";
        if (btnDeleta) {
            originalBtnDeletaContent = btnDeleta.innerHTML;
            btnDeleta.disabled = true;
            btnDeleta.innerHTML = `<i class="bi bi-arrow-repeat inline-block animate-spin"></i>`;
            btnDeleta.classList.add('opacity-70', 'cursor-not-allowed');
        }

        try {
            const resp = await fetch(action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formData
            });

            const data = await resp.json();

            // Mostra a mensagem
            if (data.mensagem) {
                mostrarMensagem(data.mensagem);
            }

            if (data.sucesso) {
                // Animação de remoção da linha ou card
                const elemento = form.closest("tr") || form.closest(".bg-white.rounded-lg");
                if (elemento) {
                    elemento.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                    elemento.style.opacity = "0";
                    elemento.style.transform = "translateX(-20px)";

                    // Aguarda a animação e então recarrega o conteúdo
                    setTimeout(async () => {
                        await recarregarConteudo();
                    }, 300);
                } else {
                    await recarregarConteudo();
                }
            }

        } catch (erro) {
            console.error("Erro ao deletar:", erro);
            mostrarMensagem("Erro ao processar a exclusão.");
        } finally {
            if (btnDeleta) {
                btnDeleta.disabled = false;
                btnDeleta.innerHTML = originalBtnDeletaContent;
                btnDeleta.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        }
    }

    // ========== INICIALIZAÇÃO ==========
    attachModalFormListener();
    attachDeleteListeners();
});
