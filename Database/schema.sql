SET time_zone = "+01:00";

CREATE DATABASE IF NOT EXISTS quizzeo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quizzeo_db;

CREATE TABLE `users` (
	id INT PRIMARY KEY AUTO_INCREMENT,
    role ENUM('admin', 'school', 'company', 'user'),
    email VARCHAR(50) UNIQUE,
    password VARCHAR(200),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    is_active TINYINT(1) DEFAULT(1),
    created_at DATETIME,
    updated_at DATETIME NULL
);

CREATE TABLE `quizzes` (
	id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    title VARCHAR(50),
    description TEXT NULL,
    status ENUM('draft', 'launched', 'finished'),
    is_active TINYINT(1) DEFAULT(1),
    access_token VARCHAR(64) UNIQUE,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`)
);

CREATE TABLE `questions` (
	id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    intitule TEXT,
    type ENUM('qcm', 'texte_libre'),
    points INT NULL,
    ordre INT,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`)
);

CREATE TABLE `choices` (
	id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    libelle TEXT,
    is_correct TINYINT(1) DEFAULT(0),
    ordre INT,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`)
);

CREATE TABLE `quiz_attempts` (
	id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    user_id INT NOT NULL,
    started_at DATETIME,
    finished_at DATETIME NULL,
    score DECIMAL(5,2) NULL,
    is_completed TINYINT(1) DEFAULT(0),
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

CREATE TABLE `quiz_attempt_answers` (
	id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    choice_id INT NULL,
    answer_text TEXT NULL,
    is_correct TINYINT(1) NULL,
    FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts`(`id`),
	FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`),
    FOREIGN KEY (`choice_id`) REFERENCES `choices`(`id`)
);

SELECT * FROM users;
SELECT * FROM quizzes;
SELECT * FROM questions;
SELECT * FROM choices;
SELECT * FROM quiz_attempts;
SELECT * FROM quiz_attempt_answers;