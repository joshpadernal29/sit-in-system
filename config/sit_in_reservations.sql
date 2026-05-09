DROP TABLE IF EXISTS reservations;

CREATE TABLE reservations (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_pk_id INT(11) NOT NULL,
    pc_number INT(11) NOT NULL,
    lab_name VARCHAR(50) NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time TIME NOT NULL,
    purpose VARCHAR(100) NOT NULL,
    language VARCHAR(50) DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected', 'active', 'completed') DEFAULT 'pending',
    action ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    actual_time_in TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    CONSTRAINT fk_student_reservation 
        FOREIGN KEY (student_pk_id) 
        REFERENCES students(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE reservations 
ADD COLUMN action ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' 
AFTER status;