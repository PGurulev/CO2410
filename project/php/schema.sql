  CREATE TABLE IF NOT EXISTS `mails` ( -- used for the emails
    `id` INT NOT NULL AUTO_INCREMENT,
    `subject` VARCHAR(255) NOT NULL,
    `body` MEDIUMTEXT NOT NULL,
    `sender_name` VARCHAR(255) NOT NULL,
    `sender_email` VARCHAR(320) NOT NULL,
    `is_fake` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_unique_mail` (`subject`, `sender_email`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; -- this is the schema for the mails table, references taken from "geeksforgeeks.org/introduction-to-mysql-character-encoding"

  CREATE TABLE IF NOT EXISTS `mail_recipients` ( -- used for the recipients of the emails
    `id` INT NOT NULL AUTO_INCREMENT,
    `mail_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(320) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_mail_id` (`mail_id`),
    CONSTRAINT `fk_mail_recipients_mail` FOREIGN KEY (`mail_id`) REFERENCES `mails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  CREATE TABLE IF NOT EXISTS `departments` ( -- used for the departments
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  CREATE TABLE IF NOT EXISTS `employees` ( -- used for the employees
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(320) NOT NULL UNIQUE,
    `position` VARCHAR(255) NOT NULL,
    `department_id` INT NOT NULL,
    `manager_email` VARCHAR(320) NULL,
    PRIMARY KEY (`id`),
    KEY `idx_department_id` (`department_id`),
    KEY `idx_manager_email` (`manager_email`),
    CONSTRAINT `fk_employees_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


