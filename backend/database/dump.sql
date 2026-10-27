DROP DATABASE IF EXISTS rul;
CREATE DATABASE rul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rul;

CREATE TABLE instructors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  last_name VARCHAR(50) NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  middle_name VARCHAR(50) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  middle_name VARCHAR(50) NOT NULL,
  birth_date DATE NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  instructor_id INT NULL COMMENT 'закреплённый инструктор курсанта',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(10) NOT NULL UNIQUE COMMENT 'обозначение категории: A, B, C',
  description VARCHAR(100) NOT NULL COMMENT 'какие транспортные средства'
) ENGINE=InnoDB;
CREATE TABLE slots (id INT AUTO_INCREMENT PRIMARY KEY, time_slot CHAR(5) NOT NULL UNIQUE) ENGINE=InnoDB;
CREATE TABLE transmissions (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(30) NOT NULL UNIQUE) ENGINE=InnoDB;
CREATE TABLE statuses (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(30) NOT NULL UNIQUE) ENGINE=InnoDB;

CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  instructor_id INT NULL,
  category_id INT NOT NULL,
  lesson_date DATE NOT NULL,
  slot_id INT NOT NULL,
  duration_minutes SMALLINT NOT NULL DEFAULT 90 CHECK (duration_minutes BETWEEN 30 AND 120),
  transmission_id INT NOT NULL,
  status_id INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_slot (lesson_date, slot_id, category_id, status_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE SET NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (slot_id) REFERENCES slots(id),
  FOREIGN KEY (transmission_id) REFERENCES transmissions(id),
  FOREIGN KEY (status_id) REFERENCES statuses(id)
) ENGINE=InnoDB;

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  application_id INT NOT NULL UNIQUE,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment VARCHAR(500) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO instructors (last_name, first_name, middle_name, phone) VALUES
 ('Смирнов','Алексей','Петрович','8(900)111-22-33'),
 ('Кузнецова','Марина','Игоревна','8(900)222-33-44'),
 ('Орлов','Дмитрий','Сергеевич','8(900)333-44-55');
INSERT INTO categories (name, description) VALUES ('A','Мотоциклы'),('B','Легковые автомобили'),('C','Грузовые автомобили');
INSERT INTO slots (time_slot) VALUES ('09:00'),('11:00'),('13:00'),('15:00'),('17:00');
INSERT INTO transmissions (name) VALUES ('Механическая'),('Автоматическая');
INSERT INTO statuses (id, name) VALUES (1,'Новая'),(2,'Подтверждена'),(3,'Занятие проведено'),(4,'Отменена');
