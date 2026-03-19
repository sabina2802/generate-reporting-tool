CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id` INTEGER PRIMARY KEY,
  `email` VARCHAR(255),
  `password_hash` VARCHAR(255),
  `role` VARCHAR(50),
  `created_at` TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `data_sources` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id` INTEGER PRIMARY KEY,
  `name` VARCHAR(255),
  `connection_string` TEXT,
  `type` VARCHAR(100),
  `user_id` INTEGER,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reports` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id` INTEGER PRIMARY KEY,
  `title` VARCHAR(255),
  `query` TEXT,
  `data_source_id` INTEGER,
  `user_id` INTEGER,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dashboards` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id` INTEGER PRIMARY KEY,
  `name` VARCHAR(255),
  `layout` JSON,
  `user_id` INTEGER,
  `created_at` TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `scheduled_reports` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id` INTEGER PRIMARY KEY,
  `report_id` INTEGER,
  `schedule_cron` VARCHAR(100),
  `recipients` TEXT,
  `is_active` BOOLEAN,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `exports` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id` INTEGER PRIMARY KEY,
  `report_id` INTEGER,
  `format` VARCHAR(50),
  `file_path` VARCHAR(500),
  `created_at` TIMESTAMP,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
