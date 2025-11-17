<?php
session_start();
require_once "funcoes_auth.php";
require_once "validacoes_login.php";
require_once "backend/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(strip_tags($_POST['email']));
    $senha = trim($_POST["senha"]);

    // Verificar o email
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Email inválido!";
        header("Location: " . BASE_URL . "login");
        exit;
    }

    //Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Método invalido!";
        header("Location: " . BASE_URL . "login");
        exit;
    }

    //Validadar senha
    if (validarSenha($senha) == false) {
        $_SESSION['resposta'] = "Senha inválida!";
        header("Location: " . BASE_URL . "login");
        exit;
    }

    if (!empty($email) && !empty($senha)) {
        try {
            $stmt = $conexao->prepare("SELECT id, nome, email, senha_hash, cargo, ultima_verificacao, relatorio_anual_pendente FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($id, $nome, $email, $senha_db, $cargo, $ultima_verificacao_db, $relatorio_pendente_db);

            $usuarioEncontrado = $stmt->fetch();
            $stmt->close();

            if (!$usuarioEncontrado) {
                $_SESSION['resposta'] = "E-mail ou senha incorretos!";
                header("Location: " . BASE_URL . "login");
                exit;
            }

            // verifica se a senha esta correta
            if (password_verify($senha, $senha_db)) {

                // atualiza as variaveis sessions
                $_SESSION["id"] = $id;
                $_SESSION["nome"] = $nome;
                $_SESSION["email"] = $email;
                $_SESSION["cargo"] = $cargo;

                $_SESSION['resposta'] = "Bem Vindo! " . $_SESSION['nome'];

                if ($cargo == 0) {
                    // caso o usuario for comum

                    $hoje = new DateTime();
                    // Garante que mesmo usuários novos (com data nula) passem pela verificação.
                    $ultimaVerificacao = new DateTime($ultima_verificacao_db ?? '1970-01-01');
                    $relatorioPendente = (bool)$relatorio_pendente_db;

                    // VERIFICAÇÃO DE INÍCIO DE ANO
                    if ($ultimaVerificacao->format('Y') < $hoje->format('Y')) {
                        $stmtUpdate = $conexao->prepare("UPDATE usuarios SET relatorio_anual_pendente = 1 WHERE id = ?");
                        $stmtUpdate->bind_param("i", $id);
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
                        $relatorioPendente = true; // Atualiza a variável local para o redirecionamento
                    }

                    // VERIFICAÇÃO DE RECORRENTES (Executa se o mês for diferente)
                    if ($ultimaVerificacao->format('Y-m') < $hoje->format('Y-m')) {
                        verificarRecorrentes($id);
                    }

                    // ATUALIZA A DATA DA ÚLTIMA VERIFICAÇÃO PARA AGORA
                    $stmtUpdateData = $conexao->prepare("UPDATE usuarios SET ultima_verificacao = NOW() WHERE id = ?");
                    $stmtUpdateData->bind_param("i", $id);
                    $stmtUpdateData->execute();
                    $stmtUpdateData->close();

                    // LÓGICA DE REDIRECIONAMENTO
                    if ($relatorioPendente) {
                        // Se há um relatório pendente, força o redirecionamento para a página de fechamento.
                        $_SESSION['relatorio_pendente'] = true;
                        header("Location: " . BASE_URL . "relatorio");
                        exit;
                    } else {
                        // Se não há pendências, segue para o dashboard.
                        header("Location: " . BASE_URL . "dashboard");
                        exit;
                    }
                } elseif ($cargo == 1) {
                    // apenas redireciona
                    header("Location: " . BASE_URL . "dashboard");
                    exit;
                } else {
                    // caso o usuario não tenha um cargo invalido
                    header("Location: " . BASE_URL . "login");
                    $_SESSION['resposta'] = "Cargo Invalido!";
                    exit;
                }
            } else {
                $_SESSION['resposta'] = "E-mail ou senha incorretos!";
                header("Location: " . BASE_URL . "login");
                exit;
            }
        } catch (Exception $erro) {
            // Caso houver erro ele retorna
            switch ($erro->getCode()) {
                // erro de quantidade de paramêtros erro
                case 1136:
                    $_SESSION['resposta'] = "Quantidade de dados inseridos inválida!";
                    header("Location: " . BASE_URL . "login");
                    exit;
                default:
                    $_SESSION['resposta'] = "Erro inesperado. Tente novamente.";
                    header("Location: " . BASE_URL . "login");
                    exit;
            }
        }
    } else {
        $_SESSION['resposta'] = "Preencha todas as informações!";
    }
} else {
    $_SESSION['resposta'] = "Variável POST ínvalida!";
}
header("Location: " . BASE_URL . "login");
exit;
