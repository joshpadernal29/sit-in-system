-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 10:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sit_in_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `priority` enum('general','urgent','academic') DEFAULT 'general',
  `target_audience` enum('all','bsit','bscs') DEFAULT 'all',
  `is_active` tinyint(1) DEFAULT 1,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `priority`, `target_audience`, `is_active`, `date_posted`) VALUES
(2, 'Laboratory Rules, Regulations, and Proper Decorum', '📢 OFFICIAL ANNOUNCEMENT: CCS LABORATORY GUIDELINES\r\nTO: All CCS Students (BSIT & BSCS)\r\n\r\nFROM: College of Computer Studies Laboratory Management\r\n\r\nSUBJECT: Laboratory Rules, Regulations, and Proper Decorum\r\n\r\n1. NO FOOD OR DRINKS: Strictly no snacks or beverages (including water) near the workstations to prevent permanent equipment damage.\r\n\r\n2. ACADEMIC USE ONLY: Lab computers are for programming, research, and assignments. Gaming and social media browsing are strictly prohibited.\r\n\r\n3. HARDWARE INTEGRITY: Do not disconnect peripherals (mice, keyboards, monitors). Report all technical issues to the Lab Technician on duty immediately.\r\n\r\n4. NO UNAUTHORIZED SOFTWARE: Do not install any software, IDEs, or games without prior approval from the lab management.\r\n\r\n5. DATA BACKUP POLICY: Most systems use \"Deep Freeze.\" Any files saved to the local C: drive will be deleted upon restart. Always backup to GitHub or Cloud storage.\r\n\r\n6. SILENT ZONE: Maintain a quiet environment. Collaborative work must be done in a whisper to respect other students\' focus.\r\n\r\n7. NETWORK ETHICS: High-bandwidth activities like torrenting or streaming non-academic content are prohibited to ensure stable connectivity for all.\r\n\r\n8. SECURITY & PRIVACY: Always log out of your personal accounts (GitHub, Gmail, etc.) and your student workstation profile before leaving.\r\n\r\n9. CLEAN AS YOU GO (CLAYGO): Arrange your chair and ensure your station is free of trash before exiting the laboratory.\r\n\r\n10. DRESS CODE: Students must be in their proper college uniform or lab gown as prescribed by the department to gain entry.\r\n\r\nPlease be guided accordingly.', 'general', 'all', 1, '2026-04-11 07:42:37'),
(3, 'LAB 542 (renovation)', 'There will be no sit in for lab 542 as its currently under renovation', 'urgent', 'all', 1, '2026-04-11 09:32:25'),
(4, 'notif test', 'testing', 'general', 'all', 1, '2026-04-11 09:40:58'),
(5, 'notif test2', 'test2', 'general', 'bsit', 1, '2026-04-11 09:51:25'),
(6, 'notif testing 3', 'testing 3', 'general', 'all', 1, '2026-04-11 10:12:08'),
(7, 'wefwefw', 'wefvwefwe', 'general', 'all', 1, '2026-05-19 11:40:32'),
(8, 'wefwefwf', 'wqefwef', 'general', 'all', 1, '2026-05-19 11:46:42');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `record_id`, `student_id`, `category`, `message`, `submitted_at`) VALUES
(1, 10, 1, 'Software', 'feedback test', '2026-04-11 09:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `pc_status`
--

CREATE TABLE `pc_status` (
  `id` int(11) NOT NULL,
  `lab_name` varchar(50) NOT NULL,
  `pc_number` int(11) NOT NULL,
  `status` enum('available','unavailable','maintenance') DEFAULT 'available',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pc_status`
--

INSERT INTO `pc_status` (`id`, `lab_name`, `pc_number`, `status`, `updated_at`) VALUES
(1, '544', 5, 'maintenance', '2026-05-09 10:22:53'),
(11, '544', 10, 'unavailable', '2026-05-09 10:27:09'),
(31, '544', 27, '', '2026-05-09 08:44:24'),
(34, '544', 34, '', '2026-05-09 08:53:34'),
(36, '544', 40, '', '2026-05-09 08:56:39'),
(56, '544', 1, 'unavailable', '2026-05-09 10:22:51'),
(59, '544', 9, 'maintenance', '2026-05-09 10:22:59'),
(60, '544', 13, 'unavailable', '2026-05-09 10:23:02'),
(61, '542', 5, 'unavailable', '2026-05-09 10:23:06'),
(62, '526', 18, 'unavailable', '2026-05-09 10:23:09'),
(63, '526', 27, 'maintenance', '2026-05-09 10:23:11'),
(64, '544', 17, 'unavailable', '2026-05-09 10:27:00'),
(66, '544', 21, 'maintenance', '2026-05-09 10:27:06'),
(68, '542', 9, 'maintenance', '2026-05-09 10:27:13'),
(69, '542', 13, 'unavailable', '2026-05-09 10:27:14');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `student_pk_id` int(11) NOT NULL,
  `pc_number` int(11) NOT NULL,
  `lab_name` varchar(50) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_time` time NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected','active','completed') DEFAULT 'pending',
  `action` enum('pending','approved','rejected') DEFAULT 'pending',
  `actual_time_in` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `student_pk_id`, `pc_number`, `lab_name`, `schedule_date`, `schedule_time`, `purpose`, `language`, `status`, `action`, `actual_time_in`, `created_at`) VALUES
(1, 1, 21, '544', '2026-05-09', '15:23:00', 'Programming Task', 'PHP', 'rejected', 'rejected', NULL, '2026-05-09 07:22:17'),
(2, 1, 29, '544', '2026-05-09', '17:21:00', 'Research', 'Python', 'rejected', 'rejected', NULL, '2026-05-09 09:20:33'),
(3, 1, 29, '544', '2026-05-09', '17:23:00', 'Research', 'C#', 'rejected', 'rejected', NULL, '2026-05-09 09:22:12'),
(4, 1, 10, '544', '2026-05-09', '18:24:00', 'Research', 'C#', 'rejected', 'rejected', NULL, '2026-05-09 10:23:35'),
(5, 1, 14, '544', '2026-05-16', '19:10:00', 'Programming Task', 'Python', 'rejected', 'rejected', NULL, '2026-05-16 11:05:00'),
(6, 1, 18, '544', '2026-05-16', '19:32:00', 'Programming Task', 'Python', 'rejected', 'approved', NULL, '2026-05-16 11:05:58'),
(7, 1, 29, '544', '2026-05-18', '20:31:00', 'Research', 'Java', 'rejected', 'rejected', NULL, '2026-05-18 12:26:01'),
(8, 1, 34, '544', '2026-05-19', '19:47:00', 'Research', 'C++', 'active', 'approved', NULL, '2026-05-19 11:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_records`
--

CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL,
  `student_pk_id` int(11) DEFAULT NULL,
  `student_id_str` varchar(20) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `lab` varchar(50) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `task_status` varchar(50) DEFAULT 'Pending',
  `behavior_score` int(11) DEFAULT NULL,
  `points_earned_this_session` decimal(5,2) DEFAULT 0.00,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NULL DEFAULT NULL,
  `status` enum('Active','Completed') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_records`
--

INSERT INTO `sit_in_records` (`id`, `student_pk_id`, `student_id_str`, `fullname`, `lab`, `language`, `task_status`, `behavior_score`, `points_earned_this_session`, `login_time`, `logout_time`, `status`) VALUES
(1, 2, '1012', ' ', 'Lab 542', 'Java', 'Completed', 10, 8.02, '2026-03-21 09:32:02', '2026-03-21 09:34:10', 'Completed'),
(2, 2, '1012', ' ', 'Lab 524', 'PHP', 'Completed', 10, 8.00, '2026-03-21 09:34:51', '2026-03-21 09:34:54', 'Completed'),
(3, 2, '1012', ' ', 'Lab 542', 'Java', 'Completed', 10, 8.00, '2026-03-21 09:35:40', '2026-03-21 09:35:52', 'Completed'),
(4, 1, '1010', ' ', 'Lab 524', 'C++', 'Completed', 10, 8.00, '2026-03-21 09:36:14', '2026-03-21 09:36:18', 'Completed'),
(5, 2, '1012', ' ', 'Lab 542', 'Java', 'Completed', 10, 8.01, '2026-03-21 09:37:12', '2026-03-21 09:38:08', 'Completed'),
(6, 1, '1010', ' ', 'Lab 526', 'PHP', 'Completed', 10, 8.01, '2026-03-21 09:37:20', '2026-03-21 09:38:07', 'Completed'),
(7, 4, '1013', ' ', 'Lab 524', 'C#', 'Completed', 10, 8.01, '2026-03-21 09:37:36', '2026-03-21 09:38:06', 'Completed'),
(8, 1, '1010', ' ', 'Lab 544', 'PHP', 'Completed', 10, 8.01, '2026-03-21 10:47:20', '2026-03-21 10:47:58', 'Completed'),
(9, 6, '1015', ' ', 'Lab 526', 'PHP', 'Completed', 10, 8.01, '2026-03-21 11:04:16', '2026-03-21 11:04:54', 'Completed'),
(10, 1, '1010', ' ', 'Lab 526', 'PHP', 'Completed', 10, 10.00, '2026-04-11 08:51:00', '2026-05-02 08:34:43', 'Completed'),
(11, 1, '1010', 'jack black', 'Lab 544', 'PHP', 'Pending', NULL, 0.00, '2026-05-19 11:42:10', NULL, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `software_applications`
--

CREATE TABLE `software_applications` (
  `id` int(11) NOT NULL,
  `software_name` varchar(150) NOT NULL,
  `developer` varchar(100) DEFAULT 'Unknown',
  `version` varchar(50) NOT NULL,
  `category` varchar(100) DEFAULT 'General Development',
  `license_type` enum('Open Source','Proprietary Free','Proprietary Paid') DEFAULT 'Open Source',
  `target_lab` varchar(50) NOT NULL,
  `date_imported` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `software_applications`
--

INSERT INTO `software_applications` (`id`, `software_name`, `developer`, `version`, `category`, `license_type`, `target_lab`, `date_imported`) VALUES
(1, 'Visual Studio Code', 'Microsoft', '1.87.2', 'Development', 'Open Source', 'all', '2026-05-16 10:37:48'),
(2, 'Analytics Suite', 'Google', '4.5.0', 'Analytics', 'Proprietary Free', 'all', '2026-05-16 10:37:48'),
(3, 'Adobe Photoshop', 'Adobe', '25.5.0', 'Design', '', 'all', '2026-05-16 10:37:48'),
(4, 'Wireshark', 'The Wireshark Team', '4.2.3', 'Networking', '', 'all', '2026-05-16 10:37:48'),
(5, 'Node.js', 'OpenJS Foundation', '20.11.1', 'Runtime', '', 'all', '2026-05-16 10:37:48'),
(6, 'Visual Studio Code', 'Microsoft', '1.87.2', 'Development', 'Open Source', '526', '2026-05-16 10:55:11'),
(7, 'Analytics Suite', 'Google', '4.5.0', 'Analytics', 'Proprietary Free', '526', '2026-05-16 10:55:11'),
(8, 'Adobe Photoshop', 'Adobe', '25.5.0', 'Design', '', '526', '2026-05-16 10:55:11'),
(9, 'Wireshark', 'The Wireshark Team', '4.2.3', 'Networking', '', '526', '2026-05-16 10:55:11'),
(10, 'Node.js', 'OpenJS Foundation', '20.11.1', 'Runtime', '', '526', '2026-05-16 10:55:11'),
(11, 'Git', 'The Git Project', '2.44.0', 'Version Control', '', '526', '2026-05-16 10:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `home_address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `sit_ins` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accumulated_points` decimal(7,2) DEFAULT 0.00,
  `sessions_earned` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `firstname`, `middlename`, `lastname`, `course`, `year_level`, `email`, `home_address`, `password`, `sit_ins`, `created_at`, `accumulated_points`, `sessions_earned`) VALUES
(1, '1010', 'jack', 'Sigma', 'black', 'BSIT', '3', 'jackBlack@gmail.com', 'Sanciangko St., Cebu city, Philippines', '$2y$10$xhcdOzkMgphDkyjy69QsIeLev60m2n8no370JWJ.FlyLZUGKPonD2', 29, '2026-03-21 06:33:49', 34.02, 0),
(2, '1012', 'John', '', 'Doe', 'BSCS', '4', 'itsDoe@gmail.com', 'Brgy Tisa, Cebu city, Philippines', '$2y$10$.ud9qizle25X8tYAjtbIxuczTAZwiZWYoBC99MXBR3IH9OBOdtAS2', 30, '2026-03-21 07:51:04', 32.03, 0),
(4, '1013', 'xxx', 'yyy', 'zzz', 'BSPSYCHE', '1', 'sample@gmail.com', 'Skina Japan, Philippines', '$2y$10$lhAG5a9rR7EoBt7vqlapQOeqFThPm3wFd5TZrzi9YeG74IY3tBzG.', 30, '2026-03-21 09:36:53', 8.01, 0),
(5, '1014', 'clarisse', '', 'cruz', 'BSIT', '3', 'janeDoe@gmail.com', 'capitol hills , cebu city, philippines', '$2y$10$pYBUJnB.trsVFRQDdl.9/eXd5CA4lDtp7YivG3qyV/ryuGPyp0jeC', 30, '2026-03-21 10:43:53', 0.00, 0),
(6, '1015', 'patrick', '', 'star', 'BSCS', '4', 'patrickStar@gmail.com', 'bikini bottom st.', '$2y$10$fBOy0cYnWG0JckFlskSy1u69PJDD4m4XpqtL9DaI7KcruzOOSkWr6', 30, '2026-03-21 11:01:09', 8.01, 0);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `student_pk` int(11) NOT NULL,
  `content` text NOT NULL,
  `rating` tinyint(1) DEFAULT 5,
  `status` enum('unfeatured','featured') DEFAULT 'unfeatured',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `student_pk`, `content`, `rating`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 'this is a good system for sit in monitoring system', 5, 'featured', 1, '2026-05-16 08:45:27', '2026-05-18 12:46:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `record_id` (`record_id`);

--
-- Indexes for table `pc_status`
--
ALTER TABLE `pc_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_pc` (`lab_name`,`pc_number`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student_reservation` (`student_pk_id`);

--
-- Indexes for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_pk_id` (`student_pk_id`);

--
-- Indexes for table `software_applications`
--
ALTER TABLE `software_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_pk` (`student_pk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pc_status`
--
ALTER TABLE `pc_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `software_applications`
--
ALTER TABLE `software_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `sit_in_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_student_reservation` FOREIGN KEY (`student_pk_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD CONSTRAINT `sit_in_records_ibfk_1` FOREIGN KEY (`student_pk_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`student_pk`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
