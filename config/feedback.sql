CREATE TABLE feedbacks (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    record_id INT(11) NOT NULL,        -- Links to 'id' in sit_in_records
    student_id INT(11) NOT NULL,      -- Links to 'id' in students
    category VARCHAR(50) NOT NULL,     -- e.g., 'Hardware', 'Software'
    message TEXT NOT NULL,             -- The actual feedback/report
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- This ensures if a sit-in record is deleted, the feedback is also removed
    FOREIGN KEY (record_id) REFERENCES sit_in_records(id) ON DELETE CASCADE
);