<?php
// Script de migração para o novo sistema de recorrências
// Execute via CLI ou acesse pelo navegador (apenas uma vez)

require_once __DIR__ . '/../backend/valida.php'; // Usa o sistema de validação/conexão existente

echo "Iniciando migração de recorrentes...<br>\n";

// Iniciar transação
$conexao->begin_transaction();

try {
    // 1. Criar tabela recorrentes
    echo "1. Verificando/Criando tabela recorrentes...<br>\n";
    $sqlTabela = "
    CREATE TABLE IF NOT EXISTS recorrentes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT(10) UNSIGNED NOT NULL,
        tipo ENUM('renda', 'despesa') NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        valor DECIMAL(10,2) NOT NULL,
        categoria_id INT(10) UNSIGNED NULL COMMENT 'Apenas para despesas',
        dia_vencimento TINYINT NOT NULL DEFAULT 1 COMMENT 'Dia do mês (1-31)',
        ativo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Pausado',
        data_inicio DATE NOT NULL COMMENT 'A partir de quando recorre',
        data_fim DATE NULL COMMENT 'Até quando recorre (NULL = indefinido)',
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if (!$conexao->query($sqlTabela)) {
        throw new Exception("Erro ao criar tabela recorrentes: " . $conexao->error);
    }
    echo "Tabela recorrentes OK.<br>\n";

    // 2. Adicionar colunas em despesas
    echo "2. Verificando/Adicionando colunas em despesas...<br>\n";
    $result = $conexao->query("SHOW COLUMNS FROM despesas LIKE 'recorrente_id'");
    if ($result->num_rows == 0) {
        $conexao->query("ALTER TABLE despesas ADD COLUMN recorrente_id INT UNSIGNED NULL, ADD COLUMN ignorado TINYINT(1) NOT NULL DEFAULT 0");
        $conexao->query("ALTER TABLE despesas ADD CONSTRAINT fk_desp_recorrente FOREIGN KEY (recorrente_id) REFERENCES recorrentes(id) ON DELETE SET NULL");
        echo "Colunas adicionadas em despesas.<br>\n";
    } else {
        echo "Colunas já existem em despesas.<br>\n";
    }

    // 3. Adicionar colunas em rendas
    echo "3. Verificando/Adicionando colunas em rendas...<br>\n";
    $result = $conexao->query("SHOW COLUMNS FROM rendas LIKE 'recorrente_id'");
    if ($result->num_rows == 0) {
        $conexao->query("ALTER TABLE rendas ADD COLUMN recorrente_id INT UNSIGNED NULL, ADD COLUMN ignorado TINYINT(1) NOT NULL DEFAULT 0");
        $conexao->query("ALTER TABLE rendas ADD CONSTRAINT fk_rendas_recorrente FOREIGN KEY (recorrente_id) REFERENCES recorrentes(id) ON DELETE SET NULL");
        echo "Colunas adicionadas em rendas.<br>\n";
    } else {
        echo "Colunas já existem em rendas.<br>\n";
    }

    // 4. Migrar dados
    echo "4. Migrando dados existentes...<br>\n";

    // Migrar Despesas (tipo=0, recorrente=1)
    $qDespesas = "SELECT recorrencia_grupo, MAX(id) as max_id, MIN(data) as min_data 
                  FROM despesas 
                  WHERE recorrente = 1 AND tipo = 0 AND recorrencia_grupo IS NOT NULL 
                  GROUP BY recorrencia_grupo";
    $resDespesas = $conexao->query($qDespesas);
    
    $countDespesas = 0;
    while ($row = $resDespesas->fetch_assoc()) {
        $grupo = $row['recorrencia_grupo'];
        $maxId = $row['max_id'];
        $minData = $row['min_data'];

        // Pegar detalhes do mais recente
        $qDet = $conexao->query("SELECT usuario_id, descricao, valor, categoria_id, DAY(data) as dia_vencimento FROM despesas WHERE id = $maxId");
        $det = $qDet->fetch_assoc();

        if ($det) {
            $stmt = $conexao->prepare("INSERT INTO recorrentes (usuario_id, tipo, descricao, valor, categoria_id, dia_vencimento, ativo, data_inicio) VALUES (?, 'despesa', ?, ?, ?, ?, 1, ?)");
            $stmt->bind_param("isssis", $det['usuario_id'], $det['descricao'], $det['valor'], $det['categoria_id'], $det['dia_vencimento'], $minData);
            $stmt->execute();
            $newId = $stmt->insert_id;
            $stmt->close();

            // Atualizar entradas
            $stmtUpd = $conexao->prepare("UPDATE despesas SET recorrente_id = ? WHERE recorrencia_grupo = ?");
            $stmtUpd->bind_param("is", $newId, $grupo);
            $stmtUpd->execute();
            $stmtUpd->close();
            
            $countDespesas++;
        }
    }
    
    // Despesas sem grupo mas com recorrente=1
    $qDespesasSem = "SELECT id, usuario_id, descricao, valor, categoria_id, DAY(data) as dia_vencimento, data FROM despesas WHERE recorrente = 1 AND tipo = 0 AND recorrencia_grupo IS NULL";
    $resDespesasSem = $conexao->query($qDespesasSem);
    while ($row = $resDespesasSem->fetch_assoc()) {
        $stmt = $conexao->prepare("INSERT INTO recorrentes (usuario_id, tipo, descricao, valor, categoria_id, dia_vencimento, ativo, data_inicio) VALUES (?, 'despesa', ?, ?, ?, ?, 1, ?)");
        $stmt->bind_param("isssis", $row['usuario_id'], $row['descricao'], $row['valor'], $row['categoria_id'], $row['dia_vencimento'], $row['data']);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();

        $conexao->query("UPDATE despesas SET recorrente_id = $newId WHERE id = {$row['id']}");
        $countDespesas++;
    }
    
    echo "Migrados $countDespesas templates de despesas.<br>\n";

    // Migrar Rendas (recorrente=1)
    $qRendas = "SELECT recorrencia_grupo, MAX(id) as max_id, MIN(data) as min_data 
                FROM rendas 
                WHERE recorrente = 1 AND recorrencia_grupo IS NOT NULL 
                GROUP BY recorrencia_grupo";
    $resRendas = $conexao->query($qRendas);
    
    $countRendas = 0;
    while ($row = $resRendas->fetch_assoc()) {
        $grupo = $row['recorrencia_grupo'];
        $maxId = $row['max_id'];
        $minData = $row['min_data'];

        $qDet = $conexao->query("SELECT usuario_id, descricao, valor, DAY(data) as dia_vencimento FROM rendas WHERE id = $maxId");
        $det = $qDet->fetch_assoc();

        if ($det) {
            $stmt = $conexao->prepare("INSERT INTO recorrentes (usuario_id, tipo, descricao, valor, categoria_id, dia_vencimento, ativo, data_inicio) VALUES (?, 'renda', ?, ?, NULL, ?, 1, ?)");
            $stmt->bind_param("isdis", $det['usuario_id'], $det['descricao'], $det['valor'], $det['dia_vencimento'], $minData);
            $stmt->execute();
            $newId = $stmt->insert_id;
            $stmt->close();

            $stmtUpd = $conexao->prepare("UPDATE rendas SET recorrente_id = ? WHERE recorrencia_grupo = ?");
            $stmtUpd->bind_param("is", $newId, $grupo);
            $stmtUpd->execute();
            $stmtUpd->close();
            
            $countRendas++;
        }
    }
    
    $qRendasSem = "SELECT id, usuario_id, descricao, valor, DAY(data) as dia_vencimento, data FROM rendas WHERE recorrente = 1 AND recorrencia_grupo IS NULL";
    $resRendasSem = $conexao->query($qRendasSem);
    while ($row = $resRendasSem->fetch_assoc()) {
        $stmt = $conexao->prepare("INSERT INTO recorrentes (usuario_id, tipo, descricao, valor, categoria_id, dia_vencimento, ativo, data_inicio) VALUES (?, 'renda', ?, ?, NULL, ?, 1, ?)");
        $stmt->bind_param("isdis", $row['usuario_id'], $row['descricao'], $row['valor'], $row['dia_vencimento'], $row['data']);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();

        $conexao->query("UPDATE rendas SET recorrente_id = $newId WHERE id = {$row['id']}");
        $countRendas++;
    }

    echo "Migrados $countRendas templates de rendas.<br>\n";

    $conexao->commit();
    echo "Migração concluída com sucesso!<br>\n";

} catch (Exception $e) {
    $conexao->rollback();
    echo "Erro na migração: " . $e->getMessage() . "<br>\n";
}
