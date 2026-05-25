-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2026 at 02:00 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_ha_nam`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics_logs`
--

CREATE TABLE `analytics_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `event_type` enum('view_location','search','share_location','view_news','view_event','direction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_keyword` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `status` enum('active','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Tâm linh', 'tam-linh', 'Các địa điểm chùa, đền, di tích tâm linh', 'temple', 1, 'active', '2026-05-22 02:00:25', '2026-05-22 02:00:25'),
(2, 'Văn hóa', 'van-hoa', 'Các địa điểm lịch sử, văn hóa địa phương', 'museum', 2, 'active', '2026-05-22 02:00:25', '2026-05-22 02:00:25'),
(3, 'Ẩm thực', 'am-thuc', 'Quán ăn, đặc sản địa phương', 'food', 3, 'active', '2026-05-22 02:00:25', '2026-05-22 02:00:25'),
(4, 'Check-in', 'check-in', 'Địa điểm tham quan, chụp ảnh', 'camera', 4, 'active', '2026-05-22 02:00:25', '2026-05-22 02:00:25'),
(5, 'Lưu trú', 'luu-tru', 'Khách sạn, homestay, nhà nghỉ', 'hotel', 5, 'active', '2026-05-22 02:00:25', '2026-05-22 02:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `chat_logs`
--

CREATE TABLE `chat_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('visible','hidden','pending','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visible',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `direction_logs`
--

CREATE TABLE `direction_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `start_lat` decimal(10,7) DEFAULT NULL,
  `start_lng` decimal(10,7) DEFAULT NULL,
  `destination_lat` decimal(10,7) NOT NULL,
  `destination_lng` decimal(10,7) NOT NULL,
  `distance_meters` int UNSIGNED DEFAULT NULL,
  `duration_seconds` int UNSIGNED DEFAULT NULL,
  `travel_mode` enum('driving','walking','cycling','motorbike') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'driving',
  `provider` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'google_maps',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `program` text COLLATE utf8mb4_unicode_ci,
  `location_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `featured_image` text COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','cancelled','expired','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_reports`
--

CREATE TABLE `feedback_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `report_type` enum('wrong_info','duplicate_location','image_error','wrong_position','location_closed','system_suggestion','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` enum('location','news','event','comment','system') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','resolved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_response` text COLLATE utf8mb4_unicode_ci,
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geographic_boundaries`
--

CREATE TABLE `geographic_boundaries` (
  `id` bigint UNSIGNED NOT NULL,
  `region_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `boundary_geojson` json NOT NULL,
  `status` enum('active','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `geographic_boundaries`
--

INSERT INTO `geographic_boundaries` (`id`, `region_name`, `boundary_geojson`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hà Nam', '{\"type\": \"FeatureCollection\", \"features\": []}', 'active', '2026-05-22 02:00:25', '2026-05-22 02:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `itineraries`
--

CREATE TABLE `itineraries` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_days` int UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `share_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `itinerary_days`
--

CREATE TABLE `itinerary_days` (
  `id` bigint UNSIGNED NOT NULL,
  `itinerary_id` bigint UNSIGNED NOT NULL,
  `day_number` int UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `itinerary_items`
--

CREATE TABLE `itinerary_items` (
  `id` bigint UNSIGNED NOT NULL,
  `day_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `order_index` int UNSIGNED NOT NULL,
  `estimated_time` time DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `detailed_history` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `ward` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT 'Hà Nam',
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `opening_hours` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` text COLLATE utf8mb4_unicode_ci,
  `thumbnail_url` text COLLATE utf8mb4_unicode_ci,
  `view_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `source` enum('admin','community') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `status` enum('draft','published','hidden','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `detailed_history`, `address`, `ward`, `district`, `province`, `lat`, `lng`, `opening_hours`, `phone`, `website_url`, `thumbnail_url`, `view_count`, `source`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Chùa Tam Chúc', 'chua-tam-chuc', 'Địa điểm du lịch tâm linh nổi bật tại Hà Nam.', 'Chùa Tam Chúc là một trong những điểm du lịch tâm linh nổi bật, thu hút nhiều du khách tham quan.', NULL, 'Thị trấn Ba Sao, Kim Bảng, Hà Nam', NULL, NULL, 'Hà Nam', '20.5835000', '105.9159000', NULL, NULL, NULL, NULL, 0, 'admin', 'published', 1, NULL, '2026-05-22 02:00:25', '2026-05-22 02:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `location_images`
--

CREATE TABLE `location_images` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location_suggestions`
--

CREATE TABLE `location_suggestions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_suggest` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `images` json DEFAULT NULL,
  `status` enum('pending','approved','rejected','need_more_info') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reject_reason` text COLLATE utf8mb4_unicode_ci,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_location_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image` text COLLATE utf8mb4_unicode_ci,
  `type` enum('news','guide','announcement') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'news',
  `status` enum('draft','published','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `author_id` bigint UNSIGNED DEFAULT NULL,
  `view_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `panoramas`
--

CREATE TABLE `panoramas` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `scene_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio_url` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` enum('active','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `panorama_hotspots`
--

CREATE TABLE `panorama_hotspots` (
  `id` bigint UNSIGNED NOT NULL,
  `panorama_id` bigint UNSIGNED NOT NULL,
  `target_panorama_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hotspot_type` enum('scene','info','link') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scene',
  `yaw` decimal(8,4) DEFAULT NULL,
  `pitch` decimal(8,4) DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `link_url` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` text COLLATE utf8mb4_unicode_ci,
  `role` enum('user','moderator','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `status` enum('active','locked','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `display_name`, `avatar_url`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$example_hash_change_me', 'admin@example.com', 'Quản trị viên', NULL, 'admin', 'active', NULL, '2026-05-22 02:00:25', '2026-05-22 02:00:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_analytics_event_time` (`event_type`,`created_at`),
  ADD KEY `idx_analytics_location` (`location_id`),
  ADD KEY `idx_analytics_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categories_status_order` (`status`,`display_order`);

--
-- Indexes for table `chat_logs`
--
ALTER TABLE `chat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_logs_session` (`session_id`,`created_at`),
  ADD KEY `idx_chat_logs_user` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_location_status` (`location_id`,`status`,`created_at`),
  ADD KEY `idx_comments_user` (`user_id`),
  ADD KEY `idx_comments_parent` (`parent_id`);

--
-- Indexes for table `direction_logs`
--
ALTER TABLE `direction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_direction_logs_user` (`user_id`),
  ADD KEY `idx_direction_logs_location` (`location_id`),
  ADD KEY `idx_direction_logs_created_at` (`created_at`),
  ADD KEY `idx_direction_logs_coords` (`start_lat`,`start_lng`,`destination_lat`,`destination_lng`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_events_created_by` (`created_by`),
  ADD KEY `idx_events_time_status` (`start_time`,`end_time`,`status`),
  ADD KEY `idx_events_featured` (`is_featured`),
  ADD KEY `idx_events_location` (`location_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`location_id`),
  ADD KEY `idx_favorites_user` (`user_id`),
  ADD KEY `idx_favorites_location` (`location_id`);

--
-- Indexes for table `feedback_reports`
--
ALTER TABLE `feedback_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_feedback_reports_status` (`status`),
  ADD KEY `idx_feedback_reports_user` (`user_id`),
  ADD KEY `idx_feedback_reports_target` (`target_type`,`target_id`),
  ADD KEY `idx_feedback_reports_resolved_by` (`resolved_by`);

--
-- Indexes for table `geographic_boundaries`
--
ALTER TABLE `geographic_boundaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_geographic_boundaries_status` (`status`);

--
-- Indexes for table `itineraries`
--
ALTER TABLE `itineraries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `share_token` (`share_token`),
  ADD KEY `idx_itineraries_user` (`user_id`),
  ADD KEY `idx_itineraries_token` (`share_token`);

--
-- Indexes for table `itinerary_days`
--
ALTER TABLE `itinerary_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_itinerary_day` (`itinerary_id`,`day_number`),
  ADD KEY `idx_itinerary_days_itinerary` (`itinerary_id`);

--
-- Indexes for table `itinerary_items`
--
ALTER TABLE `itinerary_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_itinerary_item_order` (`day_id`,`order_index`),
  ADD UNIQUE KEY `uq_itinerary_item_location` (`day_id`,`location_id`),
  ADD KEY `idx_itinerary_items_day` (`day_id`),
  ADD KEY `idx_itinerary_items_location` (`location_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_locations_created_by` (`created_by`),
  ADD KEY `fk_locations_updated_by` (`updated_by`),
  ADD KEY `idx_locations_category` (`category_id`),
  ADD KEY `idx_locations_status` (`status`),
  ADD KEY `idx_locations_source` (`source`),
  ADD KEY `idx_locations_view_count` (`view_count`),
  ADD KEY `idx_locations_coords` (`lat`,`lng`),
  ADD KEY `idx_locations_name` (`name`);

--
-- Indexes for table `location_images`
--
ALTER TABLE `location_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_location_images_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_location_images_location` (`location_id`),
  ADD KEY `idx_location_images_status` (`status`);

--
-- Indexes for table `location_suggestions`
--
ALTER TABLE `location_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_location_suggestions_user_status` (`user_id`,`status`),
  ADD KEY `idx_location_suggestions_status` (`status`),
  ADD KEY `idx_location_suggestions_processed_by` (`processed_by`),
  ADD KEY `idx_location_suggestions_created_location` (`created_location_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_news_author` (`author_id`),
  ADD KEY `idx_news_type_status` (`type`,`status`),
  ADD KEY `idx_news_published_at` (`published_at`);

--
-- Indexes for table `panoramas`
--
ALTER TABLE `panoramas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_panoramas_location` (`location_id`);

--
-- Indexes for table `panorama_hotspots`
--
ALTER TABLE `panorama_hotspots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_panorama_hotspots_panorama` (`panorama_id`),
  ADD KEY `idx_panorama_hotspots_target` (`target_panorama_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role_status` (`role`,`status`),
  ADD KEY `idx_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics_logs`
--
ALTER TABLE `analytics_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_logs`
--
ALTER TABLE `chat_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `direction_logs`
--
ALTER TABLE `direction_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_reports`
--
ALTER TABLE `feedback_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geographic_boundaries`
--
ALTER TABLE `geographic_boundaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `itineraries`
--
ALTER TABLE `itineraries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `itinerary_days`
--
ALTER TABLE `itinerary_days`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `itinerary_items`
--
ALTER TABLE `itinerary_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `location_images`
--
ALTER TABLE `location_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location_suggestions`
--
ALTER TABLE `location_suggestions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panoramas`
--
ALTER TABLE `panoramas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panorama_hotspots`
--
ALTER TABLE `panorama_hotspots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD CONSTRAINT `fk_analytics_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_analytics_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `chat_logs`
--
ALTER TABLE `chat_logs`
  ADD CONSTRAINT `fk_chat_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `direction_logs`
--
ALTER TABLE `direction_logs`
  ADD CONSTRAINT `fk_direction_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_direction_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_events_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback_reports`
--
ALTER TABLE `feedback_reports`
  ADD CONSTRAINT `fk_feedback_resolved_by` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `itineraries`
--
ALTER TABLE `itineraries`
  ADD CONSTRAINT `fk_itineraries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `itinerary_days`
--
ALTER TABLE `itinerary_days`
  ADD CONSTRAINT `fk_itinerary_days_itinerary` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `itinerary_items`
--
ALTER TABLE `itinerary_items`
  ADD CONSTRAINT `fk_itinerary_items_day` FOREIGN KEY (`day_id`) REFERENCES `itinerary_days` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_itinerary_items_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `fk_locations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_locations_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_locations_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `location_images`
--
ALTER TABLE `location_images`
  ADD CONSTRAINT `fk_location_images_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_location_images_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `location_suggestions`
--
ALTER TABLE `location_suggestions`
  ADD CONSTRAINT `fk_suggestions_created_location` FOREIGN KEY (`created_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suggestions_processed_by` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suggestions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `panoramas`
--
ALTER TABLE `panoramas`
  ADD CONSTRAINT `fk_panoramas_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `panorama_hotspots`
--
ALTER TABLE `panorama_hotspots`
  ADD CONSTRAINT `fk_hotspots_panorama` FOREIGN KEY (`panorama_id`) REFERENCES `panoramas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hotspots_target_panorama` FOREIGN KEY (`target_panorama_id`) REFERENCES `panoramas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
