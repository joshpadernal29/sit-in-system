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

-- Step 1: Update the sit_in_records table to hold per-session scores
ALTER TABLE sit_in_records 
ADD COLUMN task_status VARCHAR(20) DEFAULT 'Pending',
ADD COLUMN behavior_score INT DEFAULT 10,
ADD COLUMN points_earned_this_session FLOAT DEFAULT 0.0;

-- Step 2: Update the students profile table to track running point totals and earned bonus pools
ALTER TABLE students 
ADD COLUMN accumulated_points FLOAT DEFAULT 0.0,
ADD COLUMN sessions_earned INT DEFAULT 0;