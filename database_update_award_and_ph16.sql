-- ======================================================================
-- DATABASE UPDATE SCRIPT: AWARD NOMINATION & PH 16 WORKS
-- Project: SAATHI / IZN
-- Generated on: 2026-11-10
-- ======================================================================

-- ----------------------------------------------------------------------
-- 1. AWARD NOMINATION: Authorities Table
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `auth_award_nominations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_award_nominations_dept_idx` (`department_id`),
  KEY `auth_award_nominations_desig_idx` (`designation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------
-- 2. AWARD NOMINATION: Self Nominations Table & Columns
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `self_nominations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mobile_no` varchar(50) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `station` varchar(255) DEFAULT NULL,
  `place_of_posting` varchar(255) DEFAULT NULL,
  `service_rendered` varchar(255) DEFAULT NULL,
  `area_of_proficiency` varchar(255) DEFAULT NULL,
  `safety_category` varchar(255) DEFAULT NULL,
  `personal_branch_sr_no` varchar(255) DEFAULT NULL,
  `brief_description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Pending at TI Safety, 1: Forwarded to Sr. DOM, 2: Approved, 3: Rejected',
  `rejection_reason` text DEFAULT NULL,
  `forwarded_at` timestamp NULL DEFAULT NULL,
  `action_taken_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `self_nominations_created_by_idx` (`created_by`),
  KEY `self_nominations_status_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure created_by column exists on self_nominations (for user data isolation)
ALTER TABLE `self_nominations` ADD COLUMN IF NOT EXISTS `created_by` BIGINT UNSIGNED NULL AFTER `status`;

-- ----------------------------------------------------------------------
-- 3. PH 16 WORKS: executionends Table Fixes
-- ----------------------------------------------------------------------
ALTER TABLE `executionends` ADD COLUMN IF NOT EXISTS `milestone` VARCHAR(255) NULL AFTER `identify_new_work_id`;
ALTER TABLE `executionends` ADD COLUMN IF NOT EXISTS `date` DATE NULL AFTER `milestone`;
ALTER TABLE `executionends` MODIFY COLUMN `execution_start_date` DATE NULL;

-- ----------------------------------------------------------------------
-- 4. PH 16 WORKS: estimates Table Updates (Status, Remarks, Cost, Nullables)
-- ----------------------------------------------------------------------
ALTER TABLE `estimates` MODIFY COLUMN `call_letter` VARCHAR(255) NULL;
ALTER TABLE `estimates` MODIFY COLUMN `estimate_called` DATE NULL;
ALTER TABLE `estimates` MODIFY COLUMN `estimate_received` DATE NULL;
ALTER TABLE `estimates` MODIFY COLUMN `received_estimate` VARCHAR(255) NULL;
ALTER TABLE `estimates` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `department`;
ALTER TABLE `estimates` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
ALTER TABLE `estimates` ADD COLUMN IF NOT EXISTS `cost` DECIMAL(15,2) NULL AFTER `received_estimate`;
UPDATE `estimates` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- ----------------------------------------------------------------------
-- 5. PH 16 WORKS: identify_new_works Table (Multi-Select Traffic Inspector)
-- ----------------------------------------------------------------------
ALTER TABLE `identify_new_works` MODIFY COLUMN `concern_traffic_inspector` VARCHAR(500) NULL;
ALTER TABLE `identify_new_works` ADD COLUMN IF NOT EXISTS `ph16_ti_ids` JSON NULL AFTER `ph16_ti_id`;
UPDATE `identify_new_works` SET `ph16_ti_ids` = JSON_ARRAY(`ph16_ti_id`) WHERE `ph16_ti_id` IS NOT NULL AND (`ph16_ti_ids` IS NULL OR `ph16_ti_ids` = 'null');

-- ----------------------------------------------------------------------
-- 6. PH 16 WORKS: joint_surveys Table (Status, Remarks, Nullable Date & Days Taken)
-- ----------------------------------------------------------------------
ALTER TABLE `joint_surveys` MODIFY COLUMN `date` DATE NULL;
ALTER TABLE `joint_surveys` MODIFY COLUMN `days_taken` VARCHAR(255) NULL;
ALTER TABLE `joint_surveys` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `joint_surveys` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `joint_surveys` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- ----------------------------------------------------------------------
-- 7. PH 16 WORKS: Other Paperwork Timeline Tables to SIP (Status, Remarks, Nullable Dates & Days Taken)
-- ----------------------------------------------------------------------
-- review_estimates
ALTER TABLE `review_estimates` MODIFY COLUMN `review_estimate_date` DATE NULL;
ALTER TABLE `review_estimates` MODIFY COLUMN `days_taken` INT(10) UNSIGNED NULL;
ALTER TABLE `review_estimates` MODIFY COLUMN `previous_department` VARCHAR(255) NULL;
ALTER TABLE `review_estimates` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `review_estimates` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `review_estimates` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- approval_listings
ALTER TABLE `approval_listings` MODIFY COLUMN `date` DATE NULL;
ALTER TABLE `approval_listings` MODIFY COLUMN `days_taken` INT(11) NULL;
ALTER TABLE `approval_listings` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `approval_listings` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `approval_listings` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- finance_vetting_sanctions
ALTER TABLE `finance_vetting_sanctions` MODIFY COLUMN `date` DATE NULL;
ALTER TABLE `finance_vetting_sanctions` MODIFY COLUMN `days_taken` INT(11) NULL;
ALTER TABLE `finance_vetting_sanctions` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `finance_vetting_sanctions` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `finance_vetting_sanctions` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- tenderings
ALTER TABLE `tenderings` MODIFY COLUMN `date` DATE NULL;
ALTER TABLE `tenderings` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `tenderings` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `tenderings` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- esps
ALTER TABLE `esps` MODIFY COLUMN `date` DATE NULL;
ALTER TABLE `esps` MODIFY COLUMN `days_taken` INT(11) NULL;
ALTER TABLE `esps` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `esps` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `esps` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- sips
ALTER TABLE `sips` MODIFY COLUMN `date` DATE NULL;
ALTER TABLE `sips` MODIFY COLUMN `days_taken` INT(11) NULL;
ALTER TABLE `sips` ADD COLUMN IF NOT EXISTS `is_completed` TINYINT(1) DEFAULT 1 AFTER `milestone`;
ALTER TABLE `sips` ADD COLUMN IF NOT EXISTS `remark` VARCHAR(255) NULL AFTER `is_completed`;
UPDATE `sips` SET `is_completed` = 1 WHERE `is_completed` IS NULL;

-- ======================================================================
-- END OF DATABASE UPDATE SCRIPT
-- ======================================================================

