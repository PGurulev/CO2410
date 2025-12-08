-- Migration: Add is_fake column and unique constraint to mails table
ALTER TABLE `mails` 
ADD COLUMN `is_fake` TINYINT(1) NOT NULL DEFAULT 0 AFTER `sender_email`,
ADD UNIQUE KEY `idx_unique_mail` (`subject`, `sender_email`);


