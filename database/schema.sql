-- ═══════════════════════════════════════════════════════
-- TravenzoTravel - Database Schema
-- ═══════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS travenzotravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travenzotravel;

-- Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    gender ENUM('male','female','other') DEFAULT NULL,
    dob DATE DEFAULT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    zip VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Bookings
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    mondee_pnr VARCHAR(50),
    trip_type ENUM('oneway','roundtrip','multicity') DEFAULT 'oneway',
    origin_code VARCHAR(10) NOT NULL,
    origin_city VARCHAR(100),
    destination_code VARCHAR(10) NOT NULL,
    destination_city VARCHAR(100),
    departure_date DATE NOT NULL,
    return_date DATE,
    cabin_class ENUM('economy','premium_economy','business','first') DEFAULT 'economy',
    airline_name VARCHAR(100),
    airline_code VARCHAR(10),
    flight_number VARCHAR(20),
    departure_time VARCHAR(10),
    arrival_time VARCHAR(10),
    duration VARCHAR(20),
    stops INT DEFAULT 0,
    adults INT DEFAULT 1,
    children INT DEFAULT 0,
    infants INT DEFAULT 0,
    base_fare DECIMAL(10,2) NOT NULL,
    taxes DECIMAL(10,2) DEFAULT 0,
    service_fee DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending','confirmed','cancelled','refunded','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_ref (booking_ref),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Passengers
CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    type ENUM('adult','child','infant') DEFAULT 'adult',
    title VARCHAR(10),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dob DATE,
    gender ENUM('male','female','other'),
    passport_no VARCHAR(50),
    passport_expiry DATE,
    nationality VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Payments
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    transaction_id VARCHAR(100) UNIQUE,
    authnet_trans_id VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    card_last_four VARCHAR(4),
    card_type VARCHAR(20),
    status ENUM('pending','success','failed','refunded') DEFAULT 'pending',
    response_code VARCHAR(10),
    response_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trans (transaction_id)
) ENGINE=InnoDB;

-- Refunds
CREATE TABLE refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_id INT NOT NULL,
    user_id INT NOT NULL,
    refund_ref VARCHAR(30) UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    reason TEXT,
    status ENUM('requested','processing','completed','rejected') DEFAULT 'requested',
    authnet_refund_id VARCHAR(100),
    processed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (payment_id) REFERENCES payments(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Contact Messages
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    booking_ref VARCHAR(20),
    status ENUM('new','read','replied','closed') DEFAULT 'new',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Newsletter
CREATE TABLE newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Airports (for autocomplete)
CREATE TABLE airports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    iata VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    INDEX idx_iata (iata),
    INDEX idx_city (city)
) ENGINE=InnoDB;

-- Sample Airports Data
INSERT INTO airports (iata, name, city, country) VALUES
('DEL','Indira Gandhi International Airport','New Delhi','India'),
('BOM','Chhatrapati Shivaji Maharaj International Airport','Mumbai','India'),
('BLR','Kempegowda International Airport','Bangalore','India'),
('MAA','Chennai International Airport','Chennai','India'),
('CCU','Netaji Subhas Chandra Bose International Airport','Kolkata','India'),
('HYD','Rajiv Gandhi International Airport','Hyderabad','India'),
('GOI','Dabolim Airport','Goa','India'),
('AMD','Sardar Vallabhbhai Patel International Airport','Ahmedabad','India'),
('JFK','John F. Kennedy International Airport','New York','United States'),
('LAX','Los Angeles International Airport','Los Angeles','United States'),
('ORD','O\'Hare International Airport','Chicago','United States'),
('SFO','San Francisco International Airport','San Francisco','United States'),
('LHR','Heathrow Airport','London','United Kingdom'),
('DXB','Dubai International Airport','Dubai','United Arab Emirates'),
('SIN','Changi Airport','Singapore','Singapore'),
('BKK','Suvarnabhumi Airport','Bangkok','Thailand'),
('HKG','Hong Kong International Airport','Hong Kong','China'),
('NRT','Narita International Airport','Tokyo','Japan'),
('SYD','Sydney Kingsford Smith Airport','Sydney','Australia'),
('CDG','Charles de Gaulle Airport','Paris','France');
