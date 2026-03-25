CREATE TABLE announcements (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
    is_active TINYINT(1) DEFAULT 1,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);