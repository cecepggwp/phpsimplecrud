-- =============================================
-- To-Do App Database Schema
-- Database: todo_app
-- =============================================

-- Create database
CREATE DATABASE IF NOT EXISTS `todo_app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `todo_app`;

-- =============================================
-- Table: users
-- =============================================
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample users
-- Password for all users: admin123 (hashed using password_hash)
INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'john', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'jane', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =============================================
-- Table: categories
-- =============================================
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample categories
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Work'),
(2, 'Personal'),
(3, 'Shopping'),
(4, 'Health'),
(5, 'Education'),
(6, 'Finance');

-- =============================================
-- Table: tasks
-- =============================================
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `deadline` date NOT NULL,
  `status` enum('Pending','Completed') NOT NULL DEFAULT 'Pending',
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`),
  KEY `fk_user` (`user_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample tasks for admin user (user_id = 1)
INSERT INTO `tasks` (`name`, `description`, `deadline`, `status`, `category_id`, `user_id`) VALUES
('Complete Project Report', 'Finalize and submit the Q4 project report to management', '2024-12-15', 'Pending', 1, 1),
('Buy Groceries', 'Buy milk, eggs, bread, and vegetables from the supermarket', '2024-11-05', 'Pending', 3, 1),
('Gym Workout', 'Complete 1 hour cardio and strength training session', '2024-11-03', 'Completed', 4, 1),
('Learn PHP OOP', 'Complete the PHP Object-Oriented Programming tutorial', '2024-11-10', 'Pending', 5, 1),
('Pay Credit Card Bill', 'Pay the November credit card bill before due date', '2024-11-08', 'Pending', 6, 1),
('Team Meeting', 'Attend weekly team standup meeting at 10 AM', '2024-11-04', 'Completed', 1, 1),
('Doctor Appointment', 'Annual health checkup at City Hospital', '2024-11-12', 'Pending', 4, 1),
('Birthday Party Planning', 'Plan surprise birthday party for mom', '2024-11-20', 'Pending', 2, 1);

-- Insert sample tasks for john user (user_id = 2)
INSERT INTO `tasks` (`name`, `description`, `deadline`, `status`, `category_id`, `user_id`) VALUES
('Code Review', 'Review pull requests from team members', '2024-11-05', 'Pending', 1, 2),
('Read Book', 'Finish reading "Clean Code" by Robert Martin', '2024-11-15', 'Pending', 5, 2),
('Morning Run', 'Complete 5km morning run', '2024-11-04', 'Completed', 4, 2);

-- =============================================
-- End of SQL file
-- =============================================

-- Notes:
-- 1. Default password for all users: admin123
-- 2. To create a new password hash, use PHP:
--    echo password_hash('your_password', PASSWORD_DEFAULT);
-- 3. Foreign keys ensure data integrity:
--    - Deleting a category sets task category_id to NULL
--    - Deleting a user deletes all their tasks
