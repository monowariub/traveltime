-- Database Schema for TravelTime AI
-- Run this SQL script in your MySQL database (e.g., using phpMyAdmin)

CREATE DATABASE IF NOT EXISTS traveltime_db;
USE traveltime_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Destinations Table
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    best_time VARCHAR(100),
    traveler_type VARCHAR(100),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    base_price DECIMAL(10, 2) DEFAULT 0.00, -- Base price per person
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table (Updated with Hotel Type)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination_id INT NOT NULL,
    travel_date DATE NOT NULL,
    travelers INT NOT NULL DEFAULT 1,
    travel_mode ENUM('bus', 'train', 'plane') NOT NULL,
    hotel_type ENUM('budget', 'standard', 'luxury') NOT NULL DEFAULT 'standard',
    total_cost DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- Messages Table (Chat)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- Nullable for bot messages or anonymous
    user_name VARCHAR(255),
    message_text TEXT NOT NULL,
    message_type ENUM('user', 'bot') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recommendations Table
CREATE TABLE IF NOT EXISTS recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination_name VARCHAR(255),
    reason TEXT,
    rating DECIMAL(3, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Sample Data
INSERT INTO destinations (name, description, image_url, best_time, traveler_type, rating, base_price) VALUES
('Bali, Indonesia', 'Bali is a province of Indonesia and the westernmost of the Lesser Sunda Islands.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4', 'April to October', 'Nature Lovers', 4.8, 500.00),
('Kyoto, Japan', 'Kyoto is famous for its numerous classical Buddhist temples, as well as gardens, imperial palaces, Shinto shrines and traditional wooden houses.', 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e', 'March to May', 'Culture Seekers', 4.9, 800.00),
('Santorini, Greece', 'Santorini is one of the Cyclades islands in the Aegean Sea.', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff', 'September to October', 'Couples', 4.7, 700.00),
('Cox''s Bazar, Bangladesh', 'Longest natural sea beach in the world.', 'Capture.PNG', 'November to March', 'Beach Lovers', 4.5, 100.00),
('Saint Martin, Bangladesh', 'The only coral island in Bangladesh.', 'Image/Capture.PNG', 'November to February', 'Couples', 4.6, 150.00),
('Sreemangal, Bangladesh', 'The tea capital of Bangladesh.', 'Image/Capture 2.PNG', 'June to August', 'Nature Lovers', 4.7, 80.00);

-- Insert Admin User
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@traveltime.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
