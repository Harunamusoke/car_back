-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema car_res_park
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema car_res_park
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `car_res_park` DEFAULT CHARACTER SET utf8 ;
USE `car_res_park` ;

-- -----------------------------------------------------
-- Table `car_res_park`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `gender` CHAR(1) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `contact` VARCHAR(20) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `permissions` TINYINT NOT NULL DEFAULT '110',
  `is_active` TINYINT NOT NULL DEFAULT '0',
  `date_created` DATETIME NOT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `date_modified` DATETIME NULL DEFAULT NULL,
  `is_deleted` TINYINT NOT NULL DEFAULT '0',
  `deleted_by` INT NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `contact_UNIQUE` (`contact` ASC) VISIBLE,
  UNIQUE INDEX `email_UNIQUE` (`email`(8) ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 37
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `car_res_park`.`rates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`rates` (
  `rate_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `rate` DOUBLE NOT NULL,
  `from` TINYINT NOT NULL COMMENT 'Duration in HOURS',
  `to` TINYINT NOT NULL,
  `is_enabled` TINYINT NOT NULL DEFAULT '1' COMMENT '0 - DISABLED\\n1 - ENABLED\\n2- SHORT DISABLE',
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`rate_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `car_res_park`.`vehicles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`vehicles` (
  `vehicle_id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `license_plate` VARCHAR(20) NOT NULL,
  `date_reg` DATETIME NOT NULL,
  `is_membered` TINYINT NOT NULL DEFAULT '1',
  PRIMARY KEY (`vehicle_id`),
  UNIQUE INDEX `license_plate_UNIQUE` (`license_plate` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `car_res_park`.`slots`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`slots` (
  `slot_id` TINYINT NOT NULL,
  `address` VARCHAR(10) NOT NULL,
  `is_occupied` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`slot_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `car_res_park`.`parking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`parking` (
  `park_id` BIGINT NOT NULL AUTO_INCREMENT,
  `vehicle_id` BIGINT NOT NULL,
  `amount` DOUBLE NULL DEFAULT NULL,
  `token` VARCHAR(225) NOT NULL,
  `rate_id` INT NULL DEFAULT NULL,
  `is_cleared` TINYINT NULL DEFAULT NULL,
  `slot_id` TINYINT NOT NULL DEFAULT '0',
  `created_at` DATETIME NOT NULL,
  `created_by` INT NOT NULL,
  `exit_at` DATETIME NULL DEFAULT NULL,
  `exit_by` INT NULL DEFAULT NULL,
  PRIMARY KEY (`park_id`),
  INDEX `fk_customer_parking_customer_park_plans1_idx` (`rate_id` ASC) VISIBLE,
  INDEX `fk_customer_parking_zones1_idx` (`slot_id` ASC) VISIBLE,
  INDEX `fk_customer_parking_admin1_idx` (`created_by` ASC) VISIBLE,
  INDEX `fk_customer_parking_admin2_idx` (`exit_by` ASC) VISIBLE,
  INDEX `fk_customer_parking_vehicles1_idx` (`vehicle_id` ASC) VISIBLE,
  INDEX `idx_is_cleared` (`is_cleared` ASC) VISIBLE,
  CONSTRAINT `fk_park_checker`
    FOREIGN KEY (`created_by`)
    REFERENCES `car_res_park`.`users` (`user_id`)
    ON UPDATE CASCADE,
  CONSTRAINT `fk_park_rates`
    FOREIGN KEY (`rate_id`)
    REFERENCES `car_res_park`.`rates` (`rate_id`),
  CONSTRAINT `fk_park_users`
    FOREIGN KEY (`exit_by`)
    REFERENCES `car_res_park`.`users` (`user_id`),
  CONSTRAINT `fk_park_vehicles`
    FOREIGN KEY (`vehicle_id`)
    REFERENCES `car_res_park`.`vehicles` (`vehicle_id`),
  CONSTRAINT `fk_park_zone`
    FOREIGN KEY (`slot_id`)
    REFERENCES `car_res_park`.`slots` (`slot_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 209
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `car_res_park`.`systems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`systems` (
  `sys_id` TINYINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `endpoint` VARCHAR(45) NOT NULL,
  `charge` DOUBLE NOT NULL DEFAULT '0',
  `date_created` DATETIME NOT NULL,
  `deleted` TINYINT NULL DEFAULT '0',
  PRIMARY KEY (`sys_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `car_res_park`.`payments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`payments` (
  `pay_id` BIGINT NOT NULL AUTO_INCREMENT,
  `park_id` BIGINT NOT NULL,
  `sys_id` TINYINT NOT NULL,
  `number` INT NOT NULL DEFAULT '0',
  `amount` DOUBLE NOT NULL,
  `date_initiated` DATETIME NOT NULL,
  `date_completed` DATETIME NULL DEFAULT NULL,
  `external_ref` VARCHAR(45) NULL DEFAULT NULL,
  `status` TINYINT NULL DEFAULT NULL COMMENT '0 - PENDIG\\n1 - SUCCESS\\n2 - FAILED',
  PRIMARY KEY (`pay_id`),
  INDEX `fk_payments_payment_sys1_idx` (`sys_id` ASC) VISIBLE,
  INDEX `fk_payments_customer_parking1_idx` (`park_id` ASC) VISIBLE,
  CONSTRAINT `fk_payments_customer_parking1`
    FOREIGN KEY (`park_id`)
    REFERENCES `car_res_park`.`parking` (`park_id`),
  CONSTRAINT `fk_payments_payment_sys1`
    FOREIGN KEY (`sys_id`)
    REFERENCES `car_res_park`.`systems` (`sys_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

USE `car_res_park` ;

-- -----------------------------------------------------
-- Placeholder table for view `car_res_park`.`cars_parking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_res_park`.`cars_parking` (`park_id` INT, `license_plate` INT, `name` INT, `token` INT, `slot` INT, `checked_by` INT, `checked_at` INT, `cleared` INT, `exit_at` INT, `exit_by` INT);

-- -----------------------------------------------------
-- procedure CARS_STILL_IN
-- -----------------------------------------------------

DELIMITER $$
USE `car_res_park`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CARS_STILL_IN`()
BEGIN

SELECT `parking`.`park_id`,
`vehicles`.`license_plate`,`vehicles`.`name`, `parking`.`token`, `slots`.`address` AS `slot` ,
 CONCAT( `users`.`first_name` , " " , `users`.`last_name` ) AS 	`checked_by` , `parking`.`created_at`AS `checked_at`
FROM `parking`
    JOIN `vehicles` ON `vehicles`.`vehicle_id` =  `parking`.`vehicle_id`
    JOIN `slots` ON  `slots`.`slot_id` = `parking`.`slot_id`
	JOIN  `users` ON `users`.`user_id` = `parking`.`created_by`
WHERE  `is_cleared` IS NULL
ORDER BY `parking`.`park_id` DESC;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- View `car_res_park`.`cars_parking`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `car_res_park`.`cars_parking`;
USE `car_res_park`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `car_res_park`.`cars_parking` AS select `car_res_park`.`parking`.`park_id` AS `park_id`,`car_res_park`.`vehicles`.`license_plate` AS `license_plate`,`car_res_park`.`vehicles`.`name` AS `name`,`car_res_park`.`parking`.`token` AS `token`,`car_res_park`.`slots`.`address` AS `slot`,concat(`car_res_park`.`users`.`first_name`,' ',`car_res_park`.`users`.`last_name`) AS `checked_by`,`car_res_park`.`parking`.`created_at` AS `checked_at`,`car_res_park`.`parking`.`is_cleared` AS `cleared`,`car_res_park`.`parking`.`exit_at` AS `exit_at`,`car_res_park`.`parking`.`exit_by` AS `exit_by` from (((`car_res_park`.`parking` join `car_res_park`.`vehicles` on((`car_res_park`.`vehicles`.`vehicle_id` = `car_res_park`.`parking`.`vehicle_id`))) join `car_res_park`.`slots` on((`car_res_park`.`slots`.`slot_id` = `car_res_park`.`parking`.`slot_id`))) join `car_res_park`.`users` on((`car_res_park`.`users`.`user_id` = `car_res_park`.`parking`.`created_by`))) order by `car_res_park`.`parking`.`park_id` desc;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
