/*
SQLyog Ultimate v12.4.3 (64 bit)
MySQL - 5.7.33 : Database - todo_app
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`todo_app` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `todo_app`;

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`name`) values 
(1,'Work'),
(2,'Personal'),
(3,'Shopping'),
(4,'Health'),
(5,'Education'),
(6,'Finance'),
(10,'Important');

/*Table structure for table `tasks` */

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deadline` date NOT NULL,
  `status` enum('Pending','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tasks` */

insert  into `tasks`(`id`,`name`,`description`,`deadline`,`status`,`category_id`,`created_at`) values 
(1,'Complete Project Report','Finalize and submit the Q4 project report to management','2024-12-15','Pending',1,'2025-10-30 21:23:13'),
(2,'Buy Groceries','Buy milk, eggs, bread, and vegetables from the supermarket','2024-11-05','Completed',3,'2025-10-30 21:23:13'),
(3,'Gym Workout','Complete 1 hour cardio and strength training session','2024-11-03','Completed',1,'2025-10-30 21:23:13'),
(4,'Learn PHP OOP','Complete the PHP Object-Oriented Programming tutorial','2024-11-10','Pending',5,'2025-10-30 21:23:13'),
(5,'Pay Credit Card Bill','Pay the November credit card bill before due date','2024-11-08','Pending',6,'2025-10-30 21:23:13'),
(6,'Team Meeting','Attend weekly team standup meeting at 10 AM','2024-11-04','Completed',1,'2025-10-30 21:23:13'),
(7,'Doctor Appointment','Annual health checkup at City Hospital','2024-11-12','Pending',4,'2025-10-30 21:23:13'),
(8,'Birthday Party Planning','Plan surprise birthday party for mom','2024-11-20','Pending',2,'2025-10-30 21:23:13'),
(9,'Code Review','Review pull requests from team members','2024-11-05','Completed',1,'2025-10-30 21:23:13'),
(10,'Read Book','Finish reading \"Clean Code\" by Robert Martin','2024-11-15','Pending',5,'2025-10-30 21:23:13'),
(11,'Morning Run','Complete 5km morning run','2024-11-04','Completed',4,'2025-10-30 21:23:13'),
(13,'Fix your Laptop','Reset the windows OS or Reinstall it.','2025-11-05','Completed',1,'2025-11-03 15:23:34');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id_user`,`username`,`password`,`email`) values 
(1,'admin','12345','admin1@gmail.com'),
(2,'user1','54321','user1@gmail.com');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
