CREATE DATABASE IF NOT EXISTS bioskop_db;
USE bioskop_db;

DROP TABLE IF EXISTS reservation_seats;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS schedules;
DROP TABLE IF EXISTS films;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE films (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  duration INT,
  genre VARCHAR(100),
  poster VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  film_id INT NOT NULL,
  show_date DATE NOT NULL,
  show_time TIME NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  seats_total INT DEFAULT 100,
  seats_available INT DEFAULT 100,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_schedules_filmid
      FOREIGN KEY (film_id) REFERENCES films(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  schedule_id INT NOT NULL,
  seats INT NOT NULL,
  total_price DECIMAL(12,2) NOT NULL,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_resv_userid
      FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE SET NULL,
  CONSTRAINT fk_resv_scheduleid
      FOREIGN KEY (schedule_id) REFERENCES schedules(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reservation_seats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL,
  seat_code VARCHAR(10) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rs_reservation
      FOREIGN KEY (reservation_id) REFERENCES reservations(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;
