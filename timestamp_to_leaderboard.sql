-- Migration to add timestamp column to leaderboard table
-- Use this script if the leaderboard table already exists without the timestamp column

ALTER TABLE `leaderboard` 
ADD COLUMN `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
AFTER `score`;

-- Add index for better query performance when sorting by timestamp
ALTER TABLE `leaderboard` 
ADD INDEX `idx_timestamp` (`timestamp`); -- here idx_ is an index, it makes ORDER BY faster, makes JOIN operations faster(reference: geeksforgeeks.org/mysql-indexes)

