CREATE DATABASE IF NOT EXISTS fitness_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitness_db;

   USE fitness_db;
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       email VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       name VARCHAR(100) NOT NULL,
       age INT NOT NULL,
       gender ENUM('male', 'female', 'other') NOT NULL,
       weight DECIMAL(5,2) NOT NULL,
       height DECIMAL(5,2) NULL,
       fitnessgoal ENUM('weight-loss', 'muscle-gain', 'endurance', 'strength', 'general') NOT NULL,
       dietarypreference ENUM('none', 'vegetarian', 'vegan', 'keto', 'paleo') DEFAULT 'none',
       activitylevel ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
       targetweight DECIMAL(5,2) NULL,
       createdat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updatedat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    experience INT DEFAULT 0,
    bio TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    goal ENUM('weight-loss','muscle-gain','endurance','strength','general') NOT NULL,
    dietary_preference ENUM('none','vegetarian','vegan','keto','paleo') DEFAULT 'none',
    activity_level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    plan_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE community (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
