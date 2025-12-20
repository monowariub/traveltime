-- AgriChain schema (MySQL / XAMPP friendly)
-- Import this file into phpMyAdmin or `mysql` to create the schema.
CREATE DATABASE IF NOT EXISTS agrichain DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE agrichain;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191),
  email VARCHAR(191) UNIQUE,
  password_hash VARCHAR(255),
  role VARCHAR(50),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS access_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  module VARCHAR(191),
  action VARCHAR(191),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS crops (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  name VARCHAR(191),
  field VARCHAR(191),
  area_hectares DECIMAL(10,4),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fertilizers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  crop_name VARCHAR(191),
  type VARCHAR(191),
  quantity_kg DECIMAL(10,2),
  date DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS harvests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  crop_name VARCHAR(191),
  quantity_kg DECIMAL(12,2),
  date DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inspections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  batch_code VARCHAR(191),
  notes TEXT,
  score DECIMAL(4,2),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inspection_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inspection_id INT,
  filename VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (inspection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS batches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(191),
  product VARCHAR(191),
  quantity INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  batch_code VARCHAR(191),
  quantity INT,
  status VARCHAR(50),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example admin user (commented out). To create an admin, register via the app or uncomment and set a proper password_hash.
-- INSERT INTO users (name,email,password_hash,role) VALUES ('Admin','admin@example.com','$2y$10$EXAMPLEHASH', 'admin');
