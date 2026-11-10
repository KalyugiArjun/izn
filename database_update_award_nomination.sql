-- ============================================================
-- DATABASE UPDATE SCRIPT FOR AWARD NOMINATION & AUTHORITY
-- Project: SAATHI / IZN
-- ============================================================

-- 1. Create table self_nominations (Award Nominations Table)
CREATE TABLE IF NOT EXISTS `self_nominations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `proficiency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `station_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ti_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ti_comment` text DEFAULT NULL,
  `ti_forward_date` date DEFAULT NULL,
  `ti_forward_time` time DEFAULT NULL,
  `sr_dom_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dom_comment` text DEFAULT NULL,
  `cert_proficiency` bigint(20) UNSIGNED DEFAULT NULL,
  `checked_date` date DEFAULT NULL,
  `checked_time` time DEFAULT NULL,
  `certificate_no` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0 COMMENT '0=Pending,1=TICheck,2=Awarded,3=Rejected',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Insert initial nomination sample records (if not already present)
INSERT IGNORE INTO `self_nominations` (`id`, `staff_id`, `date`, `time`, `name`, `designation_id`, `proficiency_id`, `description`, `file`, `file_type`, `station_id`, `ti_id`, `ti_comment`, `ti_forward_date`, `ti_forward_time`, `sr_dom_id`, `dom_comment`, `cert_proficiency`, `checked_date`, `checked_time`, `certificate_no`, `status`, `created_at`, `updated_at`) VALUES
(1, 362, '2026-10-22', '11:01:00', 'Mohd.Israil', 3, 1, 'Dh', 'nominations/h3hSkDp7kgxETr7DQkY0oJrP7vsVCREXQ6tWxoFq.pdf', 'pdf', 14, 513, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-10-22 05:32:01', '2026-10-22 05:32:01'),
(2, 362, '2026-10-22', '11:02:00', 'Mohd.Israil', 3, 2, 'Hxz', 'nominations/VTcfQoWoBVwl8ADjKCjNeHzgymqVzTHmlJWdKc2I.jpg', 'jpg', 14, 513, 'Recommended for Award', '2026-10-27', '21:01:00', 510, 'Give Cash Award 500', 1, '2026-10-29', '12:20:00', 'NER/IZN/OPTG/0001', 2, '2026-10-22 05:32:35', '2026-10-29 06:50:49'),
(3, 141, '2026-10-29', '12:22:00', 'VIKAS SINGH RANA', 4, 3, 'I save a passenger after starting 55331, he hanged with Door', 'nominations/7xCBCgbsC9sBpydn17Y75x55HHf9nuR8Ro7s2VL3.png', 'png', 72, 513, 'Good Work Done by SM, A Suitable Award me by Give.', '2026-10-29', '12:23:00', 510, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-29 06:52:59', '2026-10-29 06:54:15'),
(4, 362, '2026-11-03', '13:36:00', 'Mohd.Israil', 3, 1, 'Working', 'nominations/fV1w16hZhQW07IXM4870OBg2Qfj2XLtjkjsgsTHd.pdf', 'pdf', 14, 513, 'Pass', '2026-11-03', '13:38:00', 510, 'Good', 1, '2026-11-03', '13:40:00', 'NER/IZN/OPTG/0002', 2, '2026-11-03 02:36:58', '2026-11-03 02:40:25');

-- 3. Update auth_award_nominations table schema (if needed):
ALTER TABLE `auth_award_nominations` 
  ADD COLUMN IF NOT EXISTS `employee_id` BIGINT UNSIGNED NULL AFTER `designation_id`;

ALTER TABLE `auth_award_nominations` 
  MODIFY COLUMN `designation_id` BIGINT UNSIGNED NULL;
