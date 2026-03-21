CREATE TABLE sit_in_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_pk_id INT, -- Links to students.id
    student_id_str VARCHAR(20),
    fullname VARCHAR(100),
    lab VARCHAR(50),
    language VARCHAR(50),
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    status ENUM('Active', 'Completed') DEFAULT 'Active',
    FOREIGN KEY (student_pk_id) REFERENCES students(id)
);