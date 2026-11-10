-- Database Update for November 5, 2026
-- Adds created_by column to self_nominations for user-level data isolation

ALTER TABLE `self_nominations` 
ADD COLUMN IF NOT EXISTS `created_by` BIGINT UNSIGNED NULL AFTER `status`;

-- Set default created_by = 1 for historical records
UPDATE `self_nominations` 
SET `created_by` = 1 
WHERE `created_by` IS NULL;

