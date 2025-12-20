-- ==========================
-- AgriChain Unified Schema
-- ==========================

CREATE DATABASE IF NOT EXISTS agrichain DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE agrichain;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','farmer','inspector','transporter','packaging','customer') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Access logs
CREATE TABLE IF NOT EXISTS access_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  module VARCHAR(191),
  action VARCHAR(191),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crops
CREATE TABLE IF NOT EXISTS crops (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(191) NOT NULL,
  field VARCHAR(191),
  area_hectares DECIMAL(10,4),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fertilizers
CREATE TABLE IF NOT EXISTS fertilizers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  crop_id INT,
  type VARCHAR(191),
  quantity_kg DECIMAL(10,2),
  date DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE SET NULL,
  INDEX (user_id),
  INDEX (crop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Harvests
CREATE TABLE IF NOT EXISTS harvests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  crop_id INT,
  quantity_kg DECIMAL(12,2),
  date DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE SET NULL,
  INDEX (user_id),
  INDEX (crop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Batches (created by farmers, usable by all)
CREATE TABLE IF NOT EXISTS batches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  farmer_id INT NOT NULL,
  code VARCHAR(191) NOT NULL UNIQUE,
  product VARCHAR(191),
  quantity INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (farmer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inspections
CREATE TABLE IF NOT EXISTS inspections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  batch_id INT,
  notes TEXT,
  score DECIMAL(4,2),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
  INDEX (user_id),
  INDEX (batch_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inspection Photos
CREATE TABLE IF NOT EXISTS inspection_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inspection_id INT NOT NULL,
  filename VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (inspection_id) REFERENCES inspections(id) ON DELETE CASCADE,
  INDEX (inspection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  batch_id INT,
  quantity INT DEFAULT 1,
  status ENUM('pending','shipped','delivered','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
  INDEX (user_id),
  INDEX (batch_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feedback
CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inventory (packaging module)
CREATE TABLE IF NOT EXISTS inventory (
  id INT AUTO_INCREMENT PRIMARY KEY,
  batch_id INT NOT NULL,
  label_code VARCHAR(191) UNIQUE,
  quantity INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE,
  INDEX (batch_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT,
  read_status BOOLEAN DEFAULT FALSE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================
-- Example users
-- ==========================
-- INSERT INTO users (name,email,password_hash,role) VALUES ('Admin','admin@example.com','$2y$10$EXAMPLEHASH','admin');
-- INSERT INTO users (name,email,password_hash,role) VALUES ('Farmer One','farmer@example.com','$2y$10$EXAMPLEHASH','farmer');
