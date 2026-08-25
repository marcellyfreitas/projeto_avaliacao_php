-- ============================================================
-- Script de criação do banco de dados
-- Sistema de Ordem de Serviços - JM Informática
-- ============================================================

CREATE DATABASE IF NOT EXISTS `jm_informatica`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `jm_informatica`;

-- -----------------------------------------------------------
-- Tabela de usuários
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id_user`  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(100)    NOT NULL,
  `email`    VARCHAR(150)    NOT NULL,
  `password` VARCHAR(255)    NOT NULL,
  `ativo`    TINYINT(1)      NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uk_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Tabela de serviços
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service` (
  `id_service`    INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `description`   VARCHAR(255)   NOT NULL,
  `price`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `finished_at`   DATETIME       NULL DEFAULT NULL,
  `commission_user` DECIMAL(10,2) NULL DEFAULT NULL,
  `user_id_user`  INT UNSIGNED   NOT NULL,
  `created_at`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_service`),
  KEY `idx_service_user` (`user_id_user`),
  CONSTRAINT `fk_service_user`
    FOREIGN KEY (`user_id_user`) REFERENCES `user` (`id_user`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Usuário padrão para teste (senha: 123456)
-- -----------------------------------------------------------
INSERT INTO `user` (`name`, `email`, `password`, `ativo`)
VALUES ('Administrador', 'admin@jminformatica.com.br', '$2y$12$2TvFKtmT0G/YuM2TUJrhneYSw2jfl8Dm8YEvJIvR4v4ZrE5tFFtlu', 1);
