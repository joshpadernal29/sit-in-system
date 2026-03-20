CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(20) NOT NULL UNIQUE,
    `firstname` VARCHAR(50) NOT NULL,
    `middlename` VARCHAR(50),
    `lastname` VARCHAR(50) NOT NULL,
    `course` VARCHAR(100) NOT NULL,
    `year_level` VARCHAR(100) NOT NULL, 
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `home_address` TEXT,
    `password` VARCHAR(255) NOT NULL,
    `sit_ins` INT DEFAULT 30, -- New column with default 30
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;