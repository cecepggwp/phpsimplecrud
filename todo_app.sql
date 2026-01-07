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
  `id_task` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deadline` date NOT NULL,
  `status` enum('Pending','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `task_type` enum('Personal','Dosen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Personal',
  `id_matkul` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_task`),
  KEY `fk_category` (`category_id`),
  KEY `fk_created_by` (`created_by`),
  KEY `fk_task_matkul` (`id_matkul`),
  CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `tb_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `tb_users` (`id_user`),
  CONSTRAINT `fk_task_matkul` FOREIGN KEY (`id_matkul`) REFERENCES `tb_matakuliah` (`id_mk`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tasks` */

insert  into `tasks`(`id_task`,`name`,`description`,`deadline`,`status`,`category_id`,`created_by`,`task_type`,`id_matkul`,`created_at`) values 
(2,'Tugas Dari Admin Ganteng Banget','Gilak Gw Ganteng Cui','2026-01-09','Pending',4,1,'Dosen',NULL,'2026-01-07 17:42:40'),
(3,'Database CRUD #1','CRUD Database','2026-01-13','Pending',1,2,'Dosen',7,'2026-01-07 17:52:09'),
(5,'Wedding Photo','Foto Orang Menikah','2026-01-09','Pending',1,13,'Dosen',4,'2026-01-07 20:03:02'),
(7,'Saya Keren Banget kan?','Saya keren banget','2026-01-11','Pending',4,1,'Dosen',NULL,'2026-01-07 20:33:16'),
(10,'Tugas Dari Admin Ganteng','ganteng banget','2026-01-09','Pending',5,1,'Dosen',NULL,'2026-01-07 21:04:28'),
(11,'Bikin Makalah','Bikin Makalah dan Laporan','2026-01-23','Pending',5,1,'Dosen',6,'2026-01-07 21:06:31');

/*Table structure for table `tb_categories` */

DROP TABLE IF EXISTS `tb_categories`;

CREATE TABLE `tb_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`id_dosen`),
  KEY `id_user` (`id_user`),
  KEY `id_jurusan` (`id_prodi`),
  CONSTRAINT `tb_dosen_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id_user`),
  CONSTRAINT `tb_dosen_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `tb_prodi` (`id_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_dosen` */

insert  into `tb_dosen`(`id_dosen`,`id_user`,`nip_dosen`,`nama_dosen`,`id_prodi`) values 
(1,2,'123456789098','Putu Haryanto, S.Pd., M.Ds.',2),
(2,5,'109876543212','Kristian Jaya, S.Kom., M.Kom.',1),
(3,9,'245920140007','I Kadek Wira, S.Ds., M.Ti.',NULL),
(4,12,'245920140064','Ronny Riti',1),
(5,6,'212345678909','Wanto Wirdawan',2),
(6,13,'5432109876','Alman Sudirman, S.Pd., M.Pd',1);

/*Table structure for table `tb_matakuliah` */

DROP TABLE IF EXISTS `tb_matakuliah`;

CREATE TABLE `tb_matakuliah` (
  `id_mk` int(11) NOT NULL AUTO_INCREMENT,
  `nm_mk` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mk`),
  KEY `id_jurusan` (`id_prodi`),
  CONSTRAINT `tb_matakuliah_ibfk_1` FOREIGN KEY (`id_prodi`) REFERENCES `tb_prodi` (`id_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_matakuliah` */

insert  into `tb_matakuliah`(`id_mk`,`nm_mk`,`id_prodi`) values 
(1,'Animasi 3D',1),
(2,'Tipografi Dasar',1),
(3,'Ilustrasi Digital',1),
(4,'Fotografi',1),
(5,'Pemrograman Web Lanjut',2),
(6,'Algoritma dan Struktur Data',2),
(7,'Basis Data',2),
(8,'Jaringan Komputer',2),
(10,'Praktikum Jaringan Komputer',2),
(12,'Bisnis Media Sosial',6),
(13,'Bahasa Indonesia',1),
(14,'Fotografi Desain',1),
(15,'Komputer Grafis Bitmap',1),
(16,'Komputer Grafis Vektor',1),
(17,'Komputer Grafis Tata Letak',1),
(18,'Grafis 3D',1),
(19,'Animasi Dasar',1),
(20,'Produksi Film',1),
(21,'Animasi Grafiis',1),
(22,'Metode Reproduksi Grafika',1),
(23,'Desain Web',1),
(24,'Desain Interaktif',1),
(25,'Desain Kemasan',1),
(26,'Eksperimental Visual',1),
(27,'Fotografi Dasar',1),
(28,'Sejarah Desain modern',1),
(29,'Sejarah Kebudayaan Bali',1),
(30,'Karakter Bentuk',1),
(31,'Ilustraasi Grafis',1),
(32,'Ilustrasi Digital',1),
(33,'Ornamen Ilustrasi Tradisi',1);

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
(1,3,'245920140001','Dian Wijaya',1),
(2,4,'245920140002','Stephanie Xian',1),
(4,7,'245920140005','Naufal Abidian',2),
(5,8,'245920140006','Ronny Riti',1),
(6,10,'245920140008','Hermanto Subianto',2),
(7,11,'245920140034','Dewa Arwinata',1);

/*Table structure for table `tb_prodi` */

DROP TABLE IF EXISTS `tb_prodi`;

CREATE TABLE `tb_prodi` (
  `id_prodi` int(11) NOT NULL AUTO_INCREMENT,
  `nm_prodi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_prodi` */

insert  into `tb_prodi`(`id_prodi`,`nm_prodi`) values 
(1,'Desain Komunikasi Visual'),
(2,'Sistem dan Teknologi Informasi'),
(3,'Desain Interior'),
(5,'Desain Mode'),
(6,'Bisnis Digital'),
(7,'Arsitektur'),
(8,'Manajemen Bisnis');

/*Table structure for table `tb_tugas_mhs` */

DROP TABLE IF EXISTS `tb_tugas_mhs`;

CREATE TABLE `tb_tugas_mhs` (
  `id_tugas_mhs` int(11) NOT NULL AUTO_INCREMENT,
  `id_task` int(11) NOT NULL,
  `id_mhs` int(11) NOT NULL,
  `status` enum('Pending','Completed') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `nilai` int(3) DEFAULT '0',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `finish_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tugas_mhs`),
  KEY `fk_tm_task` (`id_task`),
  KEY `fk_tm_mhs` (`id_mhs`),
  CONSTRAINT `fk_tm_mhs` FOREIGN KEY (`id_mhs`) REFERENCES `tb_mhs` (`id_mhs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tm_task` FOREIGN KEY (`id_task`) REFERENCES `tasks` (`id_task`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_tugas_mhs` */

insert  into `tb_tugas_mhs`(`id_tugas_mhs`,`id_task`,`id_mhs`,`status`,`nilai`,`catatan`,`finish_date`) values 
(1,2,1,'Completed',0,NULL,'2026-01-07 12:33:24'),
(2,2,2,'Completed',0,NULL,'2026-01-07 12:33:24'),
(3,2,5,'Completed',0,NULL,'2026-01-07 12:33:24'),
(4,2,7,'Completed',0,NULL,'2026-01-07 12:33:24'),
(5,3,4,'Completed',0,NULL,'2026-01-07 12:38:50'),
(6,3,6,'Pending',0,NULL,NULL),
(7,5,1,'Pending',0,NULL,NULL),
(8,5,2,'Pending',0,NULL,NULL),
(9,5,5,'Pending',0,NULL,NULL),
(10,5,7,'Pending',0,NULL,NULL),
(11,7,4,'Pending',0,NULL,NULL),
(12,7,6,'Completed',0,NULL,'2026-01-07 12:47:19'),
(15,11,4,'Pending',0,NULL,NULL),
(16,11,6,'Pending',0,NULL,NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_users` */

insert  into `tb_users`(`id_user`,`username`,`nama_lengkap`,`password`,`email`,`role`,`nmr_induk`) values 
(1,'admin',NULL,'12345','admin1@gmail.com','1','111111111111'),
(2,'user1','Putu Haryanto, S.Pd., M.Ds.','54321','user1@gmail.com','2','123456789098'),
(3,'user2','Dian Wijaya','qwerty1','user2@gmail.com','3','245920140001'),
(4,'xianyuie','Stephanie Xian','12345','xianyuie@gmail.com','3','245920140002'),
(5,'akukeren','Kristian Jaya, S.Kom., M.Kom.','qwerty2','keren@gmail.com','2','109876543212'),
(6,'wowbanget','Wanto Wirdawan','yuiop','wow@gmail.com','2','212345678909'),
(7,'apadah','Naufal Abidian','zxcvbnm','apadah@gmail.com','3','245920140005'),
(8,'lembut','Ronny Riti','098765','lembut@gmail.com','3','245920140006'),
(9,'akuya','I Kadek Wira, S.Ds., M.Ti.','akuya','akuya@gmail.com','2','245920140007'),
(10,'kuyakuya','Hermanto Subianto','kuyakuya','kuya@gmail.com','3','245920140008'),
(11,'arwidewa','Dewa Arwinata','dewaarwi','dewa@gmail.com','3','245920140034'),
(12,'ronilaura','Ronny Riti','1234567890','ronny@gmail.com','2','245920140064'),
(13,'alman123','Alman Sudirman, S.Pd., M.Pd','123','alman123@gmail.com','2','5432109876');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
