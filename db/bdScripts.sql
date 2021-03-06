-- MySQL Script generated by MySQL Workbench
-- Wed 11 Oct 2017 11:15:06 AM CEST
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema TAH_DB
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `TAH_DB` ;

-- -----------------------------------------------------
-- Schema TAH_DB
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `TAH_DB` DEFAULT CHARACTER SET utf8 ;
USE `TAH_DB` ;

-- -----------------------------------------------------
-- Table `TAH_DB`.`Game`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TAH_DB`.`Game` ;

CREATE TABLE IF NOT EXISTS `TAH_DB`.`Game` (
  `GameCode` VARCHAR(45) NOT NULL,
  `Title` VARCHAR(50) NULL,
  `CurrentWriter` INT NULL,
  PRIMARY KEY (`GameCode`));


-- -----------------------------------------------------
-- Table `TAH_DB`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TAH_DB`.`User` ;

CREATE TABLE IF NOT EXISTS `TAH_DB`.`User` (
  `userID` INT NOT NULL AUTO_INCREMENT,
  `Game_GameCode` VARCHAR(45) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `create_time` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `checkin` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `color` VARCHAR(45) NULL,
  PRIMARY KEY (`userID`, `Game_GameCode`),
  INDEX `fk_user_Game1_idx` (`Game_GameCode` ASC),
  CONSTRAINT `fk_user_Game1`
    FOREIGN KEY (`Game_GameCode`)
    REFERENCES `TAH_DB`.`Game` (`GameCode`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TAH_DB`.`Entry`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TAH_DB`.`Entry` ;

CREATE TABLE IF NOT EXISTS `TAH_DB`.`Entry` (
  `EntryNr` INT NOT NULL AUTO_INCREMENT,
  `Game_GameCode` VARCHAR(45) NOT NULL,
  `user_userID` INT NOT NULL,
  `Text` LONGTEXT NULL,
  `create_time` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`EntryNr`, `Game_GameCode`),
  INDEX `fk_Entry_user1_idx` (`user_userID` ASC),
  CONSTRAINT `fk_Entry_Game1`
    FOREIGN KEY (`Game_GameCode`)
    REFERENCES `TAH_DB`.`Game` (`GameCode`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Entry_user1`
    FOREIGN KEY (`user_userID`)
    REFERENCES `TAH_DB`.`User` (`userID`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
