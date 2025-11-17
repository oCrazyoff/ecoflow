CREATE TABLE `avisos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo/Visível, 0=Inativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios_avisos_vistos` (
  `usuario_id` int(10) unsigned NOT NULL,
  `aviso_id` int(10) unsigned NOT NULL,
  `data_visto` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`usuario_id`, `aviso_id`),
  KEY `idx_aviso` (`aviso_id`),
  CONSTRAINT `fk_uav_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_uav_aviso`
    FOREIGN KEY (`aviso_id`)
    REFERENCES `avisos` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;