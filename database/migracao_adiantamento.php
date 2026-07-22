<?php
/**
 * Script de migração: Adiciona novos campos para suporte a adiantamento de despesas recorrentes.
 * 
 * Este script deve ser executado UMA VEZ para:
 * 1. Adicionar os novos campos às tabelas despesas e rendas.
 * 2. Gerar UUIDs retroativos para despesas/rendas recorrentes existentes.
 * 
 * Uso: Acesse via navegador ou execute via CLI: php migracao_adiantamento.php
 */

require_once __DIR__ . '/../backend/conexao.php';

// Proteção: verificar se a migração já foi aplicada
$check = $conexao->query("SHOW COLUMNS FROM despesas LIKE 'recorrencia_grupo'");
if ($check->num_rows > 0) {
    echo "⚠️ Migração já aplicada. Os campos já existem na tabela.\n";
    
    // Verificar se precisa gerar UUIDs retroativos
    $pendentes = $conexao->query("SELECT COUNT(*) as total FROM despesas WHERE recorrente = 1 AND recorrencia_grupo IS NULL");
    $row = $pendentes->fetch_assoc();
    
    if ($row['total'] == 0) {
        echo "✅ Nenhum UUID retroativo pendente.\n";
        exit;
    }
    
    echo "🔄 Gerando UUIDs retroativos para {$row['total']} despesas...\n";
} else {
    echo "🔄 Iniciando migração...\n\n";
    
    // =========================================================================
    // ETAPA 1: Adicionar novos campos à tabela `despesas`
    // =========================================================================
    echo "📦 Etapa 1: Adicionando campos à tabela despesas...\n";

    $alteracoes_despesas = [
        "ALTER TABLE `despesas` ADD COLUMN `recorrencia_grupo` VARCHAR(36) DEFAULT NULL COMMENT 'UUID que agrupa instâncias da mesma despesa recorrente' AFTER `data`",
        "ALTER TABLE `despesas` ADD COLUMN `data_pagamento` DATE DEFAULT NULL COMMENT 'Data real do pagamento' AFTER `recorrencia_grupo`",
        "ALTER TABLE `despesas` ADD COLUMN `adiantamento_ref_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'Referência cruzada entre adiantamento e despesa de competência' AFTER `data_pagamento`",
        "ALTER TABLE `despesas` ADD COLUMN `tipo` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0: Normal, 1: Adiantamento' AFTER `adiantamento_ref_id`",
        "ALTER TABLE `despesas` ADD INDEX `idx_recorrencia_grupo` (`recorrencia_grupo`)",
        "ALTER TABLE `despesas` ADD INDEX `idx_adiantamento_ref` (`adiantamento_ref_id`)",
        "ALTER TABLE `despesas` ADD CONSTRAINT `fk_adiantamento_ref` FOREIGN KEY (`adiantamento_ref_id`) REFERENCES `despesas` (`id`) ON DELETE SET NULL",
    ];

    foreach ($alteracoes_despesas as $sql) {
        try {
            $conexao->query($sql);
            echo "  ✅ OK\n";
        } catch (Exception $e) {
            // Ignora se já existe (para poder re-executar com segurança)
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                echo "  ⏭️ Já existe, ignorando.\n";
            } else {
                echo "  ❌ Erro: " . $e->getMessage() . "\n";
            }
        }
    }

    // =========================================================================
    // ETAPA 2: Adicionar campo à tabela `rendas`
    // =========================================================================
    echo "\n📦 Etapa 2: Adicionando campo à tabela rendas...\n";

    try {
        $conexao->query("ALTER TABLE `rendas` ADD COLUMN `recorrencia_grupo` VARCHAR(36) DEFAULT NULL COMMENT 'UUID que agrupa instâncias da mesma renda recorrente' AFTER `recorrente`");
        echo "  ✅ recorrencia_grupo adicionado.\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "  ⏭️ Já existe, ignorando.\n";
        } else {
            echo "  ❌ Erro: " . $e->getMessage() . "\n";
        }
    }
}

// =========================================================================
// ETAPA 3: Gerar UUIDs retroativos para recorrências existentes
// =========================================================================
echo "\n📦 Etapa 3: Gerando UUIDs retroativos...\n";

$conexao->begin_transaction();

try {
    // ---- DESPESAS RECORRENTES ----
    // Agrupa despesas recorrentes por descrição, valor e categoria
    $sqlGruposDespesas = "
        SELECT descricao, valor, categoria_id 
        FROM despesas 
        WHERE recorrente = 1 AND recorrencia_grupo IS NULL
        GROUP BY descricao, valor, categoria_id
    ";
    $resultGrupos = $conexao->query($sqlGruposDespesas);
    $totalGruposDespesas = 0;

    if ($resultGrupos && $resultGrupos->num_rows > 0) {
        $stmtUpdate = $conexao->prepare("
            UPDATE despesas 
            SET recorrencia_grupo = ? 
            WHERE descricao = ? AND valor = ? AND categoria_id = ? AND recorrente = 1 AND recorrencia_grupo IS NULL
        ");

        while ($grupo = $resultGrupos->fetch_assoc()) {
            // Gera um UUID v4 para este grupo
            $uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $stmtUpdate->bind_param("ssdi", $uuid, $grupo['descricao'], $grupo['valor'], $grupo['categoria_id']);
            $stmtUpdate->execute();
            $totalGruposDespesas++;
        }
        $stmtUpdate->close();
    }
    echo "  ✅ $totalGruposDespesas grupos de despesas recorrentes receberam UUID.\n";

    // ---- RENDAS RECORRENTES ----
    $sqlGruposRendas = "
        SELECT descricao, valor 
        FROM rendas 
        WHERE recorrente = 1 AND recorrencia_grupo IS NULL
        GROUP BY descricao, valor
    ";
    $resultGruposR = $conexao->query($sqlGruposRendas);
    $totalGruposRendas = 0;

    if ($resultGruposR && $resultGruposR->num_rows > 0) {
        $stmtUpdateR = $conexao->prepare("
            UPDATE rendas 
            SET recorrencia_grupo = ? 
            WHERE descricao = ? AND valor = ? AND recorrente = 1 AND recorrencia_grupo IS NULL
        ");

        while ($grupo = $resultGruposR->fetch_assoc()) {
            $uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $stmtUpdateR->bind_param("ssd", $uuid, $grupo['descricao'], $grupo['valor']);
            $stmtUpdateR->execute();
            $totalGruposRendas++;
        }
        $stmtUpdateR->close();
    }
    echo "  ✅ $totalGruposRendas grupos de rendas recorrentes receberam UUID.\n";

    // ---- ATUALIZAR data_pagamento para despesas já pagas ----
    $conexao->query("UPDATE despesas SET data_pagamento = data WHERE status = 1 AND data_pagamento IS NULL");
    echo "  ✅ data_pagamento atualizado para despesas já pagas.\n";

    $conexao->commit();
    echo "\n🎉 Migração concluída com sucesso!\n";

} catch (Exception $e) {
    $conexao->rollback();
    echo "\n❌ ERRO CRÍTICO (rollback realizado): " . $e->getMessage() . "\n";
}
