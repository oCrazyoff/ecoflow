<?php
function validarNome(string $nome): string
{
    $nomeLimpo = trim($nome);
    $nomeLimpo = preg_replace('/\s+/', ' ', $nomeLimpo);

    if (
        empty($nomeLimpo) ||
        mb_strlen($nomeLimpo, 'UTF-8') < 3 ||
        mb_strlen($nomeLimpo, 'UTF-8') > 100 ||
        !preg_match("/^[a-zA-ZÀ-ú\s'-]+$/u", $nomeLimpo)
    ) {
        // Se qualquer validação falhar, retorna o valor padrão.
        return "Usuário";
    }

    $nomeFormatado = mb_convert_case($nomeLimpo, MB_CASE_TITLE, 'UTF-8');

    return $nomeFormatado;
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