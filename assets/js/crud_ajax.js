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
                // Substitui o modal inteiro
                modalAtual.parentNode.replaceChild(modalNovo, modalAtual);

                // Re-executa os scripts do modal
                const scripts = modalNovo.querySelectorAll("script");
                scripts.forEach(script => {
                    const novoScript = document.createElement("script");
                    novoScript.textContent = script.textContent;
                    document.body.appendChild(novoScript);
                });
            }

            // Atualiza os modais de visualização (avisos admin)
            doc.querySelectorAll(".modal-visualizar").forEach(modalNovo => {
                const id = modalNovo.id;
                const modalExistente = document.getElementById(id);
                if (modalExistente) {
                    modalExistente.parentNode.replaceChild(modalNovo.cloneNode(true), modalExistente);
                } else {
                    // Se é um modal novo, adiciona ao body
                    const mainEl = document.querySelector("main");
                    if (mainEl && mainEl.nextSibling) {
                        mainEl.parentNode.insertBefore(modalNovo.cloneNode(true), mainEl.nextSibling);
                    }
                }
            });

            // Re-attach os event listeners de delete nos novos forms
            attachDeleteListeners();
            
            // Re-attach o listener do modal form no novo modal
            attachModalFormListener();

            // Re-attach o listener do trocarStatus se existir na página de despesas
            reattachTrocarStatus(doc);

        } catch (erro) {
            console.error("Erro ao recarregar conteúdo:", erro);
            // Fallback: recarrega a página inteira
            window.location.reload();
        }
    }

    /**
     * Re-attach a função trocarStatus para a página de despesas
     */
    function reattachTrocarStatus(doc) {
        // Verifica se existe a função trocarStatus (página de despesas)
        const scriptsDespesas = doc.querySelectorAll("main + script, main script");
        scriptsDespesas.forEach(script => {
            if (script.textContent.includes("trocarStatus")) {
                // A função trocarStatus já está definida globalmente, 
                // os botões com onclick já apontam para ela
            }
        });
    }

    // ========== INTERCEPTAÇÃO DO MODAL FORM (CADASTRAR / EDITAR) ==========

    /**
     * Intercepta o submit do modal form
     */
    function attachModalFormListener() {
        const modalForm = document.getElementById("modal-form");
        if (!modalForm) return;

        modalForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const form = e.target;
            const action = form.action;
            const formData = new FormData(form);

            // Desabilita o botão de submit para evitar duplo clique
            const btnSubmit = form.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.textContent = "Enviando...";
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
                    btnSubmit.textContent = "Enviar";
                }

                // Esconde o loader global (exibido pelo loading.js no submit)
                const loader = document.getElementById("container-loading");
                if (loader) loader.classList.add("hidden");
                document.documentElement.classList.remove("loading");
            }
        });
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

        // Desabilita o botão
        const btnDeleta = form.querySelector(".btn-deleta");
        if (btnDeleta) btnDeleta.disabled = true;

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
            if (btnDeleta) btnDeleta.disabled = false;
            
            // Esconde o loader global
            const loader = document.getElementById("container-loading");
            if (loader) loader.classList.add("hidden");
            document.documentElement.classList.remove("loading");
        }
    }

    // ========== INICIALIZAÇÃO ==========
    attachModalFormListener();
    attachDeleteListeners();
});
