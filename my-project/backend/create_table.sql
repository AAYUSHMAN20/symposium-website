-- Database and Table Creation Script for Symposium Contact Management
-- Run this script in your MySQL database to create the necessary table

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS symposium_db;
USE symposium_db;

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create index for better performance
CREATE INDEX idx_email ON contact_messages(email);
CREATE INDEX idx_submitted_at ON contact_messages(submitted_at);
CREATE INDEX idx_is_read ON contact_messages(is_read);

-- Sample data (optional)
-- INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, ip_address) 
-- VALUES ('John', 'Doe', 'john.doe@example.com', '+91 98765 43210', 'Test Subject', 'This is a test message.', '127.0.0.1');

-- Display table structure
DESCRIBE contact_messages;
