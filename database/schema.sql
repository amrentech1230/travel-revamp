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
-- India
('DEL','Indira Gandhi International Airport','New Delhi','India'),
('BOM','Chhatrapati Shivaji Maharaj International Airport','Mumbai','India'),
('BLR','Kempegowda International Airport','Bangalore','India'),
('MAA','Chennai International Airport','Chennai','India'),
('CCU','Netaji Subhas Chandra Bose International Airport','Kolkata','India'),
('HYD','Rajiv Gandhi International Airport','Hyderabad','India'),
('GOI','Dabolim Airport','Goa','India'),
('AMD','Sardar Vallabhbhai Patel International Airport','Ahmedabad','India'),
('PNQ','Pune Airport','Pune','India'),
('COK','Cochin International Airport','Kochi','India'),
('JAI','Jaipur International Airport','Jaipur','India'),
('GAU','Lokpriya Gopinath Bordoloi Airport','Guwahati','India'),
('LKO','Chaudhary Charan Singh Airport','Lucknow','India'),
('IXC','Chandigarh International Airport','Chandigarh','India'),
('TRV','Trivandrum International Airport','Thiruvananthapuram','India'),
('VNS','Lal Bahadur Shastri Airport','Varanasi','India'),
('PAT','Jay Prakash Narayan Airport','Patna','India'),
('SXR','Sheikh ul-Alam International Airport','Srinagar','India'),
('IXB','Bagdogra Airport','Siliguri','India'),
('BBI','Biju Patnaik International Airport','Bhubaneswar','India'),
-- United States
('JFK','John F. Kennedy International Airport','New York','United States'),
('LAX','Los Angeles International Airport','Los Angeles','United States'),
('ORD','O\'Hare International Airport','Chicago','United States'),
('SFO','San Francisco International Airport','San Francisco','United States'),
('ATL','Hartsfield-Jackson Atlanta International Airport','Atlanta','United States'),
('DFW','Dallas/Fort Worth International Airport','Dallas','United States'),
('MIA','Miami International Airport','Miami','United States'),
('BOS','Boston Logan International Airport','Boston','United States'),
('SEA','Seattle-Tacoma International Airport','Seattle','United States'),
('DEN','Denver International Airport','Denver','United States'),
('IAH','George Bush Intercontinental Airport','Houston','United States'),
('LAS','Harry Reid International Airport','Las Vegas','United States'),
('MCO','Orlando International Airport','Orlando','United States'),
('EWR','Newark Liberty International Airport','Newark','United States'),
('PHX','Phoenix Sky Harbor International Airport','Phoenix','United States'),
('IAD','Washington Dulles International Airport','Washington DC','United States'),
-- United Kingdom & Europe
('LHR','Heathrow Airport','London','United Kingdom'),
('LGW','Gatwick Airport','London','United Kingdom'),
('MAN','Manchester Airport','Manchester','United Kingdom'),
('EDI','Edinburgh Airport','Edinburgh','United Kingdom'),
('CDG','Charles de Gaulle Airport','Paris','France'),
('ORY','Paris Orly Airport','Paris','France'),
('FRA','Frankfurt Airport','Frankfurt','Germany'),
('MUC','Munich Airport','Munich','Germany'),
('AMS','Schiphol Airport','Amsterdam','Netherlands'),
('BCN','Barcelona-El Prat Airport','Barcelona','Spain'),
('MAD','Adolfo Suarez Madrid-Barajas Airport','Madrid','Spain'),
('FCO','Leonardo da Vinci Airport','Rome','Italy'),
('MXP','Milan Malpensa Airport','Milan','Italy'),
('ZRH','Zurich Airport','Zurich','Switzerland'),
('VIE','Vienna International Airport','Vienna','Austria'),
('CPH','Copenhagen Airport','Copenhagen','Denmark'),
('OSL','Oslo Gardermoen Airport','Oslo','Norway'),
('ARN','Stockholm Arlanda Airport','Stockholm','Sweden'),
('HEL','Helsinki-Vantaa Airport','Helsinki','Finland'),
('IST','Istanbul Airport','Istanbul','Turkey'),
('ATH','Athens International Airport','Athens','Greece'),
('LIS','Lisbon Portela Airport','Lisbon','Portugal'),
('DUB','Dublin Airport','Dublin','Ireland'),
('WAW','Warsaw Chopin Airport','Warsaw','Poland'),
('PRG','Vaclav Havel Airport','Prague','Czech Republic'),
('BUD','Budapest Ferenc Liszt Airport','Budapest','Hungary'),
-- Middle East
('DXB','Dubai International Airport','Dubai','United Arab Emirates'),
('AUH','Abu Dhabi International Airport','Abu Dhabi','United Arab Emirates'),
('DOH','Hamad International Airport','Doha','Qatar'),
('RUH','King Khalid International Airport','Riyadh','Saudi Arabia'),
('JED','King Abdulaziz International Airport','Jeddah','Saudi Arabia'),
('BAH','Bahrain International Airport','Manama','Bahrain'),
('MCT','Muscat International Airport','Muscat','Oman'),
('KWI','Kuwait International Airport','Kuwait City','Kuwait'),
('TLV','Ben Gurion Airport','Tel Aviv','Israel'),
('AMM','Queen Alia International Airport','Amman','Jordan'),
-- Asia Pacific
('SIN','Changi Airport','Singapore','Singapore'),
('BKK','Suvarnabhumi Airport','Bangkok','Thailand'),
('HKG','Hong Kong International Airport','Hong Kong','China'),
('NRT','Narita International Airport','Tokyo','Japan'),
('HND','Haneda Airport','Tokyo','Japan'),
('KIX','Kansai International Airport','Osaka','Japan'),
('ICN','Incheon International Airport','Seoul','South Korea'),
('PEK','Beijing Capital International Airport','Beijing','China'),
('PVG','Shanghai Pudong International Airport','Shanghai','China'),
('TPE','Taiwan Taoyuan International Airport','Taipei','Taiwan'),
('KUL','Kuala Lumpur International Airport','Kuala Lumpur','Malaysia'),
('CGK','Soekarno-Hatta International Airport','Jakarta','Indonesia'),
('DPS','Ngurah Rai International Airport','Bali','Indonesia'),
('MNL','Ninoy Aquino International Airport','Manila','Philippines'),
('HAN','Noi Bai International Airport','Hanoi','Vietnam'),
('SGN','Tan Son Nhat International Airport','Ho Chi Minh City','Vietnam'),
('RGN','Yangon International Airport','Yangon','Myanmar'),
('CMB','Bandaranaike International Airport','Colombo','Sri Lanka'),
('DAC','Hazrat Shahjalal International Airport','Dhaka','Bangladesh'),
('KTM','Tribhuvan International Airport','Kathmandu','Nepal'),
-- Australia & New Zealand
('SYD','Sydney Kingsford Smith Airport','Sydney','Australia'),
('MEL','Melbourne Tullamarine Airport','Melbourne','Australia'),
('BNE','Brisbane Airport','Brisbane','Australia'),
('PER','Perth Airport','Perth','Australia'),
('AKL','Auckland Airport','Auckland','New Zealand'),
('WLG','Wellington Airport','Wellington','New Zealand'),
-- Africa
('JNB','O.R. Tambo International Airport','Johannesburg','South Africa'),
('CPT','Cape Town International Airport','Cape Town','South Africa'),
('CAI','Cairo International Airport','Cairo','Egypt'),
('NBO','Jomo Kenyatta International Airport','Nairobi','Kenya'),
('ADD','Bole International Airport','Addis Ababa','Ethiopia'),
('LOS','Murtala Muhammed International Airport','Lagos','Nigeria'),
('CMN','Mohammed V International Airport','Casablanca','Morocco'),
-- Americas
('YYZ','Toronto Pearson International Airport','Toronto','Canada'),
('YVR','Vancouver International Airport','Vancouver','Canada'),
('YUL','Montreal-Trudeau International Airport','Montreal','Canada'),
('MEX','Mexico City International Airport','Mexico City','Mexico'),
('CUN','Cancun International Airport','Cancun','Mexico'),
('GRU','Guarulhos International Airport','Sao Paulo','Brazil'),
('GIG','Galeao International Airport','Rio de Janeiro','Brazil'),
('EZE','Ministro Pistarini International Airport','Buenos Aires','Argentina'),
('SCL','Arturo Merino Benitez International Airport','Santiago','Chile'),
('BOG','El Dorado International Airport','Bogota','Colombia'),
('LIM','Jorge Chavez International Airport','Lima','Peru'),
('PTY','Tocumen International Airport','Panama City','Panama'),
('SJO','Juan Santamaria International Airport','San Jose','Costa Rica'),
('HAV','Jose Marti International Airport','Havana','Cuba');
