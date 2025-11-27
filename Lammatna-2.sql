-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 27, 2025 at 10:01 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Lammatna`
--

-- --------------------------------------------------------

--
-- Table structure for table `gathering`
--

CREATE TABLE `gathering` (
  `GatheringID` int NOT NULL,
  `date` date NOT NULL,
  `category` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `time` time NOT NULL,
  `joinCode` varchar(20) NOT NULL,
  `adminID` int NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gathering`
--

INSERT INTO `gathering` (`GatheringID`, `date`, `category`, `name`, `location`, `time`, `joinCode`, `adminID`, `latitude`, `longitude`) VALUES
(1, '2025-12-15', 'اجتماع عائلي', 'غداء العائلة الشهري', 'منزل الجدة - حي النخيل', '14:00:00', 'ABCD1234', 1, NULL, NULL),
(2, '2025-11-20', 'حفلة تخرج', 'تخرج أحمد من الجامعة', 'قاعة الأفراح - وسط المدينة', '19:30:00', 'EFGH5678', 2, NULL, NULL),
(3, '2025-12-05', 'كشتة', 'نهاية أسبوع في البر', 'منتزه السلام - خارج المدينة', '08:00:00', 'IJKL9012', 3, NULL, NULL),
(4, '2025-10-29', 'اجتماع عائلي', 'دورية السبت', 'البيت', '17:35:00', '149762A3', 3, NULL, NULL),
(8, '2025-11-27', 'اجتماع عائلي', 'تجربة١', 'بيتنا', '16:37:00', 'A356DCD4', 7, NULL, NULL),
(9, '2025-11-20', 'حفلة تخرج', 'تجربة٢', 'بيتنا', '03:44:00', 'E4B733E8', 7, NULL, NULL),
(10, '2025-11-25', 'حفلة تخرج', 'تجربة٣', 'بيتنا', '23:40:00', '00639A84', 7, NULL, NULL),
(11, '2025-11-26', 'كشتة', 'تجربة4', 'بيتنا', '02:00:00', '5E16C580', 7, 24.75478050, 46.62860811),
(12, '2025-11-26', 'حفلة تخرج', 'تجربة٥', 'النخيل', '02:11:00', 'B1838F3D', 7, 24.69677697, 46.68216646),
(13, '2025-11-27', 'اجتماع عائلي', 'تجربة٦', 'بيتنا', '16:12:00', '7DD8370D', 7, 24.71860943, 46.53659761),
(14, '2025-11-27', 'اجتماع عائلي', 'تجربة٧', 'بيتنا', '15:12:00', 'ED5521E5', 7, 24.69241002, 46.68079316),
(15, '2025-11-27', 'حفلة تخرج', 'تجربة٨', 'بيتنا', '02:23:00', '24EC440E', 7, 24.82397509, 46.71100557),
(16, '2025-11-27', 'اجتماع عائلي', 'تجربة٩', 'بيتنا', '02:34:00', 'CC1A4710', 7, 24.73295439, 46.67530000),
(17, '2025-11-26', 'حفلة تخرج', 'تجربة١٠', 'بيتنا', '02:41:00', 'C598BFA0', 7, 24.72110433, 46.65607393),
(18, '2025-11-26', 'حفلة تخرج', 'تجربة١٠', 'بيتنا', '02:41:00', '4EAB9862', 7, 24.72110433, 46.65607393),
(19, '2025-11-27', 'اجتماع عائلي', 'تجربة١١', 'بيتنا', '02:45:00', '8A526D51', 7, 24.69740081, 46.70139253),
(20, '2025-11-28', 'كشتة', 'تجربة١٢', 'بيتنا', '02:50:00', '6365D57A', 7, 24.66682909, 46.81400239),
(21, '2025-11-27', 'حفلة تخرج', 'تجربة١٣', 'ب', '02:49:00', 'BC4415FE', 7, 24.68429956, 46.58603608),
(22, '2025-11-27', 'اجتماع اصدقاء', '١٤', 'ن', '02:53:00', 'CF4D6E72', 7, 24.72298521, 46.64536118),
(23, '2025-11-27', 'اجتماع اصدقاء', 'تجربة١٥', 'ت', '02:53:00', '8A30954A', 7, 24.73079593, 46.65289819),
(24, '2025-11-27', 'اجتماع اصدقاء', 'تجربة14', 'f', '15:07:00', 'EDAF2157', 7, 24.75796153, 46.64677202),
(25, '2025-11-27', 'اجتماع اصدقاء', 'تجربة١٥', 'بيتنا', '15:12:00', '86B1511F', 7, 24.71092952, 46.67979002),
(26, '2025-11-27', 'حفلة تخرج', 'تجربة مدري كم', 'بيتنا', '18:59:00', '1F63DF4C', 7, 24.72354070, 46.66269362),
(27, '2025-11-27', 'حفلة تخرج', 'تجربة مدري كم ٢', 'البيت', '18:01:00', 'ED3EB457', 7, 24.70648997, 46.67369604),
(28, '2025-11-20', 'اجتماع عائلي', 'تجربة١٦', 'بيتنا', '17:28:00', 'CD659720', 7, 24.70746951, 46.70170903),
(29, '2025-11-28', 'حفلة تخرج', 'ت', 'بيت', '17:42:00', '26BFAB24', 7, 24.73655012, 46.67336345),
(30, '2025-11-28', 'حفلة تخرج', 'تخرج', 'بيتنا', '18:41:00', '1AE6F98B', 7, 24.71779566, 46.68768644),
(31, '2025-11-28', 'حفلة تخرج', 'ن', 'بيا', '18:48:00', '6AED689A', 7, 24.70881454, 46.66258096),
(32, '2025-11-28', 'حفلة تخرج', 'تخرج', 'بيتنا', '18:49:00', 'F51E7A22', 7, 24.71512527, 46.65119767),
(33, '2025-11-28', 'اخرى', 'تجربتي', 'بيتنا', '19:00:00', 'DF32EEB3', 9, 24.71317604, 46.68445706),
(34, '2025-11-27', 'اخرى', 'فعالية التخزين', 'بيتنا', '07:15:00', '35AE4D76', 7, 24.73420657, 46.65982365),
(35, '2025-11-20', 'اخرى', 'فعالية التخزين٢', 'بيتنا', '07:19:00', '3A06EB04', 7, 24.71943782, 46.66077852),
(36, '2025-12-04', 'اخرى', 'فعالية التخزين٣', 'البيت', '07:21:00', '11D511CC', 7, 24.74922690, 46.65639579),
(37, '2025-11-26', 'اخرى', 'تخزن', 'بيتنا', '07:25:00', 'CA8002E1', 7, 24.71363898, 46.68601811),
(38, '2025-11-27', 'اخرى', 'تجربة داتابيس', 'بيتنا', '16:18:00', 'D4E7AFD1', 7, 24.67182091, 46.62723481),
(39, '2025-12-04', 'اخرى', 'تجربة داتابيس٢٢', 'بيتنا', '16:21:00', '1DBBD206', 7, 24.71176283, 46.66571378),
(40, '2025-11-27', 'اخرى', 'داتابيس٣', 'بيتنا', '16:24:00', '7578E624', 10, 24.72769217, 46.67832017),
(41, '2025-11-26', 'اخرى', 'داتابيس ١١١١', 'بيتنا', '16:29:00', '03F0CBF2', 10, 24.70723071, 46.67910874),
(42, '2025-12-04', 'اخرى', 'database2222', 'home', '16:31:00', '78E15DF8', 10, 24.71194801, 46.66260242),
(43, '2025-12-04', 'اخرى', 'databse33', 'fdk', '16:37:00', 'F2D1875D', 10, 24.71205522, 46.67707562),
(44, '2025-11-27', 'اخرى', 'داتبيسسس', 'بيتنا', '16:46:00', '48DD1278', 10, 24.70925313, 46.68010652),
(45, '2025-11-27', 'اخرى', 'داتابيس٤', 'بيت', '16:53:00', 'B4245A12', 10, 24.72235175, 46.66500032),
(46, '2025-11-27', 'اخرى', 'داتابيس٥', 'بين', '16:55:00', 'F0BD5E91', 10, 24.71549074, 46.68697297),
(47, '2025-11-27', 'اخرى', 'داتابي٦', 'بيتن', '17:00:00', 'F2B0B2F6', 10, 24.71038372, 46.66912019),
(48, '2025-11-27', 'اخرى', 'داتابي٦', 'بيتنا', '17:00:00', 'DAFACBB7', 10, 24.71412629, 46.66500032),
(49, '2025-11-27', 'اخرى', 'داتابي٦', 'ر', '17:00:00', 'D66D4A73', 10, 24.72036366, 46.68697297),
(50, '2025-11-27', 'اخرى', 'داتابي٦', 'ب', '17:00:00', '4A8EA46F', 10, 24.73034280, 46.66225374),
(51, '2025-11-27', 'اخرى', 'داتابي٦', 'تتت', '17:00:00', '350C93CE', 10, 24.70716736, 46.64646089),
(52, '2025-11-27', 'اخرى', 'داتابي٦', 'تتت', '17:00:00', '5205ADEE', 10, 24.70716736, 46.64646089),
(53, '2025-11-27', 'اجتماع اصدقاء', 'تسسستتتتت', 'بيتنا', '17:06:00', 'C1021373', 10, 24.71038372, 46.69521272),
(54, '2025-11-27', 'اجتماع اصدقاء', 'تسسستتتتت', 'بيتنا', '17:06:00', '18D80C6B', 10, 24.72223481, 46.65813386),
(55, '2025-11-27', 'اجتماع عائلي', 'تيست٢٢', 'بيتنا', '17:26:00', '0B791B9A', 10, 24.72784809, 46.68559968),
(56, '2025-11-27', 'اجتماع عائلي', 'تيست٣٣', 'بيتنا', '17:27:00', '7BDC21A2', 10, 24.71287878, 46.66637361),
(57, '2025-11-28', 'حفلة تخرج', 'داتابي8', 'ff', '00:11:00', '93A34992', 7, 24.73012841, 46.66500032);

-- --------------------------------------------------------

--
-- Table structure for table `Notification`
--

CREATE TABLE `Notification` (
  `NotificationID` int NOT NULL,
  `time` varchar(10) NOT NULL,
  `UsedID` int NOT NULL,
  `GatheringID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Notification`
--

INSERT INTO `Notification` (`NotificationID`, `time`, `UsedID`, `GatheringID`) VALUES
(1, '24h', 1, 1),
(2, '1h', 2, 1),
(3, '24h', 3, 2),
(4, '1h', 1, 3),
(5, '24h', 2, 3),
(6, '1h', 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Participant`
--

CREATE TABLE `Participant` (
  `UserID` int NOT NULL,
  `GatheringID` int NOT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Participant`
--

INSERT INTO `Participant` (`UserID`, `GatheringID`, `status`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 4, 1),
(2, 1, 1),
(2, 2, 1),
(2, 3, 1),
(2, 4, 1),
(3, 2, 1),
(3, 3, 1),
(3, 4, 1),
(6, 4, 1),
(7, 8, 1),
(7, 9, 1),
(7, 10, 1),
(7, 11, 1),
(7, 12, 1),
(7, 13, 1),
(7, 14, 1),
(7, 15, 1),
(7, 16, 1),
(7, 17, 1),
(7, 18, 1),
(7, 19, 1),
(7, 20, 1),
(7, 21, 1),
(7, 22, 1),
(7, 23, 1),
(7, 24, 1),
(7, 25, 1),
(7, 26, 1),
(7, 27, 1),
(7, 28, 1),
(7, 29, 1),
(7, 30, 1),
(7, 31, 1),
(7, 32, 1),
(7, 34, 1),
(7, 35, 1),
(7, 36, 1),
(7, 37, 1),
(7, 38, 1),
(7, 39, 1),
(7, 41, 1),
(7, 57, 1),
(9, 33, 1),
(10, 40, 1),
(10, 41, 1),
(10, 42, 1),
(10, 43, 1),
(10, 44, 1),
(10, 45, 1),
(10, 46, 1),
(10, 49, 1),
(10, 50, 1),
(10, 51, 1),
(10, 52, 1),
(10, 53, 1),
(10, 54, 1),
(10, 55, 1),
(10, 56, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Reminders`
--

CREATE TABLE `Reminders` (
  `id` int NOT NULL,
  `GatheringID` int NOT NULL,
  `UserID` int NOT NULL,
  `remind_at` datetime NOT NULL,
  `sent` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Reminders`
--

INSERT INTO `Reminders` (`id`, `GatheringID`, `UserID`, `remind_at`, `sent`) VALUES
(1, 11, 7, '2025-11-25 02:00:00', 1),
(2, 12, 7, '2025-11-25 02:11:00', 1),
(3, 13, 7, '2025-11-27 15:12:00', 1),
(4, 14, 7, '2025-11-27 14:12:00', 1),
(5, 15, 7, '2025-11-27 01:23:00', 1),
(6, 16, 7, '2025-11-27 01:34:00', 1),
(7, 17, 7, '2025-11-26 01:41:00', 1),
(8, 18, 7, '2025-11-26 01:41:00', 1),
(9, 19, 7, '2025-11-26 02:45:00', 1),
(10, 20, 7, '2025-11-27 02:50:00', 1),
(11, 21, 7, '2025-11-27 01:49:00', 1),
(12, 22, 7, '2025-11-26 02:53:00', 1),
(13, 23, 7, '2025-11-27 01:53:00', 1),
(14, 24, 7, '2025-11-26 15:07:00', 1),
(15, 25, 7, '2025-11-27 14:12:00', 1),
(16, 26, 7, '2025-11-26 18:59:00', 1),
(17, 27, 7, '2025-11-26 18:01:00', 1),
(18, 28, 7, '2025-11-19 17:28:00', 1),
(19, 40, 10, '2025-11-26 16:24:00', 1),
(20, 41, 10, '2025-11-26 15:29:00', 1),
(21, 42, 10, '2025-12-03 16:31:00', 0),
(22, 43, 10, '2025-12-03 16:37:00', 0),
(23, 45, 10, '2025-11-26 16:53:00', 1),
(24, 46, 10, '2025-11-27 15:55:00', 1),
(25, 50, 10, '2025-11-26 17:00:00', 1),
(26, 51, 10, '2025-11-26 17:00:00', 1),
(27, 52, 10, '2025-11-26 17:00:00', 1),
(28, 53, 10, '2025-11-26 17:06:00', 1),
(29, 54, 10, '2025-11-26 17:06:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Task`
--

CREATE TABLE `Task` (
  `TaskID` int NOT NULL,
  `Description` varchar(200) NOT NULL,
  `note` varchar(200) DEFAULT '',
  `type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `UserID` int DEFAULT NULL,
  `GatheringID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Task`
--

INSERT INTO `Task` (`TaskID`, `Description`, `note`, `type`, `status`, `UserID`, `GatheringID`) VALUES
(1, 'إحضار الحلويات', 'كنافة وقطايف', 'item', 'pending', 1, 1),
(2, 'تحضير الشاي والقهوة', 'مع السكر والحليب', 'task', 'completed', 2, 1),
(3, 'تزيين القاعة', 'بالونات وزينة', 'task', 'pending', 3, 2),
(4, 'إحضار اللحم', 'للعشاء', 'item', 'pending', 1, 3),
(5, 'إعداد الألعاب', 'لأطفال العائلة', 'task', 'completed', 2, 3),
(6, 'ترتيب الجلسة', 'في المنتزه', 'task', 'pending', 3, 3),
(7, 'القهوة', '', 'task', 'completed', 1, 4),
(8, 'الفناجيل', '', 'task', 'pending', 3, 4),
(9, 'البالونات', '', 'item', 'completed', 6, 4),
(10, 'الشاهي', '', 'item', 'pending', 3, 4),
(11, 'شاي', 'بدون سكر', 'item', 'pending', 7, 23),
(13, 'حلا', '', 'item', 'pending', NULL, 28),
(14, 'شاي', '', 'item', 'pending', NULL, 41);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `Password`) VALUES
(1, 'سارة أحمد', 'sara.ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'فاطمة محمد', 'fatima.mohammed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'نورة عبدالله', 'nora.abdullah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(4, 'عبير', 'abeer@gmail.com', '$2y$10$nvGvZtz8zwDV9C5TWHbaKusDfNLArcH/Xyp3GQb.Vxz/QfnzkPgOW'),
(5, 'جمانه', 'juman@gmail.com', '$2y$10$1.RdOx3vHth9ljge7jgP5.6u4.Jh6ZhncBUHlN4lZhqJJq4KcsAU2'),
(6, 'دانة', 'dana@gmail.com', '$2y$10$.IBKUc/lSAa63FWuRrCZ/.AV3iLe4JAGWPDAveWtG7/04Eq8LDkxm'),
(7, 'رغد الحلوه', 'raghada896@gmail.com', '$2y$10$ZrO3agJNf.4iLXKH9DGsG.2jcnxyUuZ1BO7fKwmz/EJCCi/AmnX3u'),
(8, 'Raghad', 'r@gamil.com', '$2y$10$YYSRJzNqGN48UGHRWiDhFOVVrSqEDAZLVx1bnJANyoHMMY9pvFgMq'),
(9, 'Raghad2', 'r1@gmail.com', '$2y$10$xdlMOZD4AUPo3Vgm6qLgJ.52pKSSPSyxxhWO6Bl/aLZtrtWODPpf2'),
(10, 'nora', 'n@gmail.com', '$2y$10$k.Ec8wzEIfYoFH.6E3jeM.clPgs8BZrEiwDuLPYBJbAu1p5CXxhL6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gathering`
--
ALTER TABLE `gathering`
  ADD PRIMARY KEY (`GatheringID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `Notification`
--
ALTER TABLE `Notification`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `UsedID` (`UsedID`),
  ADD KEY `GatheringID` (`GatheringID`);

--
-- Indexes for table `Participant`
--
ALTER TABLE `Participant`
  ADD PRIMARY KEY (`UserID`,`GatheringID`),
  ADD KEY `GatheringID` (`GatheringID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `Reminders`
--
ALTER TABLE `Reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `GatheringID` (`GatheringID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `Task`
--
ALTER TABLE `Task`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `GatheringID` (`GatheringID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Reminders`
--
ALTER TABLE `Reminders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gathering`
--
ALTER TABLE `gathering`
  ADD CONSTRAINT `gathering_ibfk_1` FOREIGN KEY (`adminID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `Notification`
--
ALTER TABLE `Notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`GatheringID`) REFERENCES `Gathering` (`GatheringID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`UsedID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `Participant`
--
ALTER TABLE `Participant`
  ADD CONSTRAINT `participant_ibfk_1` FOREIGN KEY (`GatheringID`) REFERENCES `Gathering` (`GatheringID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `participant_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `Reminders`
--
ALTER TABLE `Reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`GatheringID`) REFERENCES `Gathering` (`GatheringID`),
  ADD CONSTRAINT `reminders_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `Task`
--
ALTER TABLE `Task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
