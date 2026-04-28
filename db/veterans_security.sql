-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 06:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `veterans_security`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `class_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `duration_hours` int(11) NOT NULL,
  `max_students` int(11) NOT NULL DEFAULT 10,
  `min_students` int(11) NOT NULL DEFAULT 2,
  `current_students` int(11) NOT NULL DEFAULT 0,
  `room` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `can_overlap` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('scheduled','full','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`id`, `service_id`, `class_date`, `start_time`, `end_time`, `duration_hours`, `max_students`, `min_students`, `current_students`, `room`, `location`, `instructor`, `can_overlap`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(4, 7, '2026-01-22', '12:12:00', '20:12:00', 8, 10, 2, 0, 'Class A 03', NULL, 'Jayson Wheat', 1, 'scheduled', 'Testing', '2026-01-20 17:38:22', '2026-01-20 17:38:22'),
(5, 7, '2026-01-23', '15:21:00', '23:21:00', 8, 10, 2, 2, 'Class A 03', NULL, 'Jayson Wheat', 1, 'scheduled', 'Testing', '2026-01-20 17:38:22', '2026-01-21 10:55:00'),
(6, 7, '2026-01-27', '08:42:00', '16:42:00', 8, 10, 2, 0, 'Class A 03', NULL, 'Jayson Wheat', 1, 'scheduled', 'Testing', '2026-01-20 17:38:22', '2026-01-20 17:38:22'),
(7, 1, '2026-01-22', '11:11:00', '16:11:00', 5, 8, 4, 0, 'Class A 7', NULL, 'Jayson', 1, 'scheduled', 'test', '2026-01-20 17:45:12', '2026-01-21 14:42:14'),
(8, 1, '2026-01-24', '07:50:00', '15:50:00', 8, 10, 2, 0, 'class A 09', NULL, 'Jayson ww', 1, 'scheduled', 'testing', '2026-01-20 17:45:50', '2026-01-20 17:45:50'),
(9, 3, '2026-01-23', '16:00:00', '08:00:00', 16, 10, 1, 0, '32123', NULL, 'Jack', 1, 'scheduled', NULL, '2026-01-21 13:34:30', '2026-01-21 13:34:30'),
(10, 2, '2026-01-30', '17:00:00', '01:00:00', 8, 10, 2, 0, 'Class A17', NULL, 'Jack son', 1, 'scheduled', NULL, '2026-01-21 13:36:22', '2026-01-21 13:36:22'),
(11, 2, '2026-01-31', '18:00:00', '02:00:00', 8, 10, 2, 0, 'Class A17', NULL, 'Jack son', 1, 'scheduled', NULL, '2026-01-21 13:36:22', '2026-01-21 13:36:22'),
(12, 26, '2026-01-29', '03:50:00', '07:50:00', 4, 10, 1, 0, '1231231', 'Location A', 'Jayson ww', 1, 'scheduled', 'Dallas Law Training', NULL, NULL),
(13, 26, '2026-01-29', '03:50:00', '07:50:00', 4, 10, 1, 3, '1231231', 'Location B', 'Jayson ww', 1, 'scheduled', 'Dallas Law Training', NULL, '2026-01-28 13:02:14'),
(14, 26, '2026-01-30', '18:51:00', '20:51:00', 2, 10, 1, 0, 'Class A 7', 'Location A', 'Jack', 1, 'scheduled', 'Lethal Less Traning', NULL, NULL),
(15, 26, '2026-01-30', '18:51:00', '20:51:00', 2, 10, 1, 0, 'Class A 7', 'Location B', 'Jack', 1, 'scheduled', 'Lethal Less Traning', NULL, NULL),
(16, 34, '2026-04-10', '09:00:00', '11:00:00', 2, 10, 1, 2, '1', 'Location A', 'John', 0, 'scheduled', NULL, '2026-04-08 19:32:28', '2026-04-08 19:41:14'),
(17, 68, '2026-04-10', '09:00:00', '17:00:00', 8, 10, 1, 10, '25', 'Location A', 'Jayson', 0, 'full', NULL, '2026-04-09 10:51:01', '2026-04-09 11:22:45'),
(18, 101, '2026-04-09', '16:10:00', '02:10:00', 10, 13, 1, 0, 'Quaerat quo nihil earum quis voluptate facere minus minima hic minima magnam animi fugiat qui', 'Location A', 'Sit ipsum cupidatat et vitae quo qui omnis sit in architecto ratione cupiditate omnis voluptate excepteur perferendis ducimus vitae praesentium', 0, 'scheduled', 'Velit possimus est sapiente veniam nisi corrupti harum vel sed nihil', '2026-04-09 15:13:04', '2026-04-09 15:13:04');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_12_174338_create_services_table', 2),
(5, '2026_01_12_175606_add_short_description_to_services_table', 3),
(6, '2026_01_12_182335_create_site_settings_table', 4),
(7, '2026_01_12_183941_add_profile_picture_to_users_table', 5),
(8, '2026_01_12_221013_create_customers_table', 6),
(9, '2026_01_12_221017_create_service_bookings_table', 6),
(10, '2026_01_20_220728_add_class_fields_to_services_table', 7),
(11, '2026_01_20_220737_create_class_schedules_table', 7),
(12, '2026_01_20_220740_add_payment_fields_to_service_bookings_table', 7),
(13, '2026_01_20_220744_create_payments_table', 7),
(14, '2026_01_21_184933_add_api_keys_to_site_settings_table', 8),
(15, '2026_01_27_163452_add_category_fields_to_services_table', 9),
(16, '2026_01_27_164327_create_security_company_links_table', 10),
(17, '2026_01_27_164435_add_instructor_bios_to_site_settings_table', 11),
(18, '2026_01_27_165429_add_location_to_class_schedules_table', 12),
(19, '2026_01_27_165433_add_location_to_service_bookings_table', 12),
(20, '2026_01_27_220557_create_service_relationships_table', 13),
(21, '2026_01_27_220640_add_requires_conditional_questions_to_services_table', 13),
(22, '2026_03_11_000000_add_slug_to_services_table', 14),
(23, '2026_03_11_100000_add_sub_titles_to_services_table', 15),
(24, '2026_02_12_100000_change_services_category_to_json_for_multiple', 16),
(25, '2026_03_02_201303_add_current_students_to_services_table', 16),
(26, '2026_03_26_160758_add_requirments_column_to_services_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('deposit','full_payment','remaining_balance','refund') NOT NULL DEFAULT 'deposit',
  `payment_method` enum('credit_card','debit_card','bank_transfer','cash','check','other') NOT NULL DEFAULT 'credit_card',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `gateway_response` text DEFAULT NULL,
  `synced_to_quickbooks` tinyint(1) NOT NULL DEFAULT 0,
  `quickbooks_invoice_id` varchar(255) DEFAULT NULL,
  `quickbooks_payment_id` varchar(255) DEFAULT NULL,
  `quickbooks_synced_at` timestamp NULL DEFAULT NULL,
  `synced_to_bank` tinyint(1) NOT NULL DEFAULT 0,
  `bank_synced_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_company_links`
--

CREATE TABLE `security_company_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `sub_titles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `max_students` int(11) NOT NULL DEFAULT 10,
  `min_students` int(11) NOT NULL DEFAULT 2,
  `class_type` enum('group','one-on-one') NOT NULL DEFAULT 'group',
  `has_online_parts` tinyint(1) NOT NULL DEFAULT 0,
  `testing_in_person` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `subcategory` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `requires_dallas_law` tinyint(1) NOT NULL DEFAULT 0,
  `requires_active_shooter` tinyint(1) NOT NULL DEFAULT 0,
  `requires_conditional_questions` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `current_students` int(11) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `slug`, `short_description`, `requirements`, `sub_titles`, `description`, `price`, `deposit_amount`, `duration_hours`, `max_students`, `min_students`, `class_type`, `has_online_parts`, `testing_in_person`, `image`, `order`, `is_active`, `subcategory`, `location`, `requires_dallas_law`, `requires_active_shooter`, `requires_conditional_questions`, `created_at`, `updated_at`, `categories`, `current_students`) VALUES
(1, 'Armed Security Certification', NULL, 'Armed Security Certification provides advanced training in firearm safety, legal use of force, and tactical response for professional armed security roles.', NULL, '[]', '<p>Armed Security Certification is an advanced training program designed for individuals pursuing or currently working in armed security positions. This certification equips participants with the knowledge, skills, and discipline required to carry and use firearms responsibly while performing protective duties.</p><p><br></p><p>The program covers firearm safety, weapons handling, marksmanship fundamentals, and safe storage, along with critical instruction on use-of-force laws, legal responsibilities, and ethical decision-making. Participants receive scenario-based training to develop judgment, threat assessment, and controlled response under high-stress conditions.</p><p><br></p><p>Additional focus areas include defensive tactics, situational awareness, communication, and coordination with law enforcement and emergency responders. Emphasis is placed on professionalism, accountability, and compliance with state and employer regulations.</p><p><br></p><p>Armed Security Certification is ideal for security professionals seeking armed assignments in corporate, residential, event, or high-risk environments. Completion of this training demonstrates readiness, competence, and commitment to maintaining the highest standards of safety and professionalism in armed protective services.</p>', 95.00, 10.00, NULL, 10, 2, 'group', 1, 0, 'services/gV79EbBPvkL16BCvQpqhb9gatnK9A8slmsv2OhsR.jpg', 1, 1, NULL, NULL, 0, 0, 0, '2026-01-12 12:52:47', '2026-04-22 11:53:59', '[]', 0),
(2, 'Force Science (De-escalation)', NULL, 'Force Science (De-escalation) training teaches evidence-based techniques to reduce conflict, manage behavior, and safely resolve high-stress encounters.', NULL, NULL, '<p>Force Science (De-escalation) training is a research-driven program focused on understanding human behavior, stress responses, and decision-making during high-risk or emotionally charged situations. This training equips participants with proven de-escalation strategies to prevent situations from escalating into physical force.</p><p><br></p><p>The course emphasizes verbal communication skills, body language awareness, distance management, and emotional regulation to influence behavior and maintain control. Participants learn how stress, perception, and cognitive limitations affect both responders and subjects, enabling safer and more effective interactions.</p><p><br></p><p>Scenario-based training reinforces practical application, teaching participants how to recognize warning signs, apply tactical patience, and choose appropriate responses while remaining legally and ethically compliant. The program also addresses use-of-force decision-making, accountability, and post-incident considerations.</p><p><br></p><p>Force Science (De-escalation) training is ideal for security officers, guards, supervisors, and individuals preparing for future security careers. It promotes professionalism, safety, and confidence while reducing risk, liability, and unnecessary use of force in protective service environments.</p>', 200.00, 100.00, NULL, 10, 2, 'group', 1, 0, 'services/lu6mY6GfYotlfByxzKUFWSukJnAC0U7JU5BTDtDD.jpg', 3, 1, NULL, NULL, 0, 0, 0, '2026-01-12 13:00:45', '2026-01-21 12:58:40', NULL, 0),
(3, 'ASP (Batons and Restraints)', NULL, 'ASP (Batons and Restraints) training teaches proper baton use and restraint techniques for safely controlling and managing resistant or aggressive individuals.', NULL, NULL, '<p>ASP (Batons and Restraints) training provides essential instruction in the safe, legal, and effective use of expandable batons and restraint devices for security and protective service professionals. This program emphasizes control, officer safety, and minimizing injury while managing confrontational or non-compliant situations.</p><p><br></p><p>Participants learn correct baton handling, striking zones, defensive techniques, and retention methods, along with proper application of restraints such as handcuffs and control devices. The training includes use-of-force guidelines, legal considerations, and decision-making to ensure actions are justified, proportional, and compliant with industry standards.</p><p><br></p><p>Scenario-based exercises help develop practical skills in de-escalation, subject control, team coordination, and situational awareness. Strong emphasis is placed on professionalism, accountability, and post-incident procedures, including reporting and subject care.</p><p><br></p><p>This training is ideal for security officers, guards, and individuals preparing for future security roles who require intermediate force options to maintain safety and control in dynamic environments.</p>', 100.00, 50.00, NULL, 10, 2, 'group', 0, 0, 'services/BkxqE6w72S4gcPBjXi06KoDngDnDNdh1cV4KJlne.png', 2, 1, NULL, NULL, 0, 0, 0, '2026-01-12 13:13:46', '2026-01-21 12:55:06', NULL, 0),
(4, 'NRA (Advanced Handgun Carry)', NULL, 'NRA Advanced Handgun Carry training develops advanced handgun skills, defensive carry techniques, and responsible decision-making for armed professionals and qualified civilians.', NULL, NULL, '<p>NRA Advanced Handgun Carry is an advanced-level firearms training program designed to enhance defensive handgun proficiency for individuals who carry a firearm for personal protection or professional duties. This course builds on fundamental handgun skills and focuses on real-world defensive carry scenarios.</p><p><br></p><p>Training includes advanced marksmanship, drawing from concealment, movement, use of cover, threat assessment, and defensive shooting techniques. Strong emphasis is placed on firearm safety, legal considerations, and responsible use of force in accordance with applicable laws.</p><p><br></p><p>Participants also learn mindset development, situational awareness, and decision-making under stress through scenario-based instruction. The course reinforces safe gun handling, accountability, and ethical responsibilities associated with carrying a handgun in public or professional environments.</p><p><br></p><p>NRA Advanced Handgun Carry is ideal for armed security personnel, protective service professionals, and experienced civilian carriers seeking to elevate their skills, confidence, and readiness while maintaining the highest standards of safety and professionalism.</p>', 150.00, 50.00, NULL, 10, 2, 'group', 0, 0, 'services/BrCipzeQ82L1MONsdaonwzCkq716sIzeq16tvNiU.jpg', 4, 1, NULL, NULL, 0, 0, 0, '2026-01-12 13:14:51', '2026-01-21 13:00:00', NULL, 0),
(6, 'Red Cross (First Aid, CPR, and AED)', NULL, 'Red Cross First Aid, CPR, and AED training equips individuals with lifesaving skills to respond confidently to medical emergencies.', NULL, NULL, '<p>Red Cross First Aid, CPR, and AED training provides comprehensive, nationally recognized instruction in emergency medical response for adults, children, and infants. This course prepares participants to act quickly and effectively during medical emergencies until professional help arrives.</p><p><br></p><p>Training covers essential first aid skills, including bleeding control, wound care, shock management, and response to common injuries and illnesses. Participants also receive hands-on instruction in cardiopulmonary resuscitation (CPR) and the safe, effective use of Automated External Defibrillators (AEDs).</p><p><br></p><p>Emphasis is placed on scene safety, emergency assessment, teamwork, and legal considerations such as Good Samaritan protections. Practical scenarios build confidence, coordination, and readiness in high-stress situations.</p><p><br></p><p>This training is ideal for security officers, guards, workplace teams, educators, and individuals preparing for future security or protective service roles. Certification demonstrates preparedness, responsibility, and commitment to safety in any environment.</p>', 300.00, 100.00, NULL, 10, 2, 'group', 0, 0, 'services/KWZGG0VFBFUh0fYE2nBcZwcB3brTGSyqhb2PavAq.jpg', 6, 1, NULL, NULL, 0, 0, 0, '2026-01-12 13:18:51', '2026-01-21 13:02:32', NULL, 0),
(7, 'ALERRT (Active Shooter)', NULL, 'ALERRT (Active Shooter) training prepares individuals to recognize, respond to, and survive active shooter and hostile threat incidents.', NULL, NULL, '<p>ALERRT (Advanced Law Enforcement Rapid Response Training) – Active Shooter training is a nationally recognized, research-based program designed to prepare individuals and organizations for rapid and effective response to active shooter and hostile threat events.</p><p><br></p><p>The training focuses on threat recognition, situational awareness, and immediate response strategies to reduce harm and save lives. Participants learn proven concepts such as movement during high-risk incidents, decision-making under stress, communication, coordination with first responders, and lifesaving medical response including bleeding control.</p><p><br></p><p>Scenario-based instruction reinforces practical application, helping participants understand attacker behavior, response priorities, and survival principles. The program emphasizes prevention, preparedness, and recovery while maintaining legal and ethical responsibilities.</p><p><br></p><p>ALERRT Active Shooter training is ideal for security officers, guards, school staff, healthcare workers, corporate teams, and individuals preparing for future security roles. Completion of this training increases confidence, readiness, and the ability to act decisively during critical incidents.</p>', 200.00, 50.00, NULL, 10, 2, 'group', 1, 0, 'services/zzHmHY9InmzwDnFkFocKwdNPsTCLP3vNFTmW9ZIm.jpg', 7, 1, NULL, NULL, 0, 0, 0, '2026-01-20 16:43:51', '2026-01-21 13:03:55', NULL, 0),
(20, 'Refuse to be the Victim', NULL, 'NRA personal safety and crime prevention program.', NULL, NULL, '<p><span style=\"color: rgb(71, 85, 105);\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</span></p>', NULL, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 12, 1, 'Refuse to be the Victim', NULL, 0, 0, 0, '2026-01-27 11:47:18', '2026-02-13 18:41:34', '[\"nra\"]', 0),
(26, 'Unarmed Guard Training', NULL, '4-hour comprehensive training program for unarmed security officers.', NULL, '[]', NULL, 100.00, 100.00, NULL, 10, 2, 'group', 0, 0, NULL, 1, 1, 'Unarmed Guard Training', NULL, 0, 0, 0, '2026-01-27 16:32:33', '2026-04-08 19:28:54', '[\"security_training\"]', 0),
(30, 'Enhanced Handgun Carry Permit', NULL, 'Tennessee’s Enhanced Handgun Carry Permit course now requires a minimum of 4 hours of classroom instruction plus a live‑fire range component.\r\nMost instructors deliver the class in about 6 hours total. \r\nTraining covers firearm safety, legal responsibilities, carry laws, and a required shooting qualification', NULL, '[]', '<p>The Tennessee Enhanced Handgun Carry Permit course is designed to provide citizens with a comprehensive understanding of handgun safety, legal responsibilities, and practical firearm handling.</p><p>The state now requires a minimum of 4 hours of classroom instruction combined with a mandatory live‑fire range component, resulting in an average total course time of approximately 6 hours, depending on class size and instructor pacing.</p><h3>Classroom Instruction (Minimum 4 Hours)</h3><p>The classroom portion focuses on the knowledge and judgment required to responsibly carry a handgun in Tennessee. Core topics include:</p><ul><li>Firearm safety fundamentals Safe handling, storage, transportation, and accident prevention.</li><li>Handgun operation and maintenance Understanding firearm parts, function, loading/unloading, and basic care</li><li>Tennessee laws governing carry and use of force Legal definitions, prohibited locations, duty to retreat, and lawful self‑defense</li><li>Interactions with law enforcement Best practices during traffic stops and public encounters while carrying.</li><li>Situational awareness and conflict avoidance Recognizing threats, de‑escalation principles, and responsible decision‑making.</li></ul><p><strong>This section ensures students understand not only how to carry a handgun, but when and why it is legally and ethically appropriate to use one.</strong></p><p><strong>&nbsp;Live‑Fire Range Component</strong></p><h3>Purpose of the EHCP Trainin</h3><p>The Enhanced Permit provides the broadest carry privileges in Tennessee and is recognized by more states through reciprocity agreements.&nbsp;</p><p>The course is structured to:</p><ul><li>Build safe, confident handgun carriers</li><li>Ensure students understand Tennessee’s legal framework</li><li>Prepare individuals for responsible concealed or open carry in daily life</li></ul><p><strong>Tennessee Enhanced Handgun Carry Permit – Eligibility Requirements</strong></p><p>To qualify for an EHCP, an applicant must meet all of the following:</p><p><strong>Basic Requirements</strong></p><p>• 	Be a Tennessee resident</p><p>• Be at least 21 years old (or 18 if active‑duty military or honorably discharged veteran)</p><p>• 	Provide proof of U.S. citizenship or lawful permanent residency</p><p><strong>&nbsp;Legal Eligibility</strong></p><p>• 	Have any felony convictions</p><p>• 	Be under indictment for a felony</p><p>• 	Have domestic violence convictions or pending charges</p><p>• 	Be a fugitive from justice</p><p>• 	Be an unlawful user of controlled substances</p><p>• 	Have recent court‑ordered or voluntary substance‑abuse hospitalization</p><p>(10 years if court‑ordered, 3 years if voluntary)</p><p>• 	Have two DUI convictions within 10 years, with one in the last 5 years</p><p>• 	Be under court jurisdiction for a DUI or Class A misdemeanor</p><p>• 	Have been adjudicated mentally defective, committed, or found to pose a substantial likelihood of harm within the last 7 years</p><p>• 	Have a stalking conviction or pending charge</p><p>• 	Be receiving Social Security disability due to alcohol, drug dependence, or mental disability</p><p>• 	Be subject to an Order of Protection</p><p>• 	Have been dishonorably discharged from the military</p><p>• 	Have renounced U.S. citizenship</p><p>• 	Be an illegal or unlawful alien</p><p><strong>&nbsp;Documentation &amp; Background Check</strong></p><p>• 	Submit fingerprints for TBI background check</p><p>• 	Provide photo ID</p><p>• 	Submit proof of completion of a state‑approved handgun safety course (your updated 4‑hour classroom + range format meets this requirement)</p><p><strong>Why Armed Guards Should Get the EHCP</strong></p><p>• 	Allows you to carry off‑duty</p><p>• 	Gives you reciprocity in many other states</p><p>• 	Protects you when traveling to and from work</p><p>• 	Many employers prefer or require it</p><p>• 	You avoid taking duplicate training</p><p>“Your armed guard firearms training already qualifies you for the EHCP.</p><p>You don’t need the EHCP class — just apply, get fingerprinted, and submit your armed guard training certificate.</p><p><strong>&nbsp;How an Armed Security Officer Gets Their EHCP in Tennessee</strong></p><p>“Your armed security registration only allows you to carry a firearm while you’re on duty for a licensed security company. If you want to carry off‑duty as a private citizen, you need an Enhanced Handgun Carry Permit. The process is separate, but it’s easy to complete while you’re becoming an armed guard.”</p><h2><strong>Now here’s the full, step‑by‑step process:</strong></h2><p>🔹 Step‑by‑Step: How an Armed Guard Gets Their EHCP</p><p><strong>1. Take the EHCP Training Course</strong></p><p>Even though you’re completing armed guard firearms training, Tennessee still requires the EHCP‑specific class, which includes:</p><p>• 	Minimum 4 hours classroom instruction</p><p>• 	Live‑fire qualification</p><p><strong>2. Submit Your EHCP Application Online</strong></p><p>Go to the Tennessee Department of Safety &amp; Homeland Security website and complete the handgun permit application.</p><p>You’ll choose:</p><p>• 	Enhanced Handgun Carry Permit (EHCP)</p><p><strong>3. Get Fingerprinted</strong></p><p>Even if you were fingerprinted for your armed security registration, you must be fingerprinted again for the EHCP because:</p><p>• 	PPS and DOS do not share fingerprint/background systems</p><p>• 	They are two different departments with separate requirements</p><p>Fingerprinting is done through the state’s approved vendor.</p><p><strong>4. Upload or Present Your Training Certificate</strong></p><p>After completing your EHCP class, you’ll receive a training certificate.</p><p>You must submit it to the Department of Safety as part of your application.</p><p><strong>5. Pay the State Fee</strong></p><p>The EHCP has its own state fee, separate from PPS registration fees.</p><p><strong>6. Wait for Approval</strong></p><p>Once the background check clears, the state will mail your permit.</p><p><br></p>', 100.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 7, 1, 'Enhanced Handgun Carry Permit', NULL, 0, 0, 0, '2026-01-27 16:32:33', '2026-03-26 23:58:31', '[\"security_training\"]', 0),
(33, 'Pistol Training', NULL, 'This foundational course teaches the essential knowledge, skills, and attitude for owning and operating a pistol safely. Perfect for beginners, the curriculum covers handgun parts, operation, ammunition, and shooting fundamentals. Students engage in live-fire range sessions to practice aiming, trigger control, and stance. Successful completion provides a national NRA certification, often serving as a prerequisite for advanced training or CCW permits.', NULL, '[]', '<p>The NRA Basic Pistol Shooting Course is the industry standard for new shooters and those looking to formalize their handgun training. This comprehensive program is designed to guide students through the safe handling and use of both revolvers and semi-automatic pistols. You will learn the mechanical operation of various handgun actions, the components of modern ammunition, and the NRA’s core rules for gun safety. A significant portion of the course focuses on marksmanship fundamentals, including proper grip, stance, sight alignment, and trigger press. Through supervised live-fire exercises on the range, students build confidence by practicing these skills in a controlled environment. Beyond shooting, the course covers cleaning, storage, and maintenance to ensure your firearm remains in peak condition. This course concludes with a written examination and a practical shooting qualification, resulting in a nationally recognized certificate of completion.</p>', 100.00, NULL, NULL, 10, 2, 'group', 0, 0, '2026-04-09-203650-OgbelwC8.png', 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 16:35:31', '2026-04-09 15:36:50', '[\"nra\"]', 0),
(34, 'Rifle Training', NULL, 'This comprehensive course teaches the knowledge, skills, and attitude necessary for the safe and proficient use of a rifle. Ideal for beginners, the curriculum covers rifle parts, operation, ammunition, and shooting fundamentals. Students engage in live-fire exercises to practice shooting from multiple positions. Successful completion includes a national NRA certification, proving a solid foundation in firearm safety and marksmanship.', NULL, '[]', '<p>The NRA Basic Rifle Course is a premier training program designed to take students from novice to confident rifle shooters. The curriculum begins with the NRA’s three fundamental rules for safe gun handling and moves into the mechanical operation of bolt-action, lever-action, pump-action, and semi-automatic rifles. You will learn the components of various rifle types, the technical aspects of ammunition, and how to properly clean and store your firearm. A significant portion of the course is dedicated to marksmanship, focusing on the five fundamentals: aiming, breath control, hold control, trigger control, and follow-through. Students will practice these skills during live-fire range sessions, learning to shoot from the benchrest, prone, sitting, kneeling, and standing positions. Whether you are interested in target shooting, hunting, or personal safety, this course provides the essential building blocks for all rifle disciplines.</p>', 100.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 16:39:12', '2026-04-08 19:32:28', '[\"nra\"]', 0),
(35, 'Shotgun Training', NULL, 'This comprehensive 10-hour course teaches the essential knowledge, skills, and attitude for the safe and proficient use of a shotgun. Designed for beginners, the curriculum covers shotgun parts, operation, ammunition, and shooting fundamentals. Students engage in live-fire exercises to practice hitting moving targets (clays) from various positions. Successful completion includes a national NRA certification, establishing a solid foundation for hunting or clay sports.', '<p><strong>Legal Status:</strong> Must be a law-abiding citizen or legal resident not prohibited by state or federal law from possessing a firearm.</p><p><strong>Age:</strong> Open to all ages; however, minors must be accompanied by a parent or legal guardian for the duration of the course.</p><p><strong>Equipment:</strong> A safe, functional shotgun (12-gauge or 20-gauge is standard). Many instructors provide \"loaner\" shotguns if needed.</p><p><strong>Ammunition:</strong> Minimum of 50 to 100 rounds of target-load shotshells (specific shot size usually #7.5, #8, or #9).</p><p><strong>Safety Gear: </strong>Mandatory wrap-around eye protection and high-decibel ear protection (electronic preferred).</p><p><strong>Performance:</strong> Must pass the NRA standardized shooting qualification and the written examination with a score of 90% or higher.</p>', '[]', '<p>The NRA Basic Shotgun Shooting Course is the definitive program for anyone new to the world of smoothbore firearms. This intensive training guides students through the mechanical operation of break-action, pump-action, and semi-automatic shotguns. You will learn the NRA’s primary rules for safe gun handling, the specific components of shotgun shells (shot, wad, powder, and primer), and how to identify various gauges and chokes. Unlike rifle or pistol shooting, shotgunning emphasizes \"pointing\" over \"aiming,\" and a significant portion of this course is dedicated to mastering the fundamentals: stance, gun ready position, swing to target, trigger pull, and follow-through. During supervised range sessions, students practice these techniques by engaging moving clay targets, building the hand-eye coordination necessary for success in field or competitive environments. The course also covers cleaning, maintenance, and the ethical responsibilities of shotgun ownership.</p>', 100.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 16:42:52', '2026-03-26 23:07:57', '[\"nra\"]', 0),
(37, 'Private Instruction', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit', NULL, NULL, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 16:48:31', '2026-04-08 18:04:14', '[\"nra\"]', 2),
(38, 'BLS Blended (BOTH) Hospital/Daycare Security', NULL, 'This 4.5-hour advanced CPR course is the foundational requirement for healthcare professionals and first responders in Tennessee. The curriculum covers high-quality CPR for all ages, AED operation, and relief of airway obstructions. Participants practice team-based response, critical thinking, and rapid assessment to improve patient outcomes in clinical and pre-hospital settings. Successful completion provides a 2-year certification that meets all 2026 healthcare licensing mandates.', '<p><strong>Professional Status:</strong> Intended for healthcare providers, public safety professionals, or students in medical/dental programs.</p><p><strong>Physical Ability:</strong> Must be capable of performing continuous chest compressions on a manikin at floor level and operating medical equipment like BVMs and AEDs.</p><p><strong>Pre-course Work</strong>: For \"Blended Learning\" sessions, the 2.5-hour online simulation modules must be completed prior to the in-person skills check.</p><p><strong>Performance Standards:</strong> Must achieve a minimum score of 80% on the written examination and demonstrate 100% proficiency in all required physical skill assessments.</p><p><strong>Renewal Eligibility:</strong> For recertification, participants must present a BLS certificate that is current or has expired within the last 30 days.</p><p><strong>Identification:</strong> A valid government-issued photo ID is required for identity verification at the start of class.</p>', '[\"Security at Hospitals and ChildCare required\"]', '<p>Basic Life Support (BLS) is the premier resuscitation training designed specifically for healthcare clinicians, including nurses, physicians, EMTs, and dental professionals. Updated to reflect the 2026 ECC guidelines, this course emphasizes a systematic approach to life-threatening emergencies. Key modules include performing a rapid visual survey for safety, checking responsiveness, and simultaneously assessing breathing and pulse. Participants will master high-quality chest compressions (100–120 per minute), advanced ventilation techniques using bag-valve-masks (BVM), and the integration of Automated External Defibrillators (AED) in multi-rescuer scenarios. The training utilizes scenario-based drills to sharpen critical thinking, team dynamics, and communication skills essential for high-performance resuscitation teams. Upon passing both the written exam and the hands-on skills evaluation, learners receive a nationally recognized 2-year digital certificate.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 17:19:46', '2026-03-27 16:37:59', '[\"red_cross\"]', 0),
(39, 'First Aid CPR AED', NULL, 'Gain life-saving skills with this comprehensive Red Cross certification. This course prepares you to recognize and care for emergencies involving adults, children, and infants, including cardiac arrest, choking, and sudden illness. Ideal for security professionals and workplace safety, this training covers CPR, AED use, and basic first aid. Successful completion results in a nationally recognized 2-year digital certificate that meets OSHA requirements.', '<p><strong>Physical Capability: </strong>Must be physically able to perform chest compressions on the floor and demonstrate hands-on skills such as AED pad placement and airway management.</p><p><strong>Attendance:</strong> Must attend the full instructional period and demonstrate proficiency in all required skills to the instructor.</p><p><strong>Blended Learning (if applicable):</strong> If enrolled in a \"Blended Learning\" course, the online portion must be completed and the certificate of completion presented before the in-person skills session.</p><p><strong>Performance: </strong>Must pass the final written assessment and skill evaluations with a score of 80% or higher.</p><p><strong>Identification:</strong> Bring a valid government-issued photo ID for certification verification.</p>', '[]', '<p>The Red Cross Adult and Pediatric First Aid/CPR/AED course provides the most up-to-date emergency cardiovascular care science to ensure you are ready to act in a crisis. You will learn to identify and manage critical emergencies such as head and neck injuries, burns, cuts, and environmental emergencies like heat stroke. The curriculum emphasizes high-quality chest compressions, the effective use of an Automated External Defibrillator (AED), and clearing obstructed airways for choking victims of all ages. This course is specifically designed for those who require a certification that meets strict regulatory or workplace safety standards. Training is delivered through a combination of lecture and hands-on practice with professional-grade manikins to ensure you leave the class with the confidence and skills necessary to save a life.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 17:25:03', '2026-03-27 16:34:37', '[\"red_cross\"]', 0),
(41, 'Baton', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', NULL, NULL, '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 17:39:37', '2026-02-13 18:44:28', '[\"asp_less_than_lethal\"]', 0),
(42, 'Enhanced Handgun Permit', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', '<p><br></p>', '[]', '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 17:43:06', '2026-03-26 21:44:18', '[\"handgun_carry_permit\"]', 0),
(43, 'Unarmed renewal (2 Year)', NULL, 'This state-mandated 2-hour refresher course is designed for currently licensed unarmed security professionals in Tennessee. The curriculum provides essential updates on the Use of Force Continuum, de-escalation strategies, and the legal powers and limitations of security personnel. Completion of this course satisfies the training requirements necessary to renew your unarmed registration card for another two-year period, ensuring you remain in full compliance with state law.', '<p><strong>Current License:</strong> Must hold a valid Tennessee Unarmed Security Guard License that is active or expired for no more than 90 days.</p><p><strong>Minimum Age: </strong>Must be at least 18 years old.</p><p><strong>Legal Status:</strong> Must be a U.S. citizen or a legally resident alien.</p><p><strong>Course Attendance:</strong> Must complete the full 2-hour instructional period and pass the final 50-question examination with a score of 70% or higher.</p><p><strong>Required Documentation:</strong> A digital head-and-shoulders photo (passport style) and a copy of your current guard card for verification.</p><p><strong>Technical:</strong> Access to a computer, tablet, or smartphone with a reliable internet connection (for online versions).&nbsp;</p>', '[]', '<p>Keep your professional credentials current with this comprehensive 2-hour recertification course, specifically tailored for Tennessee Unarmed Security Guards and Officers. Regulated by the Department of Commerce and Insurance, this training refreshes your knowledge of core security principles while introducing recent updates to state statutes and safety protocols. Key topics include mastering the Use of Force Continuum to ensure appropriate response levels, advanced verbal de-escalation techniques to defuse hostile situations without physical intervention, and a deep dive into Tennessee Private Protective Services rules (Rule 0780-05-02).. You will also review criminal and civil liability under TN Titles 39 and 40 to protect yourself and your employer. This course concludes with a mandatory exam to verify competency and provides the transcript required for your state renewal application.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-10 17:46:35', '2026-03-27 17:26:37', '[\"renewals\"]', 0),
(46, 'Restraints', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', NULL, NULL, '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:18:44', '2026-02-11 11:18:44', '[\"asp_less_than_lethal\"]', 0),
(47, 'OC  Spray', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', NULL, NULL, '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:20:31', '2026-02-13 18:46:16', '[\"asp_less_than_lethal\"]', 0),
(48, 'Flashlight', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', NULL, NULL, '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:22:04', '2026-02-13 18:46:58', '[\"asp_less_than_lethal\"]', 0),
(49, 'Active Shooter', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', NULL, NULL, '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:26:18', '2026-02-13 22:19:22', '[\"active_shooter\"]', 0),
(50, 'Unarmed  Security', NULL, 'This unarmed security certification training is for individuals who want to become licensed security guards. The course covers basic security procedures, observation skills, report writing, and state laws. It prepares students to work safely and professionally in unarmed security positions.', NULL, NULL, '<p>This <strong>unarmed security certification</strong> course provides the required training to become a l <strong>certified unarmed security guard</strong>. Individuals will learn essential security procedures, patrol techniques, emergency response protocols, observation and documentation skills, conflict management, and proper report writing. The course also covers state laws, use-of-force guidelines, ethics, and professional standards expected in the security industry.</p><p>Individuals will gain practical knowledge on how to handle real-world situations such as disturbances, suspicious activity, and workplace incidents while maintaining safety and professionalism. By the end of the course, participants will be prepared to perform their duties confidently, responsibly, and effectively in a variety of security settings including offices, retail locations, events, and residential properties.</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:30:29', '2026-02-25 17:11:14', '[\"security_guard\"]', 0),
(51, 'Armed  Security', NULL, NULL, NULL, NULL, '<p><br></p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:32:27', '2026-02-25 02:39:16', '[\"security_guard\"]', 0),
(53, 'De-escalation', NULL, NULL, NULL, NULL, '<p><br></p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:39:58', '2026-02-25 02:38:19', '[\"dallas_law\"]', 0),
(55, 'First Aid CPR AED', NULL, NULL, NULL, NULL, '<p><br></p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:43:59', '2026-02-25 02:37:44', '[\"dallas_law\"]', 0),
(56, 'Armed guard renewal (2 Year)', NULL, 'This mandatory 4-hour recertification course is required every two years for Tennessee armed security professionals. The program covers essential legal updates, weapon safety, and advanced marksmanship. Participants must complete a live-fire qualification on an approved silhouette target course, achieving a minimum score of 70%. This course provides the state-required training and qualification transcript needed to maintain your armed registration and legal carry status.', '<p><strong>Current License</strong>: Must hold a valid Tennessee Armed Security Guard License (active or within the 90-day grace period).</p><p><strong>Minimum Age: </strong>Must be at least 21 years old.</p><p><strong>Equipment:</strong> A reliable, duty-quality handgun (centerfire only; no rimfire). Note: You must qualify with each firearm type/caliber you intend to carry on duty.</p><p><strong>Ammunition: </strong>50 rounds of factory-new ammunition (no reloads). Some instructors may require up to 100 rounds for practice and qualification.</p><p><strong>Gear:</strong> A sturdy belt, a quality holster (outside-the-waistband preferred), at least two magazines or speed-loaders, and eye and ear protection.</p><p><strong>Performance: </strong>Must pass a written examination and achieve a minimum of 70% on the live-fire silhouette target course.</p><p><strong>Legal Status:</strong> Must maintain eligibility as a U.S. citizen or legally resident alien with no new disqualifying criminal convictions</p>', '[]', '<p>Ensure your professional credentials remain active with our comprehensive Armed Guard Renewal course, fully compliant with Tennessee Code Annotated § 62-35-122. This intensive 4-hour program is designed to refresh and refine the skills of current armed officers. The curriculum includes a deep dive into legal limitations on the use of a firearm, liability issues under Tennessee Titles 39 and 40, and modern de-escalation strategies. A major portion of the class is dedicated to hands-on marksmanship and safety protocols. You will be required to demonstrate proficiency through a live-fire qualification course on a commissioner-approved silhouette target, where a score of 70% or higher is mandatory for passing. Upon successful completion, you will receive the certified training transcript and documentation required for your biennial renewal application through the state’s online portal.</p>', 70.00, 30.00, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:46:33', '2026-03-27 17:19:53', '[\"renewals\"]', 0),
(58, 'Enhanced Armed Guard To be Determined', NULL, 'The Enhanced Armed Guard endorsement is required for officers carrying rifles or shotguns on duty. Applicants must have 5 years of full‑time law enforcement experience or 4 years of active‑duty military service in a combat MOS. This endorsement adds advanced firearms training and higher‑level decision‑making standards for elevated‑risk assignments.', NULL, NULL, '<p>The Tennessee Enhanced Armed Guard endorsement is a specialized credential for security professionals who carry rifles or shotguns in the performance of duty. This endorsement reflects a higher level of responsibility, training, and operational readiness than the standard armed guard license. It is designed for officers working in elevated‑risk environments where long‑gun deployment may be necessary, such as critical infrastructure, high‑value asset protection, or specialized response roles.</p><p>Enhanced Armed Guards are expected to demonstrate advanced judgment, disciplined firearm handling, and the ability to operate safely under pressure. The training emphasizes threat assessment, legal considerations, safe long‑gun operation, and the elevated standards required when carrying weapons with greater range, penetration, and tactical impact. Officers learn to evaluate rapidly changing situations, maintain strict muzzle discipline, and apply force only when legally justified and tactically appropriate.</p><p>This endorsement ensures that only individuals with proven high‑risk experience and advanced weapons proficiency are authorized to carry long guns in a security capacity. It reinforces professionalism, accountability, and the heightened expectations placed on officers trusted with greater firepower.</p><p>Eligibility Requirements</p><p>To qualify for the Tennessee Enhanced Armed Guard endorsement, applicants must meet one of the following background requirements:</p><p>• A minimum of 5 years of full‑time law enforcement service, or</p><p>• A minimum of 4 years of active‑duty military service in a combat MOS</p><p>These requirements ensure that Enhanced Armed Guards have real‑world experience in high‑stress environments where disciplined firearm handling and sound judgment are essential.</p><p>Training and Certification Requirements</p><p>Applicants must also hold a valid Tennessee Armed Guard registration, complete the Enhanced Armed Guard training course, pass the written exam, and successfully complete the advanced firearms qualification for long‑gun proficiency. All required state applications and fees must be submitted for approval.</p><p>Why This Endorsement Matters</p><p>The Enhanced Armed Guard credential signals that an officer has proven experience in tactical or high‑risk environments, advanced firearms discipline, and the decision‑making skills needed for elevated‑risk assignments. It is often required for roles involving critical infrastructure, executive protection, high‑value transport, and environments where long‑gun capability is part of the security posture.</p>', 150.00, 30.00, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:50:20', '2026-02-25 02:23:36', '[\"renewals\"]', 0),
(59, 'Dallas Law renewal (2 year)', NULL, 'Dallas Law requires any security officer working at a Tennessee establishment that serves alcohol to complete additional training in de‑escalation, safe restraint techniques, CPR, and First Aid. This training must be completed within 15 days of assignment and refreshed before renewal to ensure officers can safely manage incidents in alcohol‑related environments.', NULL, '[]', '<p>Dallas Law is a Tennessee statute that establishes enhanced training standards for security officers working in environments where alcohol is served. The law was created to improve safety, reduce unnecessary force, and ensure that officers assigned to bars, restaurants, nightclubs, event venues, and other on‑premises alcohol establishments are properly prepared for the unique risks associated with those settings.</p><p>Under Dallas Law, any armed or unarmed security officer working at an alcohol‑licensed location must complete additional training within 15 days of assignment. This training covers four essential areas: de‑escalation, safe restraint techniques, CPR, and First Aid. These skills address the most common challenges in alcohol‑related environments, where impaired judgment, heightened emotions, and unpredictable behavior can quickly escalate into medical or physical emergencies.</p><p>For annual renewal, officers must show that they have maintained this training and remain current on all Dallas Law requirements. This ensures that personnel working around alcohol can manage confrontations professionally, respond to medical issues effectively, and apply force only when necessary and appropriate. Dallas Law raises the standard of care for security operations in alcohol‑serving establishments and reinforces the officer’s responsibility to protect patrons, staff, and the public</p>', 60.00, 60.00, NULL, 10, 2, 'group', 1, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:53:26', '2026-03-16 13:06:29', '[\"renewals\"]', 0),
(60, 'Active Shooter Renewal Annual', NULL, 'A focused refresher course that reinforces critical response principles, threat recognition, rapid decision‑making, and coordinated action during active‑shooter events. Students review updated best practices, strengthen their situational awareness, and practice clear, decisive communication to improve safety and survivability.', NULL, NULL, '<p>Our Active Shooter Annual Renewal course reinforces the critical skills security professionals need to respond decisively during violent‑intruder events. This annual refresher focuses on updated best practices, threat recognition, rapid decision‑making, and coordinated movement under pressure. Students review core response principles, strengthen situational awareness, and practice clear, assertive communication that supports safer outcomes.</p><p>Through scenario‑based discussion and practical application, participants sharpen their ability to assess evolving threats, guide others to safety, and work effectively with responding law enforcement. This course ensures officers remain confident, current, and prepared to act with purpose when seconds matter.</p>', 75.00, 30.00, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-11 11:55:24', '2026-02-25 02:01:04', '[\"renewals\"]', 0),
(61, 'Force Science (De-Escalation)', NULL, 'A focused, scenario‑based class that teaches security officers how to stay calm, read behavior, and guide tense situations toward safe, voluntary compliance. Students learn practical communication skills, proven de‑escalation strategies, and a structured approach they can use immediately on shift.', NULL, NULL, '<p class=\"ql-align-justify\"><br></p><p>Our De‑Escalation Training course gives security officers the practical tools they need to confidently manage tense situations while maintaining safety, professionalism, and control. Built on proven principles from Force Science, behavioral psychology, and real‑world security operations, this class focuses on what officers actually face on shift—not theory, but usable skills.</p><p>Participants learn how to recognize behavioral cues, reduce emotional&nbsp;</p>', 200.00, 30.00, NULL, 10, 2, 'group', 1, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-12 10:39:28', '2026-02-25 01:57:45', '[\"force_science\"]', 0);
INSERT INTO `services` (`id`, `title`, `slug`, `short_description`, `requirements`, `sub_titles`, `description`, `price`, `deposit_amount`, `duration_hours`, `max_students`, `min_students`, `class_type`, `has_online_parts`, `testing_in_person`, `image`, `order`, `is_active`, `subcategory`, `location`, `requires_dallas_law`, `requires_active_shooter`, `requires_conditional_questions`, `created_at`, `updated_at`, `categories`, `current_students`) VALUES
(62, 'Less Lethal', NULL, 'Less‑than‑lethal tools are items security officers use to control someone without using deadly force. These tools are meant to stop a person, slow them down, or protect the officer while causing only temporary pain or discomfort. Common examples include pepper spray, batons, Tasers, and handcuffs. Tennessee requires officers to be trained before they can carry or use any of these tools on duty.', NULL, NULL, '<p>In Tennessee security work, “less‑than‑lethal” refers to tools that are designed to stop or control a person without causing death. These tools give security officers safe options to handle problems before a situation becomes dangerous. They are meant to cause temporary pain, discomfort, or loss of movement so the officer can take control without using deadly force.</p><p>Less‑than‑lethal tools are important because security officers often deal with people who are angry, aggressive, or refusing to follow instructions. These tools help officers protect themselves and others while lowering the chance of serious injury. Common less‑than‑lethal tools include pepper spray, batons, Tasers, and handcuffs. Each tool works in a different way, but all of them are meant to control a person safely and quickly.</p><p>Tennessee requires security officers to be properly trained before they can carry or use any less‑than‑lethal device. Training teaches officers how each tool works, when it is allowed, and how to use it without causing unnecessary harm. Officers also learn the laws about force, how to give clear commands, and how to check on a person after the tool is used.</p><p>Less‑than‑lethal tools help officers stay professional, make good decisions, and keep situations from getting out of control. By using these tools correctly, security officers can protect people, avoid injuries, and handle problems in a safe and responsible way.</p>', NULL, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-02-25 02:37:00', '2026-02-25 02:37:00', NULL, 0),
(63, 'bloodborne pathogens', NULL, 'Lorem Ipsum is a standard placeholder text used in the printing and typesetting industry, originating from a Latin text by Cicero, and serves to create a natural-looking block of text for design layouts.', NULL, '[]', '<p><strong>Lorem&nbsp;Ipsum&nbsp;is&nbsp;a&nbsp;standard&nbsp;placeholder&nbsp;text&nbsp;used&nbsp;in&nbsp;the&nbsp;printing&nbsp;and&nbsp;typesetting&nbsp;industry,&nbsp;originating&nbsp;from&nbsp;a&nbsp;Latin&nbsp;text&nbsp;by&nbsp;Cicero,&nbsp;and&nbsp;serves&nbsp;to&nbsp;create&nbsp;a&nbsp;natural-looking&nbsp;block&nbsp;of&nbsp;text&nbsp;for&nbsp;design&nbsp;layouts.</strong><span class=\"ql-cursor\">﻿﻿</span></p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-06 12:44:42', '2026-03-16 12:55:40', NULL, 0),
(64, 'Babysitting & Childcare', NULL, 'Lorem Ipsum is a standard placeholder text used in the printing and typesetting industry, originating from a Latin text by Cicero, and serves to create a natural-looking block of text for design layouts.', NULL, '[]', '<p>Lorem Ipsum is a standard placeholder text used in the printing and typesetting industry, originating from a Latin text by Cicero, and serves to create a natural-looking block of text for design&nbsp;</p>', 0.00, NULL, NULL, 10, 2, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-06 12:45:54', '2026-03-16 12:56:11', NULL, 0),
(65, 'ASP 4hr (less than lethal)', 'asp-4-hr', '\"This course trains security guards in the safe, legal use of less‑than‑lethal tools such as batons, OC spray, and electronic control devices. Students learn proper deployment, use‑of‑force limits, decision‑making, and liability awareness. Tennessee requires device‑specific certification before a guard may carry or use any less‑lethal weapon on duty.\"', '<p>Only Offered to Law Enforcement and Security Personnel.</p>', '[\"Flashlight\",\"OC Spray\",\"Baton\",\"Restrains\"]', '<p>In‑Depth Description: Tennessee Less‑Than‑Lethal Training for Security Guards Tennessee’s Less‑Than‑Lethal Training provides security guards with the knowledge and skills needed to safely carry and use non‑deadly defensive tools while on duty.&nbsp;The course focuses on legally compliant, responsible, and controlled use of devices such as batons, chemical agents (OC spray), and electronic control devices.Because these tools can still cause injury or create liability, Tennessee requires guards to complete device‑specific&nbsp;certification before carrying any less‑than‑lethal weapon in the performance of their duties.Training begins with an overview of Tennessee law, including the guard’s legal authority, use‑of‑force limitations, and civil and criminal liabilities associated with improper use.&nbsp;Students learn how less‑than‑lethal tools fit into the force continuum and how to make sound decisions under pressure.The course emphasizes threat assessment, situational awareness, and the importance of using verbal commands and de‑escalation before resorting to physical tools.Students practice safe striking zones for batons, correct spray patterns and distances for OC, and the operational and safety features of electronic control devices.&nbsp;The training also addresses aftercare responsibilities, reporting requirements, and how to document incidents to protect both the guard and their employer.By the end of the course, guards understand when and how to use less‑than‑lethal tools safely, effectively, and legally.Tennessee law requires guards to carry proof of certification for each device they are authorized to use, and employers must maintain training records.&nbsp;certification before carrying any less‑than‑lethal weapon in the performance of their duties.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>', 150.00, NULL, NULL, 20, 10, 'one-on-one', 0, 0, NULL, 27, 1, 'Molestiae delectus', 'Location A', 0, 0, 0, '2026-03-10 17:02:36', '2026-03-26 22:33:54', '[]', 0),
(67, 'Homeland security (6hr)', 'homeland-security', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', NULL, '[\"Enhanced handgun permit\"]', '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 100.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-10 18:02:01', '2026-03-10 18:02:31', NULL, 0),
(68, 'Active Shooter (8hr)', 'active-shooter', 'This comprehensive 8-hour course meets the Tennessee state mandate (Public Acts Chapter 367) for armed security guards in educational or religious environments. The training focuses on the ALERRT \"Avoid, Deny, Defend\" model, tactical movement, and medical \"Stop the Bleed\" techniques. This course provides the required documentation for guards to legally provide security services in Tennessee schools, churches, or childcare facilities.', NULL, '[\"Mandatory for Working Schools\"]', '<p>This intensive 8-hour program is designed to prepare security professionals to effectively respond to an active threat situation. Aligned with the Advanced Law Enforcement Rapid Response Training (ALERRT) standards, the curriculum covers the lifecycle of an active shooter event, including prevention, detection, and immediate response protocols. Key modules include \"Avoid, Deny, Defend\" strategies, tactical solo-officer movement, and room entry techniques. Participants will engage in hands-on scenario-based drills to practice neutralizing threats while minimizing risk to bystanders. Additionally, the course integrates life-saving medical training, such as tourniquet application and hemorrhage control, to manage injuries until emergency services arrive. Successful completion fulfills the Tennessee Department of Commerce and Insurance requirements for armed guards working in schools or religious institutions and includes the certified transcript for state portal upload.</p>', 150.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-10 18:04:43', '2026-04-09 10:51:01', '[]', 0),
(69, 'Forced science De-escalation', 'forced-science-de-escalation', 'This course teaches security guards how to safely manage conflict using communication, distance, and professional presence. Under Tennessee’s Dallas Law, guards must learn to recognize threats, calm tense situations, reduce use of force, and apply de‑escalation skills to protect the public, themselves, and their employer.”', NULL, '[]', '<p>De‑escalation training under Tennessee’s Dallas Law teaches security guards how to safely and effectively manage conflict without resorting to force.&nbsp;</p><p>This course focuses on recognizing early warning signs of aggression, understanding human behavior under stress, and using communication skills to calm tense situations.&nbsp;</p><p>Students learn how to maintain professional presence, control their own emotions, and apply verbal and non‑verbal techniques that reduce hostility and prevent violence.</p><p>The training emphasizes creating distance, using time as a tool, and applying structured decision‑making to avoid unnecessary physical confrontations.</p><p>&nbsp;Guards are taught how to assess threats, set boundaries, and redirect behavior while protecting themselves, the public, and their employer.&nbsp;</p><p>Dallas Law requires security personnel to prioritize safety, minimize harm, and use force only when absolutely necessary.&nbsp;</p><p>This course prepares guards to respond confidently, ethically, and legally in real‑world situations where de‑escalation is the safest and most effective option.</p><h2><strong>Is De‑escalation Training Required if You Work Around Alcohol? (Dallas Law – Tennessee)</strong></h2><p>Yes.</p><p>Under Tennessee’s Dallas Law, any security guard who works in a place where alcohol is served, sold, or consumed must complete state‑approved De‑escalation and Safe Restraint Training, even if they are unarmed.</p><p>This includes locations such as:</p><p>• Bars</p><p>• Nightclubs</p><p>• Restaurants</p><p>• Concert venues</p><p>• Festivals</p><p>• Sports arenas</p><p>• Any event or business with alcohol on the premises</p><p>If alcohol is present, Dallas Law applies.</p><p><strong>If you work security anywhere alcohol is served or consumed, Tennessee requires you to complete Dallas Law training. This includes de‑escalation, safe restraint, and duty‑to‑intervene training. You cannot work in these environments without it.”</strong></p><p>⭐ Why the Requirement Exists</p><p>Alcohol increases:</p><p>• Aggression</p><p>• Impaired judgment</p><p>• Emotional volatility</p><p>Because of this, Tennessee law requires guards to be trained to:</p><p>• Calm situations before they turn violent</p><p>• Use communication instead of force</p><p>• Protect the public and themselves</p><p>• Apply safe restraint only when absolutely necessary</p>', 80.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-10 18:06:49', '2026-03-26 22:23:14', '[]', 0),
(70, 'Dallas Law', 'dallas-law', 'Who Must Take Dallas Law Training?\r\nAny armed or unarmed security guard working at a business that serves alcohol for on‑premises consumption must complete this training.', NULL, '[\"First aid CPR AED\",\"De-escalation\",\"Restrains\"]', '<p><strong>What Tennessee’s Dallas Law Training Covers</strong></p><p>Tennessee’s Dallas Law requires security guards working in establishments that serve alcohol to complete a specialized training program.&nbsp;This training is designed to reduce violence, improve safety, and ensure guards understand how to manage high‑risk situations professionally and legally.</p><p><strong>1. De‑Escalation Techniques</strong></p><p>Learn how to calm tense situations using:</p><p>• Verbal communication</p><p>• Tone and body language</p><p>• Conflict‑management strategies</p><p>• Recognizing early signs of aggression</p><p>• Techniques to prevent situations from becoming violent</p><p><strong>2. Safe Restraint Techniques</strong></p><p>Covers how to safely control a person only when necessary, including:</p><p>• Approved physical control methods</p><p>• Avoiding excessive force</p><p>• Protecting yourself and the subject</p><p>• When restraint is legally justified</p><p><strong>&nbsp;3. Legal Powers and Limitations</strong></p><p>Understand your legal authority as a security guard, including:</p><p>• What you can and cannot do</p><p>• Tennessee criminal and civil liability laws</p><p>• Use‑of‑force rules</p><p>• Detention vs. arrest</p><p>• Your responsibilities under PPS regulations</p><p><strong>&nbsp;4. First Aid Training</strong></p><p>Basic emergency care skills such as:</p><p>• Treating injuries</p><p>• Responding to medical emergencies</p><p>• Keeping someone stable until EMS arrives</p><p><strong>5. CPR (Cardiopulmonary Resuscitation)</strong></p><p>Covers:</p><p>• Recognizing cardiac emergencies</p><p>• Performing CPR</p><p>• Safety considerations while providing aid</p>', 75.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-10 18:09:23', '2026-03-26 23:23:36', '[]', 0),
(71, 'Enhanced armed guard (1 Year)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:01:25', '2026-03-16 13:01:25', NULL, 0),
(72, 'Initial armed security (2 Years)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:01:56', '2026-03-16 13:01:56', NULL, 0),
(73, 'Initial unarmed security (2 Years)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:02:31', '2026-03-16 13:02:31', NULL, 0),
(74, 'Initial Dallas Law (2 Years)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:02:53', '2026-03-16 13:02:53', NULL, 0),
(75, 'Initial Active shooter (1 Year)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:03:15', '2026-03-16 13:03:15', NULL, 0),
(76, 'Initial BLS(Basic Life Support Hospital)  2 Years', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:04:08', '2026-03-16 13:04:28', NULL, 0),
(77, 'Less than Lethal (1 Time as long as you have the Certificate)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:05:06', '2026-03-16 13:05:06', NULL, 0),
(78, 'Active shooter (1 Year)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:05:50', '2026-03-16 13:05:50', NULL, 0),
(79, 'Enhanced armed guard (1 Year)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:07:04', '2026-03-16 13:07:04', NULL, 0),
(80, 'BLS Renewal (Hospital 2 Year)', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:12:05', '2026-03-16 13:12:05', NULL, 0),
(81, 'First AID CPR AED (2 Year )', NULL, NULL, NULL, '[]', NULL, NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-16 13:12:32', '2026-03-16 13:12:32', NULL, 0),
(87, 'Blood Borne Pathogens', NULL, 'This OSHA-compliant 2-hour course is designed for individuals with a reasonable risk of occupational exposure to blood or other potentially infectious materials (OPIM). The training covers the recognition of bloodborne diseases, including HIV, Hepatitis B, and Hepatitis C, alongside essential prevention strategies like Universal Precautions and the proper use of Personal Protective Equipment (PPE). Fulfills the annual training requirement under 29 CFR 1910.1030.', '<p><strong>Annual Requirement: </strong>This training must be completed upon initial job assignment and every 12 months thereafter for continued compliance.</p><p><strong>Exposure Risk:</strong> Intended for employees with \"reasonably anticipated\" contact with blood or OPIM as part of their regular job duties.</p><p><strong>Performance: </strong>Participants must pass a final knowledge assessment with a minimum score (typically 70% or higher) to verify competency in exposure control.</p><p><strong>Documentation: </strong>Students should provide their full legal name and job title for the employer’s permanent training records, which must be maintained for at least 3 years.</p><p><strong>Employer Integration:</strong> After completion, students are required to review their specific company \"Exposure Control Plan\" (ECP) to understand local site-specific hazards and disposal locations.</p>', '[]', '<p>This comprehensive awareness program provides the critical knowledge needed to identify and reduce the risks of occupational exposure to bloodborne pathogens in accordance with the U.S. Occupational Safety and Health Administration (OSHA) standards. The curriculum explores the epidemiology and symptoms of common bloodborne diseases, specifically focusing on HIV, HBV, and HCV. Students will learn to implement \"Universal Precautions\"—the practice of treating all human blood and certain body fluids as if they are known to be infectious. Key modules include the proper selection, use, and disposal of Personal Protective Equipment (PPE), safe handling of \"sharps,\" and effective decontamination procedures using biohazard spill kits. The course also details the necessary emergency actions to take following an exposure incident, including reporting protocols and post-exposure medical evaluations. Upon successful completion of the written examination, participants receive a certification valid for one year, satisfying the mandatory annual retraining requirement for at-risk employees.</p>', 40.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 16:27:25', '2026-03-27 16:27:25', '[\"red_cross\"]', 0),
(88, 'Babysitting & Childcare', NULL, 'Designed for youth ages 11–15, this premier course provides the knowledge and skills necessary to safely and responsibly care for infants and children. Participants learn leadership techniques, basic childcare (feeding/diapering), and how to develop a successful babysitting business. The curriculum also covers safety, behavior management, and basic first aid. Successful completion results in a nationally recognized Red Cross certification that parents trust.', '<p><strong>Age Range:</strong> Specifically designed for youth between the ages of 11 and 15.</p><p><strong>Attendance:</strong> Participants must attend all scheduled class sessions to receive certification.</p><p><strong>Participation: </strong>Students must actively engage in all skills sessions, interactive games, and group discussions.</p><p><strong>Physical Skill Demonstration:</strong> Must successfully demonstrate competency in observable skills, such as proper diapering, feeding, and holding techniques.</p><p><strong>Materials:</strong> Students should bring a notebook and pen; some instructors may request students bring a doll for additional practice if manikin numbers are limited.</p><p><strong>Optional Addition:</strong> While the standard course includes basic first aid, a separate Pediatric First Aid/CPR/AED certification is often recommended as a supplement for a more robust resume.</p>', '[]', '<p>The American Red Cross Babysitter\'s Training course is a comprehensive, youth-centered program that prepares tweens and teens for real-life caregiving scenarios. Through engaging videos, interactive group activities, and hands-on practice, students gain the confidence needed to handle everything from bedtime routines to emergency situations. Key modules include \"The Business of Babysitting,\" where students learn to market their services and interview with families, and \"Child Behavior,\" which teaches positive discipline and age-appropriate play. Practical skills like picking up and holding infants, bottle-feeding, and diaper changing are practiced using professional manikins. Additionally, the course teaches critical safety skills, including how to recognize hazards, prevent injuries, and respond to common first aid emergencies like choking or minor wounds. This class is the gold standard for young people looking to start their first job with a competitive, safety-focused edge.</p>', 100.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 16:30:31', '2026-03-27 16:30:31', '[\"red_cross\"]', 0),
(89, 'Initial armed security (2 Years)', NULL, 'This course provides the required training to become a Tennessee armed security officer. Students learn legal responsibilities, use‑of‑force limits, firearm safety, marksmanship, and proper carry procedures. Training includes classroom instruction and live‑fire qualification to prepare guards for safe, professional armed duty.', '<p><strong>&nbsp;Requirements to Become an Armed Security Officer in Tennessee</strong></p><p>To work as an armed security officer in Tennessee, you must meet all state requirements set by Private Protective Services (PPS) under the Department of Commerce &amp; Insurance.&nbsp;These requirements ensure that every armed guard is trained, qualified, and legally authorized to carry a firearm while on duty.</p><p>1. Be at Least 21 Years Old (Tennessee requires armed guards to be 21 or older at the time of registration.</p><p>&nbsp;2. Be a U.S. Citizen or Legally Authorized to Work (You must provide documentation proving citizenship or legal work authorization.</p><p>3. Have No Disqualifying Criminal History</p><p>You cannot have:</p><p>• 	Felony convictions</p><p>• 	Domestic violence convictions</p><p>• 	Certain misdemeanor offenses</p><p>• 	Active warrants or pending charges</p><p>A state and federal background check is required.</p><p>4. Complete the Unarmed Guard Training First</p><p>Before becoming armed, you must complete:</p><p>• 4 hours of unarmed guard training</p><p>• Submit fingerprints and application for unarmed registration</p><p>This is the foundation for all security work in Tennessee.</p><p>5. Complete the Initial Armed Guard Training</p><p>Tennessee requires:</p><p>• 	8 hours of classroom instruction</p><p>• 	4 hours of live‑fire range qualification</p><p>Training covers:</p><p>• 	Firearm safety</p><p>• 	Legal responsibilities</p><p>• 	Use‑of‑force laws</p><p>• 	Weapon handling</p><p>• 	Marksmanship</p><p>• 	Duty procedures</p><p>&nbsp;6. Pass the Firearms Qualification</p><p>You must demonstrate safe handling and accuracy with your duty firearm under instructor supervision.</p><p>7. Submit the Armed Guard Application</p><p>After training, you must:</p><p>• 	Submit the PPS armed guard application</p><p>• 	Provide your training certificate</p><p>• 	Pay the state fee</p><p>&nbsp;8. Maintain Your Registration</p><p>Armed guard registration must be renewed every two years, including:</p><p>• 	Refresher training</p><p>• 	Updated background check</p><p>• 	Renewal fee</p><p><strong>How to Get Your Enhanced Handgun Carry Permit (EHCP) After You Receive Your Armed Security Registration Card</strong></p><p>Once you receive your Tennessee Armed Security Officer Registration Card, you are legally allowed to carry a firearm only while on duty for your security employer. If you want to carry a handgun off‑duty, in public, or for personal protection, you should apply for the Enhanced Handgun Carry Permit (EHCP).</p><p>&nbsp;1. Use Your Armed Guard Firearms Training as Your EHCP Training</p><p>Your Tennessee armed guard firearms course already meets the state’s training requirement for the EHCP.</p><p>You do NOT need to take the EHCP class.</p><p>&nbsp;2. Apply Online for the EHCP</p><p>Go to the Tennessee Department of Safety &amp; Homeland Security website and complete the application for the Enhanced Handgun Carry Permit.</p><p>You will receive instructions for fingerprinting and document submission.</p><p>&nbsp;3. Get Fingerprinted</p><p>Even though you were fingerprinted for your armed guard registration, you must be fingerprinted again for the EHCP because the two departments do not share systems.</p><p><br></p><p>&nbsp;4. Submit Your Armed Guard Firearms Training Certificate</p><p>This certificate replaces the EHCP class certificate.</p><p>It proves you already completed the required training.</p><p><br></p><p>5. Pay the State Fee and Wait for Approval</p><p>Once your background check clears, the state will mail your EHCP card.</p><p><strong>Why You Should Get the EHCP</strong></p><p><strong>• 	Allows you to carry off‑duty</strong></p><p><strong>• 	Gives you reciprocity in many other states</strong></p><p><strong>• 	Protects you when traveling to and from work</strong></p><p><strong>• 	Many employers prefer or require it</strong></p><p><strong>• 	No extra class needed — your armed guard training already qualifie</strong></p>', '[]', '<p>Initial Armed Guard Training in Tennessee provides the essential knowledge, skills, and legal understanding required to work as an armed security officer.This state‑mandated course prepares students to carry a firearm responsibly while performing security duties and ensures they meet all Private Protective Services (PPS) requirements for armed registration.The training begins with a detailed review of Tennessee security laws, including the legal authority of an armed guard, use‑of‑force limitations, criminal and civil liability, and the responsibilities that come with carrying a firearm on duty.Students learn how to assess threats, make sound decisions under pressure, and apply force only when legally justified and absolutely necessary Classroom instruction covers firearm safety, weapon handling, safe storage, and proper carry procedures. Students are taught how to maintain situational awareness, manage high‑risk encounters, and interact professionally with the public, law enforcement, and clients.&nbsp;Emphasis is placed on communication, conflict management, and avoiding unnecessary escalation. The course includes hands‑on firearms training, where students practice marksmanship, weapon manipulation, and safe operation under the supervision of a certified instructor.&nbsp; Live‑fire qualification is required to demonstrate proficiency and ensure students can safely and accurately handle their firearm in real‑world conditions By the end of the course, students understand their legal responsibilities, demonstrate safe firearm handling, and are prepared to perform armed security duties with professionalism and confidence.&nbsp;Successful completion of this training is required before applying for Tennessee Armed Security Officer Registration</p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 16:57:49', '2026-03-27 16:57:49', '[\"security_training\"]', 0),
(90, 'Enhanced armed guard (1 Year)', NULL, 'This elite registration enhancement allows highly experienced Tennessee security professionals to carry and deploy rifles and shotguns while on duty. Restricted to veterans and former law enforcement, this 16-hour course covers advanced tactics, scenario-based deployment, and secure long-gun storage. This certification is required to legally provide high-level security services involving long firearms in the state of Tennessee.', '<p><strong>Prerequisite License:</strong> Must hold a current Tennessee Armed Security Guard registration in good standing.</p><h2><strong>Experience (One of the following):</strong></h2><p>Minimum of five (5) years of full-time law enforcement experience.</p><p>Minimum of four (4) years of active-duty military experience in a combat arms specialty with an honorable discharge.</p><p><strong>Evaluations:</strong> Must pass a fingerprint-based TBI/FBI criminal background check and a professional psychological evaluation.</p><p><strong>Course Performance:</strong> Must complete the 16-hour state-approved training and pass both a written exam and a live-fire qualification with a rifle or shotgun.</p><p><strong>Annual Renewal:</strong> Enhancement expires after one year; requires an 8-hour requalification course (minimum 6 hours of range time) and a fresh criminal background check.</p>', '[\"Must have Armed Guard Registration before applying.\"]', '<p>Established by the Tennessee General Assembly (Public Acts Chapter 344), the Enhanced Armed Guard registration is a specialized certification for security officers authorized to carry rifles or shotguns. The comprehensive 16-hour curriculum goes beyond standard pistol training, focusing on the legalities and tactics of long-gun deployment in offensive and defensive postures. Key modules include advanced marksmanship, safe weapon handling, and strict protocols for the secure storage of rifles and shotguns when they are not actively deployed. This training is designed for elite security roles that demand a higher level of threat response and preparedness. Upon successful completion of the written and practical examinations, eligible guards receive a registration enhancement that must be renewed annually through a mandatory 8-hour requalification course.</p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 16:59:34', '2026-03-27 17:36:12', '[\"security_training\"]', 0),
(91, 'Initial unarmed security (2 Years)', NULL, 'An initial unarmed guard in Tennessee must be at least 18, a U.S. citizen or resident alien, and demonstrate good moral character. The role requires a state-certified 4-hour training course covering legal powers, patrol duties, and emergency procedures. Applicants must pass a fingerprint-based TBI/FBI background check. Once the $70 fee and application are submitted via the CORE portal, guards can often work for up to 75 days while their permanent registration is processing.', '<h2><strong>Legal Requirements &amp; Eligibility</strong></h2><p><strong>Age/Status:</strong> Must be at least 18 years old and a U.S. citizen or resident alien.</p><p><strong>Character:</strong> Must have \"good moral character\" with no felony convictions or misdemeanors involving violence, theft, or drugs within the last five years.</p><p><strong>Fingerprinting:</strong> Requires an electronic background check through IdentoGO for TBI and FBI clearance.</p><p><strong>Less-Lethal Gear:</strong> Carrying a baton, chemical spray, or stun gun requires an additional 4-hour certification specific to each device.</p><h2><strong>Typical Work Environments</strong></h2><p>Licensed guards in Tennessee commonly find employment in:</p><p><strong>Retail &amp; Malls:</strong> Preventing shoplifting and managing crowds.</p><p><strong>Government Sites:</strong> Serving as patrol monitors for municipal or state facilities.</p><p><strong>Healthcare &amp; Education: </strong>Monitoring access and ensuring safety for patients or students.</p><p><strong>Residential Communities:</strong> Patrolling gated complexes and monitoring suspicious activity.</p>', '[]', '<p>In Tennessee, an Initial Unarmed Security Guard is a licensed professional responsible for the protection of persons and property through non-lethal means Regulated by the Tennessee Private Protective Services (PPS) division, the role transitions a civilian into a state-registered officer through specific legal and technical training</p><h2><strong>Core Responsibilities</strong></h2><p><strong>Visible Deterrence &amp; Patrol:</strong> Conducts regular foot or vehicle patrols to identify signs of intrusion, unauthorized entry, or safety hazards.</p><p><strong>Access Control:</strong> Manages entry and exit points by verifying credentials, monitoring visitor logs, and checking surveillance feeds (CCTV).</p><p><strong>Incident Management: </strong>Responds to alarms, fires, and medical emergencies. While unarmed, they act as the \"eyes and ears\" for law enforcement, stabilizing scenes and coordinating with first responders.</p><p><strong>Documentation:</strong> Maintains Daily Activity Reports (DARs) and prepares detailed incident reports that can be used as evidence in legal proceedings.&nbsp;</p><h2><strong>Mandatory Training (4-Hour Curriculum)&nbsp;</strong></h2><p>To qualify for registration, applicants must pass a 4-hour state-certified course. The curriculum is divided into four critical pillars:&nbsp;</p><p><strong>1. Orientation (1 hr):</strong> Professionalism, ethics, and the role of security in private risk management.</p><p><strong>2. Legal Powers &amp; Limitations (1 hr):</strong> Authority on private property, citizen\'s arrest, and civil/criminal liability.</p><p><strong>3. Emergency Procedures (1 hr):</strong> Fire prevention, first aid basics, and evacuation protocols.</p><p><strong>4. General Duties (1 hr):</strong> Patrol techniques, report writing, and public relations.&nbsp;</p><h2><br></h2><p><br></p>', 75.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:01:04', '2026-03-27 18:26:43', '[\"security_training\"]', 0),
(92, 'Initial Dallas Law (2 Years)', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s,', NULL, '[]', '<p class=\"ql-align-justify\"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><br></p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:01:46', '2026-03-27 17:01:46', '[\"security_training\"]', 0),
(93, 'Initial Active shooter (1 Year)', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s,', NULL, '[]', '<p class=\"ql-align-justify\"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><br></p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:02:32', '2026-03-27 17:02:32', '[\"security_training\"]', 0),
(94, 'Initial BLS(Basic Life Support Hospital)  2 Years', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s,', NULL, '[]', '<p class=\"ql-align-justify\"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><br></p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:03:15', '2026-03-27 17:03:15', '[\"security_training\"]', 0),
(95, 'Less than Lethal (1 Time as long as you have the Certificate)', NULL, '“This course trains security guards in the safe, legal use of less‑than‑lethal tools such as batons, OC spray, and electronic control devices. Students learn proper deployment, use‑of‑force limits, decision‑making, and liability awareness. Tennessee requires device‑specific certification before a guard may carry or use any less‑lethal weapon on duty.”', NULL, '[\"Only Offered to Law Enforcement and Security Personnel\"]', '<p>In‑Depth Description: Tennessee Less‑Than‑Lethal Training for Security Guards Tennessee’s Less‑Than‑Lethal Training provides security guards with the knowledge and skills needed to safely carry and use non‑deadly defensive tools while on duty.&nbsp;The course focuses on legally compliant, responsible, and controlled use of devices such as batons, chemical agents (OC spray), and electronic control devices. Because these tools can still cause injury or create liability, Tennessee requires guards to complete device‑specific&nbsp;certification before carrying any less‑than‑lethal weapon in the performance of their duties.Training begins with an overview of Tennessee law, including the guard’s legal authority, use‑of‑force limitations, and civil and criminal liabilities associated with improper use.&nbsp;Students learn how less‑than‑lethal tools fit into the force continuum and how to make sound decisions under pressure.The course emphasizes threat assessment, situational awareness, and the importance of using verbal commands and de‑escalation before resorting to physical tools. Students practice safe striking zones for batons, correct spray patterns and distances for OC, and the operational and safety features of electronic control devices.&nbsp;The training also addresses aftercare responsibilities, reporting requirements, and how to document incidents to protect both the guard and their employer. By the end of the course, guards understand when and how to use less‑than‑lethal tools safely, effectively, and legally. Tennessee law requires guards to carry proof of certification for each device they are authorized to use, and employers must maintain training records. certification before carrying any less‑than‑lethal weapon in the performance of their duties.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>', 150.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:03:53', '2026-03-27 17:23:37', '[\"security_training\"]', 0),
(96, 'BLS Renewal (Hospital 2 Year)', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s,', '<p class=\"ql-align-justify\"><br></p>', '[]', '<p class=\"ql-align-justify\"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><br></p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:07:04', '2026-03-27 17:07:04', '[\"renewals\"]', 0),
(97, 'First AID CPR AED (2 Year )', NULL, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s,', NULL, '[]', '<p class=\"ql-align-justify\"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><br></p>', NULL, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:07:32', '2026-03-27 17:07:32', '[\"renewals\"]', 0),
(99, 'Handgun Carry Permit', NULL, 'Tennessee’s Enhanced Handgun Carry Permit course now requires a minimum of 4 hours of classroom instruction plus a live‑fire range component.Most instructors deliver the class in about 6 hours total. Training covers firearm safety, legal responsibilities, carry laws, and a required shooting qualification.', '<p><strong>Basic Requirements</strong></p><p>• Be a Tennessee resident</p><p>• Be at least 21 years old (or 18 if active‑duty military or honorably discharged veteran)</p><p>• Provide proof of U.S. citizenship or lawful permanent residency</p><p><strong>&nbsp;Legal Eligibility</strong></p><p>Applicant must NOT:</p><p>• Have any felony convictions</p><p>• Be under indictment for a felony</p><p>• Have domestic violence convictions or pending charges</p><p>• Be a fugitive from justice</p><p>• Be an unlawful user of controlled substances</p><p>• Have recent court‑ordered or voluntary substance‑abuse hospitalization</p><p>(10 years if court‑ordered, 3 years if voluntary)</p><p>• Have two DUI convictions within 10 years, with one in the last 5 years</p><p>• Be under court jurisdiction for a DUI or Class A misdemeanor</p><p>• Have been adjudicated mentally defective, committed, or found to pose a substantial likelihood of harm within the last 7 years</p><p>• Have a stalking conviction or pending charge</p><p>• Be receiving Social Security disability due to alcohol, drug dependence, or mental disability</p><p>• Be subject to an Order of Protection</p><p>• Have been dishonorably discharged from the military</p><p>• Have renounced U.S. citizenship</p><p>• Be an illegal or unlawful alien</p><p><strong>&nbsp;Documentation &amp; Background Check</strong></p><p>• Submit fingerprints for TBI background check</p><p>• Provide photo ID</p><p>• Submit proof of completion of a state‑approved handgun safety course (your updated 4‑hour classroom + range format meets this requirement)</p><p><strong>Why Armed Guards Should Get the EHCP</strong></p><p>• Allows you to carry off‑duty</p><p>• Gives you reciprocity in many other states</p><p>• Protects you when traveling to and from work</p><p>• Many employers prefer or require it</p><p>• You avoid taking duplicate training</p><p>“Your armed guard firearms training already qualifies you for the EHCP.</p><p>You don’t need the EHCP class — just apply, get fingerprinted, and submit your armed guard training certificate.</p><p><strong>&nbsp;How an Armed Security Officer Gets Their EHCP in Tennessee</strong></p><p>“Your armed security registration only allows you to carry a firearm while you’re on duty for a licensed security company. If you want to carry off‑duty as a private citizen, you need an Enhanced Handgun Carry Permit. The process is separate, but it’s easy to complete while you’re becoming an armed guard.”</p><p><strong>Now here’s the full, step‑by‑step process:</strong></p><p>🔹 Step‑by‑Step: How an Armed Guard Gets Their EHCP</p><p><strong>1. Take the EHCP Training Course</strong></p><p>Even though you’re completing armed guard firearms training, Tennessee still requires the EHCP‑specific class, which includes:</p><p>• Minimum 4 hours classroom instruction</p><p>• Live‑fire qualification</p><p><strong>2. Submit Your EHCP Application Online</strong></p><p>Go to the Tennessee Department of Safety &amp; Homeland Security website and complete the handgun permit application.</p><p>You’ll choose:</p><p>• Enhanced Handgun Carry Permit (EHCP)</p><p><strong>3. Get Fingerprinted</strong></p><p>Even if you were fingerprinted for your armed security registration, you must be fingerprinted again for the EHCP because:</p><p>• PPS and DOS do not share fingerprint/background systems</p><p>• They are two different departments with separate requirements</p><p>Fingerprinting is done through the state’s approved vendor.</p><p><strong>4. Upload or Present Your Training Certificate</strong></p><p>After completing your EHCP class, you’ll receive a training certificate.</p><p>You must submit it to the Department of Safety as part of your application.</p><p><strong>5. Pay the State Fee</strong></p><p>The EHCP has its own state fee, separate from PPS registration fees.</p><p><strong>6. Wait for Approval</strong></p><p>Once the background check clears, the state will mail your permit.</p>', '[]', '<p>The Tennessee Enhanced Handgun Carry Permit course is designed to provide citizens with a comprehensive understanding of handgun safety, legal responsibilities, and practical firearm handling.&nbsp;The state now requires a minimum of 4 hours of classroom instruction combined with a mandatory live‑fire range component, resulting in an average total course time of approximately 6 hours, depending on class size and instructor pacing.</p><h2><strong>Classroom Instruction (Minimum 4 Hours)</strong></h2><p>The classroom portion focuses on the knowledge and judgment required to responsibly carry a handgun in Tennessee. Core topics include:</p><p>• Firearm safety fundamentals Safe handling, storage, transportation, and accident prevention.</p><p>• Handgun operation and maintenance Understanding firearm parts, function, loading/unloading, and basic care</p><p>• Tennessee laws governing carry and use of force Legal definitions, prohibited locations, duty to retreat, and lawful self‑defense</p><p>• Interactions with law enforcement Best practices during traffic stops and public encounters while carrying.</p><p>• Situational awareness and conflict avoidance Recognizing threats, de‑escalation principles, and responsible decision‑making.</p><h2><strong>This section ensures students understand not only how to carry a handgun, but when and why it is legally and ethically appropriate to use one.</strong></h2><p><strong>&nbsp;Live‑Fire Range Component</strong></p><p>Following classroom instruction, students complete a required practical shooting portion. This includes:</p><p>• Safe range conduct and firearm handling</p><p>• Live‑fire exercises or qualification demonstrating basic proficiency</p><p>• Instructor evaluation of accuracy, control, and safe operation</p><p><strong>Purpose of the EHCP Trainin</strong></p><p>The Enhanced Permit provides the broadest carry privileges in Tennessee and is recognized by more states through reciprocity agreements.&nbsp;</p><p>The course is structured to:</p><p>• Build safe, confident handgun carriers</p><p>• Ensure students understand Tennessee’s legal framework</p><p>• Prepare individuals for responsible concealed or open carry in daily life</p><p><strong>Tennessee Enhanced Handgun Carry Permit – Eligibility Requirements</strong></p><p>To qualify for an EHCP, an applicant must meet all of the following:</p><p><br></p><p><br></p>', 100.00, NULL, NULL, 10, 1, 'group', 0, 0, NULL, 0, 1, NULL, NULL, 0, 0, 0, '2026-03-27 17:52:51', '2026-03-27 18:24:37', '[\"homeland_security\"]', 0),
(101, 'Qui nulla sed archit', 'ffdghnulla', 'Ad nobis voluptate sed Nam quidem molestias at est aut sed sunt quasi voluptatem', '<p>Neque velit, sapient.</p>', '[]', '<p>Sed id ut at ex quas.</p>', 431.00, 72.00, NULL, 34, 1, 'one-on-one', 1, 0, NULL, 1, 1, 'Nisi qui natus qui d', 'Location B', 1, 1, 0, '2026-04-09 15:13:04', '2026-04-09 15:13:04', '[\"nra\",\"homeland_security\",\"active_shooter\",\"force_science\",\"dallas_law\"]', 0);

-- --------------------------------------------------------

--
-- Table structure for table `service_bookings`
--

CREATE TABLE `service_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `class_schedule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `remaining_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','deposit_paid','fully_paid','refunded') NOT NULL DEFAULT 'pending',
  `booking_type` enum('group','one-on-one') NOT NULL DEFAULT 'group',
  `number_of_students` int(11) NOT NULL DEFAULT 1,
  `group_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_relationships`
--

CREATE TABLE `service_relationships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_service_id` bigint(20) UNSIGNED NOT NULL,
  `linked_service_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_relationships`
--

INSERT INTO `service_relationships` (`id`, `parent_service_id`, `linked_service_id`, `order`, `created_at`, `updated_at`) VALUES
(166, 101, 51, 0, '2026-04-09 15:13:04', '2026-04-09 15:13:04'),
(167, 101, 41, 1, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(168, 101, 87, 2, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(169, 101, 80, 3, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(170, 101, 59, 4, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(171, 101, 53, 5, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(172, 101, 79, 6, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(173, 101, 90, 7, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(174, 101, 69, 8, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(175, 101, 99, 9, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(176, 101, 75, 10, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(177, 101, 76, 11, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(178, 101, 74, 12, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(179, 101, 92, 13, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(180, 101, 91, 14, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(181, 101, 95, 15, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(182, 101, 46, 16, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(183, 101, 34, 17, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(186, 101, 3, 20, '2026-04-09 15:13:05', '2026-04-09 15:13:05'),
(187, 101, 2, 21, '2026-04-09 15:13:06', '2026-04-09 15:13:06'),
(188, 101, 6, 22, '2026-04-09 15:13:06', '2026-04-09 15:13:06'),
(189, 101, 7, 23, '2026-04-09 15:13:06', '2026-04-09 15:13:06'),
(190, 101, 30, 24, '2026-04-09 15:13:06', '2026-04-09 15:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1GXQ2K7YsMl2YHO3ub317xCgA21JHA1uxH07BrOO', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWEVRTU1YZkNLZXdPUHgzZmI1dWZUUGN2RmZUa3hHRDVwTm9ZbHdXWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1777393375);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `header_logo` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `quickbooks_client_id` varchar(255) DEFAULT NULL,
  `quickbooks_client_secret` varchar(255) DEFAULT NULL,
  `quickbooks_company_id` varchar(255) DEFAULT NULL,
  `quickbooks_environment` varchar(255) DEFAULT 'sandbox',
  `quickbooks_access_token` text DEFAULT NULL,
  `quickbooks_refresh_token` text DEFAULT NULL,
  `quickbooks_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `bank_api_provider` varchar(255) DEFAULT NULL,
  `bank_api_key` varchar(255) DEFAULT NULL,
  `bank_api_secret` varchar(255) DEFAULT NULL,
  `bank_account_id` varchar(255) DEFAULT NULL,
  `bank_sync_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jayson_bio` text DEFAULT NULL,
  `kenny_bio` text DEFAULT NULL,
  `square_application_id` varchar(255) DEFAULT NULL,
  `square_access_token` varchar(255) DEFAULT NULL,
  `square_location_id` varchar(255) DEFAULT NULL,
  `square_environment` varchar(255) DEFAULT 'sandbox',
  `square_enabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `header_logo`, `footer_logo`, `favicon`, `phone`, `email`, `address`, `facebook_url`, `twitter_url`, `instagram_url`, `linkedin_url`, `youtube_url`, `quickbooks_client_id`, `quickbooks_client_secret`, `quickbooks_company_id`, `quickbooks_environment`, `quickbooks_access_token`, `quickbooks_refresh_token`, `quickbooks_enabled`, `bank_api_provider`, `bank_api_key`, `bank_api_secret`, `bank_account_id`, `bank_sync_enabled`, `created_at`, `updated_at`, `jayson_bio`, `kenny_bio`, `square_application_id`, `square_access_token`, `square_location_id`, `square_environment`, `square_enabled`) VALUES
(1, 'settings/Gdsv46RCzD3YqQiuTdf0Q0lzRSNsD224rO0C6PxL.png', 'settings/xu8sOfV6uFL63fsdP9HxOizUO0VEGUXDYnOukCyo.png', 'settings/hAcwmfQmRtDi3elHarODMcslw4ikTigtJFThuUPh.png', '615-554-1131', 'tnvetsecsvctrng@gmail.com', '123 Security Way,\r\nNashville, TN 37201', NULL, NULL, NULL, NULL, NULL, 'AB0rfAJ4fBfjUdm23xtDaBr8R95pzD0D0MNqeSGuvsVpURht4a', 'l6vtWPm9Q8RuJlLxy1T0gSX9vIt6mD0EY2S3tj9L', '9341456376232298', 'sandbox', 'eyJhbGciOiJkaXIiLCJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwieC5vcmciOiJIMCJ9..dCITLbkmMWksSpidMJHSYw.BeaFBB6vgED8cCaJBLV61QVpt2Bu8o0ZMCW6LsFLy5vSImvMWaW6rmrkUSouuJIgdFFS4GvUnvcSyr6yZHXI0yhuHISGBHVerh1hmAg04-WYwWYkH_-JnEd6QykahRFoUOetI74BOKX8kaqXYXJSDawZpaHi1aojAqJ1Sr1Xe2cROKjtkgA75KjaJRGz6rvsFN7J8kU1AGh9E9Vx7eDB746Iwj66WZWUrG9hL_37Z3S6kmtuNu1U-AI3r1MxMnWMZ80ft2vG30NnS3MbC17myc5lz9YaNOemraa0XEI9caejW9hbC2imhy7CvfYq4f2wwYepzfzpmDv-HCAYMLrepCF_hUBqYnsZf1CZaeAlGXxVSrC3W-eIXeMEPE9TeH7qxLNfc0QUhZkk3Ch6RTfw1Y1M2xA4syNrNR-XKTa4DV_vzfE3xoPvbwNAToW5AfYiCnvs6IdQNy1w6OzZ2kluHfS0jGfdBzKjHek912Dgo0K9JF0LBdJHOguTS3ZCApGw.JNpmHU1-G_hvBa5zLL_sSg', 'RT1-189-H0-17844158501zbp4nylg1k2s8kek7hi', 1, NULL, NULL, NULL, NULL, 0, '2026-01-12 13:30:50', '2026-04-09 11:22:43', NULL, NULL, NULL, NULL, NULL, 'sandbox', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_picture`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@admin.com', 'profiles/Sb70mG22z3uNt3cNlqo9vjUcbn43yxKDTe72vGtj.png', NULL, '$2y$12$T9.zhU.ZeaSdCwIQi2RaWO78E.qibh7giBZyXVvcQ8jSwuWmXWAZa', 'YgIhs8bgk9sjVKD53Rhv4NVIALhyJbYYTKDYtsRl7GVnPylvmP8DyJcUeYmo', '2026-01-12 12:47:43', '2026-04-08 18:27:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_schedules_class_date_start_time_index` (`class_date`,`start_time`),
  ADD KEY `class_schedules_service_id_class_date_index` (`service_id`,`class_date`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_booking_id_status_index` (`booking_id`,`status`),
  ADD KEY `payments_customer_id_payment_date_index` (`customer_id`,`payment_date`),
  ADD KEY `payments_transaction_id_index` (`transaction_id`);

--
-- Indexes for table `security_company_links`
--
ALTER TABLE `security_company_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_slug_unique` (`slug`);

--
-- Indexes for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `service_bookings_service_id_foreign` (`service_id`),
  ADD KEY `service_bookings_class_schedule_id_foreign` (`class_schedule_id`);

--
-- Indexes for table `service_relationships`
--
ALTER TABLE `service_relationships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_relationships_parent_service_id_linked_service_id_unique` (`parent_service_id`,`linked_service_id`),
  ADD KEY `service_relationships_linked_service_id_foreign` (`linked_service_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `security_company_links`
--
ALTER TABLE `security_company_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_bookings`
--
ALTER TABLE `service_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `service_relationships`
--
ALTER TABLE `service_relationships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `class_schedules_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `service_bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD CONSTRAINT `service_bookings_class_schedule_id_foreign` FOREIGN KEY (`class_schedule_id`) REFERENCES `class_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_relationships`
--
ALTER TABLE `service_relationships`
  ADD CONSTRAINT `service_relationships_linked_service_id_foreign` FOREIGN KEY (`linked_service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_relationships_parent_service_id_foreign` FOREIGN KEY (`parent_service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
