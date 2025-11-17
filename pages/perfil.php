<?php
$titulo = "Perfil";
require_once "includes/layout/inicio.php"
?>
<main class="px-5 lg:px-10 py-5 pb-20 lg:pb-0">
    <h2 class="text-2xl font-bold">Perfil do Usuário</h2>
    <div class="card-perfil">
        <h3>Informações Pessoais</h3>
        <form action="alterar_info_perfil" method="POST">
            <!--csrf-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">

            <div class="side-input">
                <div class="input-group">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($_SESSION['nome']) ?>"
                        placeholder="Digite seu nome">
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($_SESSION['email']) ?>"
                        placeholder="Digite seu email">
                </div>
            </div>
            <div class="container-btn">
                <button>Salvar alterações</button>
            </div>
        </form>
    </div>
    <div class="card-perfil">
        <h3>Alterar Senha</h3>
        <form action="alterar_senha_perfil" method="POST">
            <!--csrf-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">

            <div class="input-group">
                <label for="senha-atual">Senha Atual</label>
                <input type="password" name="senha-atual" id="senha-atual" placeholder="•••••••">
            </div>
            <div class="side-input">
                <div class="input-group">
                    <label for="nova-senha">Nova Senha</label>
                    <input type="password" name="nova-senha" id="nova-senha" placeholder="•••••••">
                </div>
                <div class="input-group">
                    <label for="confirmar-senha">Confirmar Senha</label>
                    <input type="password" name="confirmar-senha" id="confirmar-senha" placeholder="•••••••">
                </div>
            </div>
            <div class="container-btn">
                <button>Atualizar senha</button>
            </div>
        </form>
    </div>
    <div class="card-perfil deslogar">
        <h3>Sair da Conta</h3>
        <p>Ao sair da conta, você precisará fazer login novamente para acessar o sistema.</p>
        <a href="deslogar"><i class="bi bi-arrow-bar-left"></i> Sair da conta</a>
    </div>
</main>
<?php require_once "includes/layout/fim.php" ?>