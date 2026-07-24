<?php
/**
 * Script de migração: Adiciona suporte a "Lembrar-me" na tabela de usuários.
 * 
 * Uso: Acesse via navegador ou execute via CLI: php migracao_lembrar_me.php
 */

require_once __DIR__ . '/../backend/conexao.php';

echo "🔄 Iniciando migração para a funcionalidade 'Lembrar-me'...\n\n";

try {
    $sql = "ALTER TABLE `usuarios` ADD COLUMN `lembrar_token` VARCHAR(64) DEFAULT NULL COMMENT 'Token hash para o recurso lembrar-me'";
    $conexao->query($sql);
    echo "✅ Coluna `lembrar_token` adicionada à tabela `usuarios` com sucesso.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "⏭️ A coluna `lembrar_token` já existe na tabela `usuarios`. Ignorando...\n";
    } else {
        echo "❌ Erro ao adicionar coluna: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 Migração concluída!\n";
