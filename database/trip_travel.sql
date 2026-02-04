-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2024 at 01:59 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trip_travel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aid` int(11) NOT NULL,
  `auser` varchar(100) NOT NULL,
  `aemail` varchar(150) NOT NULL,
  `adob` date NOT NULL,
  `aphone` varchar(15) NOT NULL,
  `apass` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `auser`, `aemail`, `adob`, `aphone`, `apass`, `created_at`, `updated_at`) VALUES
(3, 'Ubaid Soomro', 'ubaidsoomro505@gmail.com', '2002-06-16', '1234567890', '@321', '2024-09-17 11:34:50', '2024-09-20 06:58:18');

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `id` int(50) NOT NULL,
  `a_name` varchar(255) NOT NULL,
  `a_profetion` varchar(255) NOT NULL,
  `a_image` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`id`, `a_name`, `a_profetion`, `a_image`, `date_time`) VALUES
(9, 'Aliza', 'Travel Expert', '1726912305_image2.jpg', '2024-09-21 14:51:45'),
(16, 'Ahmed', 'Professional Dealer', '1728373776_team_3.png', '2024-10-08 12:49:36'),
(17, 'Ali ', 'Airline Pro', '1728373827_team_2.jpg', '2024-10-08 12:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `blog_id` int(11) NOT NULL,
  `blog_title` varchar(255) NOT NULL,
  `blog_image` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`blog_id`, `blog_title`, `blog_image`, `timestamp`) VALUES
(1, '5 BEST SPOTS IN JAPAN ', 'img/blogimg/blog-1.jpg', '2024-09-19 13:13:34'),
(2, '18 BEST WATERFALLS IN INDONESIA', 'img/blogimg/blog-2.jpg\r\n', '2024-09-19 13:57:09'),
(3, '25 BEST ISLANDS IN INDONESIA', 'img/blogimg/blog-3.jpg\r\n', '2024-09-19 13:58:17'),
(4, 'BALI WATERFALLS MAP', 'img/blogimg/blog-4.jpg', '2024-09-19 13:59:29');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `city_name` varchar(255) NOT NULL,
  `cover_image` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_id`, `country_name`, `city_name`, `cover_image`) VALUES
(17, 'Pakistan', 'Qasimabad Hyderabad', 'img/cityimages/Qasimabad-Hyderabad-Cover-Photo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `contact_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`contact_id`, `name`, `email`, `subject`, `message`) VALUES
(2, 'Ubaid', 'Ubaidsoomro505@gmail.com', 'hellow', 'hellow i am a good boy'),
(3, 'Ubaid Soomro', 'ubaidsoomro505@gmail.com', 'abc', 'hellow');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int(50) NOT NULL,
  `userid` int(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `usermessage` varchar(500) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `userid`, `email`, `image`, `usermessage`, `date_time`, `username`) VALUES
(1, 1, 'ubaidsoomro505@gmail.com', '6704fa14a6868.jpg', '\"Highly recommend! Friendly staff and a fantastic atmosphere throughout.\"', '2024-10-08 14:23:32', 'Ubaid '),
(7, 2, 'memon1ahmed@gmail.com', '6705100ec5d56.png', '\"Incredible trip! Well-organized, beautiful locations, and wonderful guides provided.\"', '2024-10-08 15:57:18', 'Ahmed'),
(10, 4, 'alihassanchand24@gmail.com', '67051384ab46f.jpg', '\"Amazing experience! The service was exceptional and the food delicious!\"', '2024-10-08 16:12:04', 'Ali');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `trip_image` varchar(255) NOT NULL,
  `trip_name` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `starts_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `budget` int(11) DEFAULT NULL,
  `persons` int(255) NOT NULL,
  `stars` int(10) NOT NULL,
  `duration_days` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `user_id`, `trip_image`, `trip_name`, `description`, `starts_date`, `end_date`, `destination`, `budget`, `persons`, `stars`, `duration_days`) VALUES
(8, NULL, 'img/tripimages/rshmpg.jpg', 'Multan', 'Explore the ancient city of Multan, known as the \'City of Saints,\' rich in history and culture. Multan boasts magnificent architectural marvels, including centuries-old shrines, mosques, and forts. Wander through the vibrant bazaars, famous for handcrafted pottery, colorful fabrics, and exquisite embroidery. Experience the spiritual essence of Sufi traditions as you visit the shrines of renowned saints. Multan\'s unique blend of history, culture, and art offers travelers an unforgettable journey through Pakistan\'s heritage.', '2024-09-01', '2024-09-27', 'multan', 10000, 20, 3, 30);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `user_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `profile_image`, `is_verified`, `verification_token`, `token_expiry`, `token`, `reset_token`, `user_image`) VALUES
(1, 'Ubaid ', 'ubaidsoomro505@gmail.com', '$2y$10$i3kp9SvWYgGaOauu8YR.BeWXWzDYoKe8BrJBrFh7cyWLlX6x043uy', '66ee873dace5d.jpg', 0, NULL, NULL, NULL, NULL, NULL),
(2, 'Ahmed', 'memon1ahmed@gmail.com', '$2y$10$qg1KLr9cP6i./fGqXDAlCOj2BxKrWnMF2ay.47W4atJbED7AhVM4C', '66ee8a452e4dc.jpg', 0, NULL, NULL, NULL, NULL, NULL),
(3, 'Muntaha', 'sheikhmuntaha26@gmail.com', '$2y$10$M10awtZet.sqH/.Zrbjtj.lspxTD9RgoIK9p8KLcAcmPwEDOiPvH2', '66eead59f4232.jpg', 0, NULL, NULL, NULL, NULL, NULL),
(4, 'Ali Hassan', 'alihassanchand32@gmail.com', '$2y$10$CZhj3f/sPuT5ADJsY6LVt.d9.nsi/jxzbWLZR0WeXyeM0Ncum3WRG', '66eeb12658b79.jpg', 0, NULL, NULL, NULL, NULL, NULL),
(5, 'Ali Hassan Chand', 'alihassanchand24@gmail.com', '$2y$10$c471PLyawEe725abbMT0J.vVACKOsB570mHdLt/bdrr9c7ph3UIie', NULL, 0, NULL, NULL, NULL, NULL, 'image3.jpg'),
(6, 'Aun Ali', 'hussaintalib1422@gmail.com', '$2y$10$8R/FyHbQk5NxkifsjZPNyOD6lAjQk8bUB2XubHtUZ9QPlrElOP0Uq', NULL, 0, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_image` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `code` int(10) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_image`, `password_hash`, `first_name`, `last_name`, `date_time`, `code`, `is_verified`) VALUES
(36, 'img/userimages/image3.jpg', '$2y$10$ftTpOQponeWQ3AYVwpVLVuX44iCdGMmgObl3dvKxxiKOqcRulIJkS', 'Haji', 'Soomro', '2024-09-19 18:50:41', 0, 0),
(37, 'img/userimages/image3.jpg', '$2y$10$Iy.mZZcFC86PwM6bKbVqaupbHJ2i5ZmOVVV/.fHKpOnM7jO2cRnia', 'sami', 'soomro', '2024-09-19 22:49:48', 0, 0),
(39, 'img/userimages/wp8050706-lock-screen-pc-wallpapers.jpg', '$2y$10$Eot8k/TAgVf5s5WyI5Nh/O4p7Cv9qQEu3wLh.h65bHZnnV69mZKem', 'khalid', 'soomro', '2024-09-20 15:25:12', 0, 0),
(40, 'img/userimages/image2.jpg', '$2y$10$MP3IbN6ynNQ5mUNJhY8On.UXFXSow0G3oxMrUFAGOhdpMWZV813hq', 'ubaid', 'Soomro', '2024-09-20 15:49:56', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`),
  ADD UNIQUE KEY `aemail` (`aemail`);

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`blog_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `blog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
