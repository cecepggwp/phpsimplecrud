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
  `id_dosen` int(11) DEFAULT NULL,
  `id_matkul` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`),
  KEY `id_dosen` (`id_dosen`),
  KEY `id_matkul` (`id_matkul`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `tb_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`id_dosen`) REFERENCES `tb_dosen` (`id_dosen`),
  CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`id_matkul`) REFERENCES `tb_matakuliah` (`id_mk`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tasks` */

insert  into `tasks`(`id`,`name`,`description`,`deadline`,`status`,`category_id`,`created_at`,`id_dosen`,`id_matkul`) values 
(1,'Complete Project Report','Finalize and submit the Q4 project report to management','2024-12-15','Pending',1,'2025-10-30 21:23:13',NULL,NULL),
(2,'Buy Groceries','Buy milk, eggs, bread, and vegetables from the supermarket','2024-11-05','Completed',3,'2025-10-30 21:23:13',NULL,NULL),
(3,'Gym Workout','Complete 1 hour cardio and strength training session','2024-11-03','Completed',1,'2025-10-30 21:23:13',NULL,NULL),
(4,'Learn PHP OOP','Complete the PHP Object-Oriented Programming tutorial','2024-11-10','Completed',5,'2025-10-30 21:23:13',NULL,NULL),
(5,'Pay Credit Card Bill','Pay the November credit card bill before due date','2024-11-08','Completed',6,'2025-10-30 21:23:13',NULL,NULL),
(6,'Team Meeting','Attend weekly team standup meeting at 10 AM','2024-11-04','Completed',1,'2025-10-30 21:23:13',NULL,NULL),
(7,'Doctor Appointment','Annual health checkup at City Hospital','2024-11-12','Pending',4,'2025-10-30 21:23:13',NULL,NULL),
(8,'Birthday Party Planning','Plan surprise birthday party for mom','2024-11-20','Pending',2,'2025-10-30 21:23:13',NULL,NULL),
(9,'Code Review','Review pull requests from team members','2024-11-05','Completed',1,'2025-10-30 21:23:13',NULL,NULL),
(10,'Read Book','Finish reading \"Clean Code\" by Robert Martin','2024-11-15','Pending',5,'2025-10-30 21:23:13',NULL,NULL),
(11,'Morning Run','Complete 5km morning run','2024-11-04','Completed',4,'2025-10-30 21:23:13',NULL,NULL),
(13,'Fix your Laptop','Reset the windows OS or Reinstall it.','2025-11-05','Completed',1,'2025-11-03 15:23:34',NULL,NULL);

/*Table structure for table `tb_categories` */

DROP TABLE IF EXISTS `tb_categories`;

CREATE TABLE `tb_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_categories` */

insert  into `tb_categories`(`id`,`name`) values 
(1,'Work'),
(2,'Personal'),
(3,'Shopping'),
(4,'Health'),
(5,'Education'),
(6,'Finance');

/*Table structure for table `tb_dosen` */

DROP TABLE IF EXISTS `tb_dosen`;

CREATE TABLE `tb_dosen` (
  `id_dosen` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `nip_dosen` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_dosen` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  `id_mk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_dosen`),
  KEY `id_user` (`id_user`),
  KEY `id_jurusan` (`id_prodi`),
  KEY `id_matkul` (`id_mk`),
  CONSTRAINT `tb_dosen_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id_user`),
  CONSTRAINT `tb_dosen_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `tb_prodi` (`id_prodi`),
  CONSTRAINT `tb_dosen_ibfk_3` FOREIGN KEY (`id_mk`) REFERENCES `tb_matakuliah` (`id_mk`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_dosen` */

insert  into `tb_dosen`(`id_dosen`,`id_user`,`nip_dosen`,`nama_dosen`,`id_prodi`,`id_mk`) values 
(1,2,'123456789098','Putu Haryanto, S.Pd., M.Ds.',NULL,NULL),
(2,5,'109876543212','Kristian Jaya, S.Kom., M.Kom.',NULL,NULL),
(3,9,'245920140007','I Kadek Wira, S.Ds., M.Ti.',NULL,NULL);

/*Table structure for table `tb_matakuliah` */

DROP TABLE IF EXISTS `tb_matakuliah`;

CREATE TABLE `tb_matakuliah` (
  `id_mk` int(11) NOT NULL AUTO_INCREMENT,
  `nm_mk` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mk`),
  KEY `id_jurusan` (`id_prodi`),
  CONSTRAINT `tb_matakuliah_ibfk_1` FOREIGN KEY (`id_prodi`) REFERENCES `tb_prodi` (`id_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_matakuliah` */

insert  into `tb_matakuliah`(`id_mk`,`nm_mk`,`id_prodi`) values 
(1,'Animasi 3D',1);

/*Table structure for table `tb_mhs` */

DROP TABLE IF EXISTS `tb_mhs`;

CREATE TABLE `tb_mhs` (
  `id_mhs` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `nim_mhs` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nm_mhs` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mhs`),
  KEY `id_user` (`id_user`),
  KEY `id_jurusan` (`id_prodi`),
  CONSTRAINT `tb_mhs_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id_user`),
  CONSTRAINT `tb_mhs_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `tb_prodi` (`id_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_mhs` */

insert  into `tb_mhs`(`id_mhs`,`id_user`,`nim_mhs`,`nm_mhs`,`id_prodi`) values 
(1,3,'245920140001','Dian Wijaya',NULL),
(2,4,'245920140002','Stephanie Xian',NULL),
(3,6,'212345678909','Wanto Wirdawan',NULL),
(4,7,'245920140005','Naufal Abidian',NULL),
(5,8,'245920140006','Ronny Riti',NULL),
(6,10,'245920140008','Hermanto Subianto',NULL),
(7,11,'245920140034','Dewa Arwinata',NULL);

/*Table structure for table `tb_prodi` */

DROP TABLE IF EXISTS `tb_prodi`;

CREATE TABLE `tb_prodi` (
  `id_prodi` int(11) NOT NULL AUTO_INCREMENT,
  `nm_prodi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_prodi` */

insert  into `tb_prodi`(`id_prodi`,`nm_prodi`) values 
(1,'Desain Komunikasi Visual');

/*Table structure for table `tb_tugas_mhs` */

DROP TABLE IF EXISTS `tb_tugas_mhs`;

CREATE TABLE `tb_tugas_mhs` (
  `id_tugas_mhs` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) DEFAULT NULL,
  `id_mhs` int(11) DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `kriteria` enum('Dosen','Mahasiswa') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_tugas_mhs`),
  KEY `id` (`id`),
  KEY `id_mhs` (`id_mhs`),
  CONSTRAINT `tb_tugas_mhs_ibfk_1` FOREIGN KEY (`id`) REFERENCES `tasks` (`id`),
  CONSTRAINT `tb_tugas_mhs_ibfk_2` FOREIGN KEY (`id_mhs`) REFERENCES `tb_mhs` (`id_mhs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_tugas_mhs` */

/*Table structure for table `tb_users` */

DROP TABLE IF EXISTS `tb_users`;

CREATE TABLE `tb_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '3',
  `nmr_induk` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_users` */

insert  into `tb_users`(`id_user`,`username`,`nama_lengkap`,`password`,`email`,`role`,`nmr_induk`) values 
(1,'admin',NULL,'12345','admin1@gmail.com','1','111111111111'),
(2,'user1','Putu Haryanto, S.Pd., M.Ds.','54321','user1@gmail.com','2','123456789098'),
(3,'user2','Dian Wijaya','qwerty1','user2@gmail.com','3','245920140001'),
(4,'xianyuie','Stephanie Xian','12345','xianyuie@gmail.com','3','245920140002'),
(5,'akukeren','Kristian Jaya, S.Kom., M.Kom.','qwerty2','keren@gmail.com','2','109876543212'),
(6,'wowbanget','Wanto Wirdawan','yuiop','wow@gmail.com','3','212345678909'),
(7,'apadah','Naufal Abidian','zxcvbnm','apadah@gmail.com','3','245920140005'),
(8,'lembut','Ronny Riti','098765','lembut@gmail.com','3','245920140006'),
(9,'akuya','I Kadek Wira, S.Ds., M.Ti.','akuya','akuya@gmail.com','2','245920140007'),
(10,'kuyakuya','Hermanto Subianto','kuyakuya','kuya@gmail.com','3','245920140008'),
(11,'arwidewa','Dewa Arwinata','dewaarwi','dewa@gmail.com','3','245920140034');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
