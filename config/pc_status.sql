CREATE TABLE IF NOT EXISTS pc_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_name VARCHAR(50) NOT NULL,
    pc_number INT NOT NULL,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY lab_pc (lab_name, pc_number)
);