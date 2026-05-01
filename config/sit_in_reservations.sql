CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_pk_id INT,
    lab_name VARCHAR(50),
    pc_number INT,
    schedule_date DATE,
    schedule_time TIME,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_pk_id) REFERENCES students(id)
);