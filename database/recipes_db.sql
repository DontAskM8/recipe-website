-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2025 at 08:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `competitions`
--

CREATE TABLE `competitions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competitions`
--

INSERT INTO `competitions` (`id`, `name`, `description`, `start_time`, `end_time`, `isActive`) VALUES
(18, 'Nasi Lemak', 'Compete for the best recipe for authentic Malaysian&#039;s favourtie nasi lemak.', '2025-04-13 01:40:00', '2025-05-31 01:36:00', 1),
(19, 'Fried rice', 'Everyone loves fried rice, show the world your fried rice recipe!', '2025-04-10 01:37:00', '2025-04-30 01:37:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `competition_entries`
--

CREATE TABLE `competition_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_votes`
--

CREATE TABLE `competition_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

CREATE TABLE `meal_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `meal_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `meal_type` enum('breakfast','lunch','dinner','snack') NOT NULL,
  `recipe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_plans`
--

INSERT INTO `meal_plans` (`id`, `user_id`, `meal_name`, `meal_date`, `created_at`, `meal_type`, `recipe_id`) VALUES
(1, 3, 'lunch', '2025-04-11', '2025-04-09 17:39:02', 'breakfast', 2);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `created_at`) VALUES
(1, 1, 'I Love KFC', 'KFC da best in the world, fight me if you disagree!!!', '2025-04-02 19:37:35'),
(2, 1, 'I Love McDonald', 'Fight me if u disagree!', '2025-04-02 20:18:57'),
(3, 3, 'I Love Chicago', 'Please agree with me', '2025-04-02 21:38:25');

-- --------------------------------------------------------

--
-- Table structure for table `posts_comments`
--

CREATE TABLE `posts_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts_comments`
--

INSERT INTO `posts_comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'I disagree!! Now fight me', '2025-04-02 19:59:22'),
(2, 1, 1, 'Ok', '2025-04-02 20:02:18'),
(3, 1, 1, 'NO!', '2025-04-02 21:25:12'),
(4, 1, 3, 'I Agree', '2025-04-02 21:25:43'),
(5, 1, 3, 'Ofc', '2025-04-02 22:00:11'),
(0, 1, 1, 'hi', '2025-04-03 08:36:24');

-- --------------------------------------------------------

--
-- Table structure for table `posts_ratings`
--

CREATE TABLE `posts_ratings` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts_ratings`
--

INSERT INTO `posts_ratings` (`id`, `post_id`, `user_id`, `rating`, `created_at`) VALUES
(1, 1, 1, 5, '2025-04-03 08:37:47'),
(2, 1, 1, 5, '2025-04-03 08:37:47'),
(3, 1, 1, 5, '2025-04-03 08:37:47'),
(4, 2, 3, 3, '2025-04-02 22:14:08'),
(5, 3, 3, 4, '2025-04-02 22:14:15'),
(6, 2, 3, 3, '2025-04-02 22:14:08'),
(7, 2, 3, 3, '2025-04-02 22:14:08'),
(8, 2, 1, 4, '2025-04-02 22:14:40'),
(9, 1, 3, 3, '2025-04-02 22:16:27'),
(0, 0, 3, 1, '2025-04-09 17:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cuisine` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ingredients` varchar(255) NOT NULL,
  `steps` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `cuisine`, `description`, `ingredients`, `steps`, `username`) VALUES
(1, 'Butter Prawn', 'Malaysian', 'deep fried butter prawn', 'prawn\r\nbutter\r\ncooking oil', '1. heat the oil\r\n2. fry prawn without removing shell', 'admin'),
(2, 'YZ Fried Rice ', 'Chinese', 'YangZhou Fried Rice, a traditional chinese styled fried rice recipe', '1. Rice \r\n2. Salt\r\n3. Cooking Oil\r\n4. 2 Eggs', '1. Heat a bit of oil\r\n2. Put in eggs and stir fry', 'admin'),
(3, 'Pad Krao Pao', 'Thai', 'traditional thai-style spicy basil stir fry ground pork with rice', '- rice\r\n- egg \r\n- basil\r\n- ground pork\r\n- garlic\r\n- red chilli\r\n', '1. Prepare ingredients\r\n2. pound chilli and garlic using pestle and mortar into a paste\r\n3. heat the oil\r\n4. put in the chilli and garlic paste', 'test'),
(4, 'Curry Chicken', 'Indian', 'An Indian-styled chicken dish with sauce/gravy made from different ground spices ', '- Curry Powder\r\n- Chicken\r\n- Diced onion', '1. Heat the oil\r\n2. Stir fry diced onions', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_hash` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `role`, `date_created`, `password_hash`) VALUES
(1, 'test', 'test@gmail.com', 'user', '2025-03-21 15:35:23', '$2y$10$xcc1mrdwIOBZkYKrnZIedONWgfEV8we4as2Bb1ZSpbkpAY0rN9KBu'),
(3, 'admin', 'admin@gmail.com', 'admin', '2025-03-22 15:54:40', '$2y$10$2vR7NGtEC2/PtDgl5KF/l.vqDsIxbl70kFwZnAwIcWYLByOgH/GwW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `competitions`
--
ALTER TABLE `competitions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competition_entries`
--
ALTER TABLE `competition_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `competition_entries_ibfk_1` (`competition_id`),
  ADD KEY `competition_entries_ibfk_2` (`recipe_id`),
  ADD KEY `competition_entries_ibfk_3` (`user_id`);

--
-- Indexes for table `competition_votes`
--
ALTER TABLE `competition_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `competition_votes_ibfk_1` (`competition_id`),
  ADD KEY `competition_votes_ibfk_2` (`recipe_id`),
  ADD KEY `competition_votes_ibfk_3` (`user_id`);

--
-- Indexes for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competitions`
--
ALTER TABLE `competitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `competition_entries`
--
ALTER TABLE `competition_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `competition_votes`
--
ALTER TABLE `competition_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `competition_entries`
--
ALTER TABLE `competition_entries`
  ADD CONSTRAINT `competition_entries_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `competition_entries_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `competition_entries_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `competition_votes`
--
ALTER TABLE `competition_votes`
  ADD CONSTRAINT `competition_votes_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `competition_votes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `competition_votes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD CONSTRAINT `meal_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meal_plans_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
