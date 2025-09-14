-- Create database if not exists
CREATE DATABASE IF NOT EXISTS resume_builder;
USE resume_builder;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Personal Information table
CREATE TABLE IF NOT EXISTS personal_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Education table
CREATE TABLE IF NOT EXISTS education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    degree VARCHAR(100) NOT NULL,
    university VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    grad_year VARCHAR(4),
    FOREIGN KEY (resume_id) REFERENCES personal_info(id) ON DELETE CASCADE
);

-- Experience table
CREATE TABLE IF NOT EXISTS experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    company VARCHAR(100) NOT NULL,
    duration VARCHAR(50),
    description TEXT,
    FOREIGN KEY (resume_id) REFERENCES personal_info(id) ON DELETE CASCADE
);

-- Skills table
CREATE TABLE IF NOT EXISTS skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    skill_name VARCHAR(50) NOT NULL,
    FOREIGN KEY (resume_id) REFERENCES personal_info(id) ON DELETE CASCADE
);

-- Languages table
CREATE TABLE IF NOT EXISTS languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    language VARCHAR(50) NOT NULL,
    proficiency VARCHAR(20),
    FOREIGN KEY (resume_id) REFERENCES personal_info(id) ON DELETE CASCADE
);

-- Activities table
CREATE TABLE IF NOT EXISTS activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    activity TEXT NOT NULL,
    FOREIGN KEY (resume_id) REFERENCES personal_info(id) ON DELETE CASCADE
);

-- Summary table
CREATE TABLE IF NOT EXISTS summary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    content TEXT,
    FOREIGN KEY (resume_id) REFERENCES personal_info(id) ON DELETE CASCADE
); 