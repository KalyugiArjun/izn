-- ========================================================================
-- DATABASE UPDATE SCRIPT (ONLY YESTERDAY & TODAY'S WORK - NOV 3 & NOV 4, 2026)
-- Project: SAATHI / IZN
-- ========================================================================
-- SUMMARY OF DATABASE WORK (NOV 3 & NOV 4):
-- 1. NEW TABLE CREATED: `auth_award_nominations` (Award Nomination Authority)
--    - Created on Nov 3, 2026
--    - Columns: id, department_id, designation_id, employee_id, created_by, updated_by, created_at, updated_at
--
-- 2. NEW TABLE CREATED: `self_nominations` (Award Records & Self Nominations)
--    - Created/Integrated for Module 5: Award Record Report
--    - Columns: id, staff_id, date, time, name, designation_id, proficiency_id, description,
--               file, file_type, station_id, ti_id, ti_comment, ti_forward_date, ti_forward_time,
--               sr_dom_id, dom_comment, cert_proficiency, checked_date, checked_time, certificate_no, status
--
-- 3. OTHER MODULES TOUCHED TODAY (NO DATABASE STRUCTURE CHANGES):
--    - Module 3 (My Movement): Uses existing `inspection_movements` (no schema change)
--    - Module 4 (Staff Counseling): Uses existing `staff_counselings` (no schema change)
--    - Module 6 (Employee Safety): Uses existing `employee_pmes`, `employee_refresher_courses`, `employee_gradations` (no schema change)
-- ========================================================================

SET FOREIGN_KEY_CHECKS=0;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_award_nominations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `designation_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_award_nominations_department_id_index` (`department_id`),
  KEY `auth_award_nominations_designation_id_index` (`designation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `auth_award_nominations` VALUES (2,2,2,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(3,2,3,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(4,2,4,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(5,2,63,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(6,2,67,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(7,2,68,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(8,2,69,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(9,2,71,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(10,2,72,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(12,2,77,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(13,2,78,NULL,2,2,'2026-10-26 18:25:02','2026-10-26 18:25:02'),(14,2,79,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(15,2,80,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(16,2,81,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(17,2,82,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(18,2,83,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(19,2,86,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(20,2,87,NULL,2,2,'2026-10-26 18:25:31','2026-10-26 18:25:31'),(21,2,92,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(22,2,93,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:43:15'),(23,2,95,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(24,2,96,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(25,2,97,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(26,2,98,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(27,2,99,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(28,2,100,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(29,2,101,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(30,2,102,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(31,2,103,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(32,2,104,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(33,2,105,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(34,2,106,NULL,2,2,'2026-10-26 18:26:25','2026-10-26 18:26:25'),(35,2,7,NULL,2,2,'2026-10-27 14:09:11','2026-10-27 14:09:11');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `self_nominations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `designation_id` bigint(20) unsigned DEFAULT NULL,
  `proficiency_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `station_id` bigint(20) unsigned DEFAULT NULL,
  `ti_id` bigint(20) unsigned DEFAULT NULL,
  `ti_comment` text DEFAULT NULL,
  `ti_forward_date` date DEFAULT NULL,
  `ti_forward_time` time DEFAULT NULL,
  `sr_dom_id` bigint(20) unsigned DEFAULT NULL,
  `dom_comment` text DEFAULT NULL,
  `cert_proficiency` bigint(20) unsigned DEFAULT NULL,
  `checked_date` date DEFAULT NULL,
  `checked_time` time DEFAULT NULL,
  `certificate_no` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0 COMMENT '0=Pending,1=TICheck,2=Awarded,3=Rejected',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `self_nominations` VALUES (1,362,'2026-10-22','11:01:00','Mohd.Israil',3,1,'Dh','nominations/h3hSkDp7kgxETr7DQkY0oJrP7vsVCREXQ6tWxoFq.pdf','pdf',14,513,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2026-10-22 05:32:01','2026-10-22 05:32:01'),(2,362,'2026-10-22','11:02:00','Mohd.Israil',3,2,'Hxz','nominations/VTcfQoWoBVwl8ADjKCjNeHzgymqVzTHmlJWdKc2I.jpg','jpg',14,513,'Recommended for Award','2026-10-27','21:01:00',510,'Give Cash Award 500',1,'2026-10-29','12:20:00','NER/IZN/OPTG/0001',2,'2026-10-22 05:32:35','2026-10-29 06:50:49'),(3,141,'2026-10-29','12:22:00','VIKAS SINGH RANA',4,3,'I save a passenger after starting 55331, he hanged with Door','nominations/7xCBCgbsC9sBpydn17Y75x55HHf9nuR8Ro7s2VL3.png','png',72,513,'Good Work Done by SM, A Suitable Award me by Give.','2026-10-29','12:23:00',510,NULL,NULL,NULL,NULL,NULL,1,'2026-10-29 06:52:59','2026-10-29 06:54:15'),(4,362,'2026-11-03','13:36:00','Mohd.Israil',3,1,'Working','nominations/fV1w16hZhQW07IXM4870OBg2Qfj2XLtjkjsgsTHd.pdf','pdf',14,513,'Pass','2026-11-03','13:38:00',510,'Good',1,'2026-11-03','13:40:00','NER/IZN/OPTG/0002',2,'2026-11-03 02:36:58','2026-11-03 02:40:25'),(5,860,'2026-11-03','15:00:00','ABHIJEET KUMAR',63,3,'good',NULL,NULL,87,513,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'2026-11-03 09:31:23','2026-11-03 09:31:23');

SET FOREIGN_KEY_CHECKS=1;

-- ========================================================================
-- END OF SCRIPT
-- ========================================================================
