<?php
function validarNome($nome)
{
    // Remove espaços no início/fim
    $nome = trim($nome);

    // Remove tudo que não for letra ou espaço
    $nome = preg_replace('/[^\\p{L} ]/u', '', $nome);

    // Se após a limpeza não sobrar nada válido
    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 50) {
        return false;
    }

    // Formata para primeira letra maiúscula em cada palavra
    $nome = mb_convert_case($nome, MB_CASE_TITLE, "UTF-8");

    return $nome;
}

function validarEmail($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}


function validarSenha($senha)
{
    // Pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha)) {
        return false;
    }

    return true;
}

?>