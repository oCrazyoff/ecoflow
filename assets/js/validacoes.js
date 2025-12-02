document.addEventListener("DOMContentLoaded", () => {

    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("submit", (e) => {
            const erro = validarFormulario(form);

            if (erro) {
                e.preventDefault();
                window.formHasError = true;
                mostrarErro(erro);
            }
        });
    });

});


// ------------------------------
// VALIDADOR PRINCIPAL
// ------------------------------
function validarFormulario(form) {

    const campos = form.querySelectorAll("[required]");

    for (let campo of campos) {

        const nome = campo.name;
        const valor = campo.value.trim();

        // Campo vazio
        if (valor === "") {
            return `O campo "${nome}" é obrigatório.`;
        }

        // Validação por tipo
        if (nome.includes("email") || campo.type === "email") {
            if (!validarEmail(valor)) {
                return "E-mail inválido.";
            }
        }

        if (nome.includes("nome")) {
            if (!validarNome(valor)) {
                return "O nome deve conter pelo menos 3 letras e apenas caracteres válidos.";
            }
        }

        if (nome.includes("senha")) {
            if (!validarSenha(valor)) {
                return "A senha deve ter pelo menos 6 caracteres.";
            }
        }

        if (nome.includes("confirmar") && form.querySelector('[name="senha"]')) {
            const senha = form.querySelector('[name="senha"]').value;
            if (valor !== senha) {
                return "As senhas não coincidem.";
            }
        }

        if (campo.type === "number" || campo.dataset.tipo === "numero") {
            if (isNaN(valor) || Number(valor) < 0) {
                return `O valor no campo "${nome}" deve ser numérico e maior ou igual a zero.`;
            }
        }

        if (campo.type === "date" || campo.dataset.tipo === "data") {
            if (!validarData(valor)) {
                return `A data informada no campo "${nome}" é inválida.`;
            }
        }
    }

    return null; // Sem erros
}


// ------------------------------
// FUNÇÕES DE VALIDAÇÃO
// ------------------------------
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarNome(nome) {
    const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]{3,}$/;
    return regex.test(nome);
}

function validarSenha(senha) {
    return senha.length >= 6;
}

function validarData(dataStr) {
    const data = new Date(dataStr);
    return !isNaN(data.getTime());
}


// ------------------------------
// EXIBIR ERRO NA SUA DIV EXISTENTE
// ------------------------------
function mostrarErro(msg) {

    let erroDiv = document.getElementById("div-erro");

    // Se não existir
    if (!erroDiv) {
        erroDiv = document.createElement("div");
        erroDiv.id = "div-erro";
        document.body.prepend(erroDiv);
    }

    // Troca conteúdo
    erroDiv.innerHTML = `
        <i class="bi bi-info-circle-fill"></i>
        ${msg}
    `;

    // Remove classes de animação
    erroDiv.classList.remove(
        "animate-[show_5s]",
        "lg:animate-[show_5s]",
        "animate-[show-mobile_5s]"
    );

    // Força reflow
    void erroDiv.offsetWidth;

    // Reaplica classes de animação
    erroDiv.classList.add(
        "animate-[show_5s]",
        "lg:animate-[show_5s]",
        "animate-[show-mobile_5s]"
    );
}