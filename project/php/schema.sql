CREATE TABLE IF NOT EXISTS `mails` ( -- used for the emails
  `id` INT NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(255) NOT NULL,
  `body` MEDIUMTEXT NOT NULL,
  `sender_name` VARCHAR(255) NOT NULL,
  `sender_email` VARCHAR(320) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; --this is the schema for the mails table, references taken from "geeksforgeeks.org/introduction-to-mysql-character-encoding"

CREATE TABLE IF NOT EXISTS `mail_recipients` ( -- used for the recipients of the emails
  `id` INT NOT NULL AUTO_INCREMENT,
  `mail_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(320) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mail_id` (`mail_id`),
  CONSTRAINT `fk_mail_recipients_mail` FOREIGN KEY (`mail_id`) REFERENCES `mails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


