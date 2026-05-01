CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_pk_id INT NOT NULL,
    pc_number INT NOT NULL,
    lab_name VARCHAR(50) NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time TIME NOT NULL,
    purpose VARCHAR(100) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);