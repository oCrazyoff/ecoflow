<?php
session_start();
require_once "funcoes_auth.php";
require_once "validacoes_login.php";
require_once "backend/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(strip_tags($_POST['email']));
    $senha = trim($_POST["senha"]);

    // Validações Iniciais
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Email inválido!";
        header("Location: " . BASE_URL . "login");
        exit;
    }

    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Método invalido!";
        header("Location: " . BASE_URL . "login");
        exit;
    }

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

            if (password_verify($senha, $senha_db)) {
                // Login com sucesso
                $_SESSION["id"] = $id;
                $_SESSION["nome"] = $nome;
                $_SESSION["email"] = $email;
                $_SESSION["cargo"] = $cargo;
                $_SESSION['resposta'] = "Bem Vindo! " . $_SESSION['nome'];

                // verificações antes do login
                if ($cargo == 0 || $cargo == 1) {

                    $hoje = new DateTime();
                    // Garante data válida para usuários antigos/novos
                    $ultimaVerificacao = new DateTime($ultima_verificacao_db ?? '1970-01-01');
                    $relatorioPendente = (bool)$relatorio_pendente_db;

                    // 1. VERIFICAÇÃO DE INÍCIO DE ANO (Relatório Anual)
                    if ($ultimaVerificacao->format('Y') < $hoje->format('Y')) {
                        $stmtUpdate = $conexao->prepare("UPDATE usuarios SET relatorio_anual_pendente = 1 WHERE id = ?");
                        $stmtUpdate->bind_param("i", $id);
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
                        $relatorioPendente = true;
                    }

                    // 2. VERIFICAÇÃO DE RECORRENTES (Despesas/Rendas)
                    // Só executa se o mês atual for maior que o mês da última verificação
                    if ($ultimaVerificacao->format('Y-m') < $hoje->format('Y-m')) {

                        // Executa a função de gerar despesas e rendas
                        verificarRecorrentes($id);
                    }

                    // 3. ATUALIZA A DATA DA ÚLTIMA VERIFICAÇÃO PARA "AGORA"
                    $stmtUpdateData = $conexao->prepare("UPDATE usuarios SET ultima_verificacao = NOW() WHERE id = ?");
                    $stmtUpdateData->bind_param("i", $id);
                    $stmtUpdateData->execute();
                    $stmtUpdateData->close();
                }

                // redirecionamento
                if ($cargo == 0) {
                    // Usuário Comum: Verifica se precisa ver o relatório anual
                    if ($relatorioPendente) {
                        $_SESSION['relatorio_pendente'] = true;
                        header("Location: " . BASE_URL . "relatorio");
                        exit;
                    } else {
                        header("Location: " . BASE_URL . "dashboard");
                        exit;
                    }
                } elseif ($cargo == 1) {
                    // Administrador: Vai direto para o dashboard
                    header("Location: " . BASE_URL . "dashboard");
                    exit;
                } else {
                    // Cargo desconhecido
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
            // Tratamento de Erros
            switch ($erro->getCode()) {
                case 1136:
                    $_SESSION['resposta'] = "Quantidade de dados inseridos inválida!";
                    header("Location: " . BASE_URL . "login");
                    exit;
                default:
                    // Opcional: Logar o erro real em arquivo para debug
                    // error_log($erro->getMessage());
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
