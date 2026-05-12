CREATE TABLE testimonials (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_pk INT(11) NOT NULL, -- Foreign key linking to your students/users table
    content TEXT NOT NULL,
    rating TINYINT(1) DEFAULT 5, -- Allows for a 1-5 star system
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_featured TINYINT(1) DEFAULT 0, -- Set to 1 to "pin" it to the top
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraint to ensure student_id is valid
    FOREIGN KEY (student_pk) REFERENCES students(id) ON DELETE CASCADE
);