<?php
$titulo = "Relatórios Anuais";
require_once "includes/layout/inicio.php";

// Busca os relatórios do usuário logado
$sql = "SELECT id, ano, receitas_total, despesas_total, saldo_total, status, data_geracao 
        FROM relatorios_anuais 
        WHERE usuario_id = ? 
        ORDER BY ano DESC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
?>
<main>
    <header class="header-dashboard px-7 flex-col lg:flex-row">
        <div class="txt-header self-start">
            <h2>Meus Relatórios Anuais</h2>
            <p>Acompanhe o fechamento de caixa de cada ano anterior.</p>
        </div>
    </header>

    <div class="p-5 lg:p-7 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <div class="bg-white rounded-xl border border-borda p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <h3 class="text-xl font-bold text-gray-800">Ano de <?= htmlspecialchars($row['ano']) ?></h3>
                        <?php if ($row['status'] == 'GERADO'): ?>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-100 text-emerald-600">
                                Concluído
                            </span>
                        <?php else: ?>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-red-100 text-red-600">
                                Falhou
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Receitas:</span>
                            <span class="font-medium text-emerald-500"><?= formatarReais($row['receitas_total']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Despesas:</span>
                            <span class="font-medium text-red-500"><?= formatarReais($row['despesas_total']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t border-gray-50 mt-1">
                            <span class="text-gray-500 font-semibold">Saldo:</span>
                            <span class="font-bold <?= $row['saldo_total'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                <?= formatarReais($row['saldo_total']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 flex items-center justify-between">
                        <span class="text-xs text-gray-400">
                            Gerado em <?= date('d/m/Y', strtotime($row['data_geracao'])) ?>
                        </span>
                        
                        <?php if ($row['status'] == 'GERADO'): ?>
                            <a href="<?= BASE_URL ?>relatorio?id=<?= $row['id'] ?>" class="text-sm font-semibold text-verde hover:text-verde-hover flex items-center gap-1 bg-verde/10 px-3 py-1.5 rounded-lg transition-colors">
                                Visualizar <i class="bi bi-arrow-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full flex flex-col items-center justify-center p-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <i class="bi bi-journal-x text-4xl text-gray-400 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-600">Nenhum relatório encontrado</h3>
                <p class="text-sm text-gray-500 text-center max-w-md">Os relatórios são gerados automaticamente na virada de cada ano.</p>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require_once "includes/layout/fim.php" ?>
