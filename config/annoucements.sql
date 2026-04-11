CREATE TABLE announcements (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    priority ENUM('general', 'urgent', 'academic') DEFAULT 'general',
    target_audience ENUM('all', 'bsit', 'bscs') DEFAULT 'all',
    is_active TINYINT(1) DEFAULT 1,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ALTER TABLE announcements 
-- MODIFY COLUMN priority ENUM('general', 'urgent', 'academic') DEFAULT 'general';

-- ALTER TABLE announcements 
-- ADD COLUMN target_audience ENUM('all', 'bsit', 'bscs') DEFAULT 'all' AFTER priority;