USE ssb_internal;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tasks (title, status) VALUES
('Verify Apache & MySQL ports', 'completed'),
('Configure rsync deployment pipeline', 'pending'),
('Test mysqldump backup & restore', 'pending');
