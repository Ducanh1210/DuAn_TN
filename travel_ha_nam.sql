-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th5 27, 2026 lúc 08:25 AM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hna_15`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `analytics_logs`
--

CREATE TABLE `analytics_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `event_type` enum('view_location','search','share_location','view_news','view_event','direction') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `search_keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#ef4444',
  `display_order` int NOT NULL DEFAULT '0',
  `status` enum('active','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `icon_color`, `display_order`, `status`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Tâm linh', 'tam-linh', 'Các khu du lịch kết hợp giữa sinh thái, thiên nhiên và yếu tố tâm linh.', 'uploads/categories/1779854485_ChatGPT Image 11_01_06 27 thg 5, 2026.png', '#f59e0b', 1, 'active', NULL, NULL, '2026-05-26 00:45:12', '2026-05-26 21:01:25'),
(3, 'Văn hóa - Lịch sử', 'van-hoa-lich-su', 'Di tích lịch sử, bảo tàng, làng nghề truyền thống.', 'uploads/categories/1779854741_ChatGPT Image 11_05_26 27 thg 5, 2026.png', '#8b5cf6', 2, 'active', NULL, NULL, '2026-05-26 20:52:40', '2026-05-26 21:05:41'),
(4, 'Sinh thái', 'sinh-thai', 'Khu du lịch sinh thái, danh lam thắng cảnh tự nhiên.', 'uploads/categories/1779854885_ChatGPT Image 11_07_57 27 thg 5, 2026.png', '#10b981', 3, 'active', NULL, NULL, '2026-05-26 20:52:40', '2026-05-26 21:08:05'),
(5, 'Ẩm thực', 'am-thuc', 'Nhà hàng, quán ăn đặc sản địa phương.', NULL, '#f43f5e', 4, 'active', NULL, NULL, '2026-05-26 20:52:40', '2026-05-26 20:52:40'),
(6, 'Lưu trú', 'luu-tru', 'Khách sạn, homestay, resort nghỉ dưỡng.', NULL, '#3b82f6', 5, 'active', NULL, NULL, '2026-05-26 20:52:40', '2026-05-26 20:52:40'),
(7, 'Check-in giải trí', 'check-in-giai-tri', 'Điểm check-in chụp ảnh, khu vui chơi giải trí.', NULL, '#ec4899', 6, 'active', NULL, NULL, '2026-05-26 20:52:40', '2026-05-26 20:52:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_logs`
--

CREATE TABLE `chat_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('visible','hidden','pending','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `direction_logs`
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
  `travel_mode` enum('driving','walking','cycling','motorbike') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'driving',
  `provider` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'google_maps',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `program` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `featured_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','cancelled','expired','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `feedback_reports`
--

CREATE TABLE `feedback_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `report_type` enum('wrong_info','duplicate_location','image_error','wrong_position','location_closed','system_suggestion','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` enum('location','news','event','comment','system') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint UNSIGNED DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','resolved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `geographic_boundaries`
--

CREATE TABLE `geographic_boundaries` (
  `id` bigint UNSIGNED NOT NULL,
  `region_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `boundary_geojson` json NOT NULL,
  `status` enum('active','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `itineraries`
--

CREATE TABLE `itineraries` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_days` int UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `share_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `itinerary_days`
--

CREATE TABLE `itinerary_days` (
  `id` bigint UNSIGNED NOT NULL,
  `itinerary_id` bigint UNSIGNED NOT NULL,
  `day_number` int UNSIGNED NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `itinerary_items`
--

CREATE TABLE `itinerary_items` (
  `id` bigint UNSIGNED NOT NULL,
  `day_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `order_index` int UNSIGNED NOT NULL,
  `estimated_time` time DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `detailed_history` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ward` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hà Nam',
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `opening_hours` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thumbnail_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attributes` json DEFAULT NULL,
  `average_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `review_count` int NOT NULL DEFAULT '0',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `source` enum('admin','community') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `status` enum('draft','published','hidden','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `locations`
--

INSERT INTO `locations` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `detailed_history`, `address`, `ward`, `district`, `province`, `lat`, `lng`, `opening_hours`, `phone`, `website_url`, `thumbnail_url`, `attributes`, `average_rating`, `review_count`, `meta_title`, `meta_description`, `view_count`, `source`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Quần thể khu du lịch Tam Chúc', 'quan-the-khu-du-lich-tam-chuc-1779781512', 'Chùa Tam Chúc là ngôi chùa lớn nhất thế giới, nằm trong quần thể khu du lịch sinh thái ngập nước ở thị trấn Ba Sao, huyện Kim Bảng, tỉnh Hà Nam.', NULL, NULL, 'Thị trấn Ba Sao, Huyện Kim Bảng, Tỉnh Hà Nam', NULL, NULL, 'Hà Nam', 20.5521700, 105.7950050, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', 1, 1, '2026-05-26 00:45:12', '2026-05-26 00:45:12'),
(2, 1, 'Khu du lịch Tam Chúc', 'khu-du-lich-tam-chuc', 'Khu du lịch tâm linh lớn nhất thế giới.', NULL, NULL, 'Thị trấn Ba Sao, Huyện Kim Bảng', NULL, NULL, 'Hà Nam', 20.5510000, 105.8150000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:00', '2026-05-26 20:56:58'),
(3, 4, 'Kẽm Trống', 'kem-trong', 'Thắng cảnh thiên nhiên nổi tiếng với sông núi hữu tình.', NULL, NULL, 'Xã Thanh Hải, Huyện Thanh Liêm', NULL, NULL, 'Hà Nam', 20.4630000, 105.9520000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:00', '2026-05-26 20:56:58'),
(4, 3, 'Đền Trần Thương', 'den-tran-thuong', 'Nơi thờ Hưng Đạo Đại Vương Trần Quốc Tuấn.', NULL, NULL, 'Xã Trần Hưng Đạo, Huyện Lý Nhân', NULL, NULL, 'Hà Nam', 20.5960000, 106.0790000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:01', '2026-05-26 20:56:58'),
(5, 3, 'Nhà bá Kiến', 'nha-ba-kien', 'Ngôi nhà cổ gắn liền với tác phẩm Chí Phèo.', NULL, NULL, 'Xã Hòa Hậu, Huyện Lý Nhân', NULL, NULL, 'Hà Nam', 20.6120000, 106.0270000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:01', '2026-05-26 20:56:58'),
(6, 3, 'Làng nghề trống Đọi Tam', 'lang-nghe-trong-doi-tam', 'Làng nghề làm trống truyền thống nổi tiếng.', NULL, NULL, 'Xã Đọi Sơn, Huyện Duy Tiên', NULL, NULL, 'Hà Nam', 20.5920000, 105.9770000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:01', '2026-05-26 20:56:58'),
(7, 1, 'Chùa Bà Đanh', 'chua-ba-danh', 'Ngôi chùa cổ kính nằm yên bình bên dòng sông Đáy.', NULL, NULL, 'Xã Ngọc Sơn, Huyện Kim Bảng', NULL, NULL, 'Hà Nam', 20.5400000, 105.8560000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:01', '2026-05-26 20:56:58'),
(8, 1, 'Thành phố Phủ Lý', 'thanh-pho-phu-ly', 'Trung tâm hành chính, kinh tế của tỉnh Hà Nam.', NULL, NULL, 'Ngã ba Hồng Phú, TP. Phủ Lý', NULL, NULL, 'Hà Nam', 20.5430000, 105.9120000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:14:01', '2026-05-26 20:14:01'),
(9, 3, 'Từ đường Nguyễn Khuyến', 'tu-duong-nguyen-khuyen', 'Nơi thờ và lưu giữ kỷ vật về Tam Nguyên Yên Đổ Nguyễn Khuyến.', NULL, NULL, 'Xã Trung Lương, Huyện Bình Lục', NULL, NULL, 'Hà Nam', 20.4700000, 105.9900000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:56:58', '2026-05-26 20:56:58'),
(10, 3, 'Khu tưởng niệm Nam Cao', 'khu-tuong-niem-nam-cao', 'Nơi an nghỉ và tưởng niệm nhà văn hiện thực xuất sắc Nam Cao.', NULL, NULL, 'Xã Hòa Hậu, Huyện Lý Nhân', NULL, NULL, 'Hà Nam', 20.6125000, 106.0275000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:56:58', '2026-05-26 20:56:58'),
(11, 3, 'Đền Lảnh Giang', 'den-lanh-giang', 'Ngôi đền linh thiêng thờ 3 vị tướng thời Hùng Vương.', NULL, NULL, 'Xã Mộc Nam, Huyện Duy Tiên', NULL, NULL, 'Hà Nam', 20.6720000, 106.0120000, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, 0, 'admin', 'published', NULL, NULL, '2026-05-26 20:56:58', '2026-05-26 20:56:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `location_images`
--

CREATE TABLE `location_images` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','rejected','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `location_images`
--

INSERT INTO `location_images` (`id`, `location_id`, `image_url`, `caption`, `is_thumbnail`, `sort_order`, `uploaded_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'locations/images/xfsAWQ3uvIxEBGqxivkWz0QLFQLMkDAOATJLE1lf.png', NULL, 0, 0, 1, 'approved', '2026-05-26 21:25:02', '2026-05-26 21:25:02'),
(2, 1, 'locations/images/z8dtcyjjEzbHo00Ry0DsXWLOdRuI5LsDMsYiMxxo.png', NULL, 0, 0, 1, 'approved', '2026-05-26 21:39:06', '2026-05-26 21:39:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `location_suggestions`
--

CREATE TABLE `location_suggestions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category_suggest` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `images` json DEFAULT NULL,
  `status` enum('pending','approved','rejected','need_more_info') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reject_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `processed_by` bigint UNSIGNED DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_location_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2026_05_22_000001_create_users_table', 1),
(5, '2026_05_22_000002_create_categories_table', 1),
(6, '2026_05_22_000003_create_geographic_boundaries_table', 1),
(7, '2026_05_22_000004_create_locations_table', 1),
(8, '2026_05_22_000005_create_location_images_table', 1),
(9, '2026_05_22_000006_create_location_suggestions_table', 1),
(10, '2026_05_22_000007_create_news_table', 1),
(11, '2026_05_22_000008_create_events_table', 1),
(12, '2026_05_22_000009_create_panoramas_table', 1),
(13, '2026_05_22_000010_create_panorama_hotspots_table', 1),
(14, '2026_05_22_000011_create_comments_table', 1),
(15, '2026_05_22_000012_create_favorites_table', 1),
(16, '2026_05_22_000013_create_itineraries_table', 1),
(17, '2026_05_22_000014_create_itinerary_days_table', 1),
(18, '2026_05_22_000015_create_itinerary_items_table', 1),
(19, '2026_05_22_000016_create_chat_logs_table', 1),
(20, '2026_05_22_000017_create_direction_logs_table', 1),
(21, '2026_05_22_000018_create_feedback_reports_table', 1),
(22, '2026_05_22_000019_create_analytics_logs_table', 1),
(23, '2026_05_26_074951_add_initial_view_to_panoramas_table', 2),
(24, '2026_05_27_020806_add_icon_color_to_categories_table', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('news','guide','announcement') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'news',
  `status` enum('draft','published','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `author_id` bigint UNSIGNED DEFAULT NULL,
  `view_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `summary`, `content`, `featured_image`, `type`, `status`, `author_id`, `view_count`, `meta_title`, `meta_description`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Công an TP.HCM tìm chủ sở hữu 2 xe máy, điện thoại liên quan vụ trộm cắp tại Vũng Tàu', 'cong-an-tphcm-tim-chu-so-huu-2-xe-may-dien-thoai-lien-quan-vu-trom-cap-tai-vung-tau-1779869542', '(PLO)- Công an TP.HCM đang tìm chủ sở hữu của 2 điện thoại di động và 2 xe máy thu giữ trong quá trình điều tra vụ án trộm cắp do bị can Lê An Đức thực hiện tại nhiều địa điểm trên địa bàn phường Vũng Tàu.', '<p>Ng&agrave;y 27-5, Cơ quan CSĐT C&ocirc;ng an TP.HCM cho biết đang thụ l&yacute; điều tra vụ &aacute;n trộm cắp t&agrave;i sản xảy ra tại nhiều địa điểm tr&ecirc;n địa b&agrave;n phường Vũng T&agrave;u do bị can L&ecirc; An Đức (31 tuổi, ngụ phường B&igrave;nh Lợi Trung, TP.HCM) thực hiện.</p>\r\n<p><img style=\"float: left;\" src=\"https://image.plo.vn/w850/Uploaded/2026/wohtohp/2026_05_27/z7870568045012-77261e7a4637c04b4ce9ca5030e11878-9571-9925.jpg.webp\" alt=\"z7870568045012_77261e7a4637c04b4ce9ca5030e11878.jpg\" width=\"395\" height=\"222\"></p>', 'news/5JQaJMQN1OkQ2eBawodGglyFSTdh309n7gCxA6Oa.png', 'news', 'published', 1, 0, NULL, NULL, '2026-05-27 15:00:00', '2026-05-27 01:01:04', '2026-05-27 01:12:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `panoramas`
--

CREATE TABLE `panoramas` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `scene_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` enum('active','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `initial_yaw` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `initial_pitch` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `initial_fov` decimal(8,4) NOT NULL DEFAULT '90.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `panoramas`
--

INSERT INTO `panoramas` (`id`, `location_id`, `scene_name`, `image_url`, `audio_url`, `sort_order`, `status`, `created_at`, `updated_at`, `initial_yaw`, `initial_pitch`, `initial_fov`) VALUES
(9, 1, 'Scene 1779844873', 'locations/panoramas/W56dLwjhIabgqFNuQfegG6iPcLHFbVKDYWSkrhBG.jpg', NULL, 0, 'active', '2026-05-26 18:21:13', '2026-05-26 18:21:13', 0.0000, 0.0000, 90.0000),
(10, 1, 'Scene 1779844874', 'locations/panoramas/SLcRcWlgtOzOAWm99ZF89d7zpMx9nQiCNNyRSfPe.jpg', NULL, 0, 'active', '2026-05-26 18:21:14', '2026-05-26 18:21:14', 0.0000, 0.0000, 90.0000),
(11, 1, 'Scene 1779844875', 'locations/panoramas/WRB3LBPBFa5kGeG8iDqScujJJR7edLNKQrXZOeC0.jpg', NULL, 0, 'active', '2026-05-26 18:21:15', '2026-05-26 18:21:15', 0.0000, 0.0000, 90.0000),
(12, 1, 'Scene 1779844876', 'locations/panoramas/xf6Ss2TB4MAlEa261SfzpOIo3U9nedk7u48HeH6c.jpg', NULL, 0, 'active', '2026-05-26 18:21:16', '2026-05-26 18:21:16', 0.0000, 0.0000, 90.0000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `panorama_hotspots`
--

CREATE TABLE `panorama_hotspots` (
  `id` bigint UNSIGNED NOT NULL,
  `panorama_id` bigint UNSIGNED NOT NULL,
  `target_panorama_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hotspot_type` enum('scene','info','link') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scene',
  `yaw` decimal(8,4) DEFAULT NULL,
  `pitch` decimal(8,4) DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `panorama_hotspots`
--

INSERT INTO `panorama_hotspots` (`id`, `panorama_id`, `target_panorama_id`, `title`, `hotspot_type`, `yaw`, `pitch`, `content`, `link_url`, `created_at`, `updated_at`) VALUES
(20, 11, 12, '', 'link', -4.5105, -10.3782, '', NULL, '2026-05-26 18:22:49', '2026-05-26 18:22:49'),
(21, 11, 10, '', 'link', -177.8492, 10.6694, '', NULL, '2026-05-26 18:22:49', '2026-05-26 18:22:49'),
(22, 9, 10, '', 'link', 0.3282, 7.9857, '', NULL, '2026-05-26 18:24:10', '2026-05-26 18:24:10'),
(23, 10, NULL, '', 'link', 178.9423, 13.3396, '', NULL, '2026-05-26 18:24:10', '2026-05-26 18:24:10'),
(24, 10, 11, '', 'link', 0.8373, -3.8067, '', NULL, '2026-05-26 18:24:10', '2026-05-26 18:24:51'),
(25, 12, 11, '', 'link', 176.4018, 18.9722, '', NULL, '2026-05-26 18:25:13', '2026-05-26 18:25:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `role` enum('user','moderator','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `status` enum('active','locked','deleted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `display_name`, `avatar_url`, `role`, `status`, `provider`, `provider_id`, `email_verified_at`, `remember_token`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin@example.com', 'Administrator', NULL, 'admin', 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-26 00:29:24', '2026-05-26 00:29:24');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_logs_location_id_foreign` (`location_id`),
  ADD KEY `analytics_logs_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `chat_logs`
--
ALTER TABLE `chat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_logs_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_location_id_foreign` (`location_id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`);

--
-- Chỉ mục cho bảng `direction_logs`
--
ALTER TABLE `direction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `direction_logs_user_id_foreign` (`user_id`),
  ADD KEY `direction_logs_location_id_foreign` (`location_id`);

--
-- Chỉ mục cho bảng `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `events_slug_unique` (`slug`),
  ADD KEY `events_location_id_foreign` (`location_id`),
  ADD KEY `events_created_by_foreign` (`created_by`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`location_id`),
  ADD KEY `favorites_location_id_foreign` (`location_id`);

--
-- Chỉ mục cho bảng `feedback_reports`
--
ALTER TABLE `feedback_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_reports_user_id_foreign` (`user_id`),
  ADD KEY `feedback_reports_resolved_by_foreign` (`resolved_by`);

--
-- Chỉ mục cho bảng `geographic_boundaries`
--
ALTER TABLE `geographic_boundaries`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `itineraries`
--
ALTER TABLE `itineraries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `itineraries_share_token_unique` (`share_token`),
  ADD KEY `itineraries_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `itinerary_days`
--
ALTER TABLE `itinerary_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `itinerary_days_itinerary_id_day_number_unique` (`itinerary_id`,`day_number`);

--
-- Chỉ mục cho bảng `itinerary_items`
--
ALTER TABLE `itinerary_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `itinerary_items_day_id_order_index_unique` (`day_id`,`order_index`),
  ADD UNIQUE KEY `itinerary_items_day_id_location_id_unique` (`day_id`,`location_id`),
  ADD KEY `itinerary_items_location_id_foreign` (`location_id`);

--
-- Chỉ mục cho bảng `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locations_slug_unique` (`slug`),
  ADD KEY `locations_category_id_foreign` (`category_id`),
  ADD KEY `locations_created_by_foreign` (`created_by`),
  ADD KEY `locations_updated_by_foreign` (`updated_by`),
  ADD KEY `locations_lat_lng_index` (`lat`,`lng`);

--
-- Chỉ mục cho bảng `location_images`
--
ALTER TABLE `location_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_images_location_id_foreign` (`location_id`),
  ADD KEY `location_images_uploaded_by_foreign` (`uploaded_by`);

--
-- Chỉ mục cho bảng `location_suggestions`
--
ALTER TABLE `location_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_suggestions_user_id_foreign` (`user_id`),
  ADD KEY `location_suggestions_processed_by_foreign` (`processed_by`),
  ADD KEY `location_suggestions_created_location_id_foreign` (`created_location_id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_slug_unique` (`slug`),
  ADD KEY `news_author_id_foreign` (`author_id`);

--
-- Chỉ mục cho bảng `panoramas`
--
ALTER TABLE `panoramas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panoramas_location_id_foreign` (`location_id`);

--
-- Chỉ mục cho bảng `panorama_hotspots`
--
ALTER TABLE `panorama_hotspots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panorama_hotspots_panorama_id_foreign` (`panorama_id`),
  ADD KEY `panorama_hotspots_target_panorama_id_foreign` (`target_panorama_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `analytics_logs`
--
ALTER TABLE `analytics_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `chat_logs`
--
ALTER TABLE `chat_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `direction_logs`
--
ALTER TABLE `direction_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `feedback_reports`
--
ALTER TABLE `feedback_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `geographic_boundaries`
--
ALTER TABLE `geographic_boundaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `itineraries`
--
ALTER TABLE `itineraries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `itinerary_days`
--
ALTER TABLE `itinerary_days`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `itinerary_items`
--
ALTER TABLE `itinerary_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `location_images`
--
ALTER TABLE `location_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `location_suggestions`
--
ALTER TABLE `location_suggestions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `panoramas`
--
ALTER TABLE `panoramas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `panorama_hotspots`
--
ALTER TABLE `panorama_hotspots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD CONSTRAINT `analytics_logs_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analytics_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chat_logs`
--
ALTER TABLE `chat_logs`
  ADD CONSTRAINT `chat_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `direction_logs`
--
ALTER TABLE `direction_logs`
  ADD CONSTRAINT `direction_logs_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `direction_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `feedback_reports`
--
ALTER TABLE `feedback_reports`
  ADD CONSTRAINT `feedback_reports_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `feedback_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `itineraries`
--
ALTER TABLE `itineraries`
  ADD CONSTRAINT `itineraries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `itinerary_days`
--
ALTER TABLE `itinerary_days`
  ADD CONSTRAINT `itinerary_days_itinerary_id_foreign` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `itinerary_items`
--
ALTER TABLE `itinerary_items`
  ADD CONSTRAINT `itinerary_items_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `itinerary_days` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerary_items_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `locations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `location_images`
--
ALTER TABLE `location_images`
  ADD CONSTRAINT `location_images_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_images_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `location_suggestions`
--
ALTER TABLE `location_suggestions`
  ADD CONSTRAINT `location_suggestions_created_location_id_foreign` FOREIGN KEY (`created_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `location_suggestions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `location_suggestions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `panoramas`
--
ALTER TABLE `panoramas`
  ADD CONSTRAINT `panoramas_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `panorama_hotspots`
--
ALTER TABLE `panorama_hotspots`
  ADD CONSTRAINT `panorama_hotspots_panorama_id_foreign` FOREIGN KEY (`panorama_id`) REFERENCES `panoramas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `panorama_hotspots_target_panorama_id_foreign` FOREIGN KEY (`target_panorama_id`) REFERENCES `panoramas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
