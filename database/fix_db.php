<?php
require_once __DIR__ . '/../backend/conexao.php';

$sql = [
    "ALTER TABLE despesas ADD COLUMN parcela_grupo VARCHAR(36) DEFAULT NULL",
    "ALTER TABLE despesas ADD COLUMN parcela_numero SMALLINT(6) DEFAULT NULL",
    "ALTER TABLE despesas ADD COLUMN parcela_total SMALLINT(6) DEFAULT NULL"
];

foreach ($sql as $query) {
    try {
        $conexao->query($query);
        echo "Sucesso: $query\n";
    } catch (Exception $e) {
        echo "Erro ou já existe: " . $e->getMessage() . "\n";
    }
}
