CREATE TABLE `software_applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `software_name` VARCHAR(150) NOT NULL,
    `developer` VARCHAR(100) DEFAULT 'Unknown',
    `version` VARCHAR(50) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'General Development',
    `license_type` ENUM('Open Source', 'Proprietary Free', 'Proprietary Paid') DEFAULT 'Open Source',
    `target_lab` VARCHAR(50) NOT NULL, -- e.g., '544', '542', '526', or 'all'
    `date_imported` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;