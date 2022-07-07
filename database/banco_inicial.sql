-- MySQL Workbench Synchronization
-- Generated: 2022-07-07 17:08
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Douglas de Jesus

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `energy_people` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE IF NOT EXISTS `energy_people`.`beneficios` (
  `beneficio_id` INT(11) NOT NULL AUTO_INCREMENT,
  `beneficio_nome` VARCHAR(40) NOT NULL,
  `beneficio_codigo` CHAR(20) NOT NULL,
  `beneficio_operadora` VARCHAR(45) NOT NULL,
  `beneficio_tipo` VARCHAR(45) NOT NULL,
  `beneficio_valor` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `beneficio_dt_vencimento` DATE NULL DEFAULT NULL COMMENT 'Data de vencimento do contrato',
  `beneficio_dh_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `beneficio_dh_alteracao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`beneficio_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;