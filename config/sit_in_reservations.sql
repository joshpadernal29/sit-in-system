DROP TABLE IF EXISTS reservations;

CREATE TABLE reservations (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_pk_id INT(11) NOT NULL,
    pc_number INT(11) NOT NULL,
    lab_name VARCHAR(50) NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time TIME NOT NULL,
    purpose VARCHAR(100) NOT NULL,
    -- 'language' added to match your sit_in_records requirements
    language VARCHAR(50) DEFAULT NULL,
    -- Expanded ENUM to handle the transition to an active session
    status ENUM('pending', 'approved', 'rejected', 'active', 'completed') DEFAULT 'pending',
    -- Tracks the actual start time when Admin confirms the sit-in
    actual_time_in TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    -- Constraint ensures valid students and cleans up data if a student is deleted
    CONSTRAINT fk_student_reservation 
        FOREIGN KEY (student_pk_id) 
        REFERENCES students(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE reservations 
ADD COLUMN action ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' 
AFTER status;