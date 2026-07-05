-- Migração: Adicionar suporte a parcelas na tabela despesas
-- Execute este script no phpMyAdmin ou MySQL CLI

ALTER TABLE `despesas`
  ADD COLUMN `parcela_grupo` VARCHAR(36) NULL DEFAULT NULL COMMENT 'UUID compartilhado entre parcelas do mesmo grupo' AFTER `data`,
  ADD COLUMN `parcela_numero` SMALLINT NULL DEFAULT NULL COMMENT 'Número da parcela (1, 2, 3...)' AFTER `parcela_grupo`,
  ADD COLUMN `parcela_total` SMALLINT NULL DEFAULT NULL COMMENT 'Total de parcelas do grupo' AFTER `parcela_numero`;

-- Índice para buscas por grupo de parcelas
ALTER TABLE `despesas`
  ADD INDEX `idx_parcela_grupo` (`parcela_grupo`);
