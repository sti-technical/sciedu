-- =====================================================
-- Computer Institute Management System - Database
-- Phase 1: Schema (MySQL / MariaDB)
-- =====================================================

CREATE DATABASE IF NOT EXISTS institute_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE institute_db;

-- 1. ADMIN USERS ---------------------------------------
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,      -- password_hash() se banega, plain password kabhi nahi
  role ENUM('admin','exam_admin') DEFAULT 'admin',
  last_login DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. COURSES -------------------------------------------
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('Computer Program','Vocational Program','Vocational Training') NOT NULL,
  name VARCHAR(150) NOT NULL,
  short_name VARCHAR(30),                   -- jaise ADCA
  duration VARCHAR(50),                     -- jaise 12 Months
  fee DECIMAL(10,2) DEFAULT 0,
  description TEXT,
  is_active TINYINT(1) DEFAULT 1
);

-- 3. BATCHES -------------------------------------------
CREATE TABLE batches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  timing VARCHAR(50) NOT NULL,              -- jaise 9am to 10am
  is_active TINYINT(1) DEFAULT 1
);

-- 4. STUDENTS ------------------------------------------
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_no VARCHAR(30) NOT NULL UNIQUE,       -- jaise SCI2024224
  name VARCHAR(120) NOT NULL,
  father_name VARCHAR(120),
  dob DATE,
  doj DATE,                                 -- date of joining
  gender ENUM('Male','Female','Other'),
  mobile VARCHAR(15),
  email VARCHAR(120),
  address TEXT,
  photo VARCHAR(255),                       -- file ka naam
  course_id INT,
  batch_id INT,
  password_hash VARCHAR(255),               -- student login ke liye
  status ENUM('Pending','Verified','Course Completed','Inactive') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id),
  FOREIGN KEY (batch_id) REFERENCES batches(id)
);

-- 5. FEES ----------------------------------------------
CREATE TABLE fees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_on DATE NOT NULL,
  payment_mode ENUM('Cash','UPI','Bank','Cheque') DEFAULT 'Cash',
  receipt_no VARCHAR(30),
  remark VARCHAR(255),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 6. ENQUIRIES (contact / enquiry form) ----------------
CREATE TABLE enquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  mobile VARCHAR(15) NOT NULL,
  email VARCHAR(120),
  course_interest VARCHAR(150),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. JOB APPLICATIONS ----------------------------------
CREATE TABLE job_applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  mobile VARCHAR(15) NOT NULL,
  email VARCHAR(120),
  position VARCHAR(100),
  resume VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. CERTIFICATES --------------------------------------
CREATE TABLE certificates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  certificate_no VARCHAR(40) NOT NULL UNIQUE,
  issue_date DATE NOT NULL,
  grade VARCHAR(10),
  percentage DECIMAL(5,2),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 9. ONLINE EXAM ---------------------------------------
CREATE TABLE exams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  duration_minutes INT NOT NULL DEFAULT 60,
  total_marks INT NOT NULL DEFAULT 100,
  pass_marks INT NOT NULL DEFAULT 40,
  exam_date DATE,
  is_active TINYINT(1) DEFAULT 1,
  FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  exam_id INT NOT NULL,
  question TEXT NOT NULL,
  option_a VARCHAR(255) NOT NULL,
  option_b VARCHAR(255) NOT NULL,
  option_c VARCHAR(255) NOT NULL,
  option_d VARCHAR(255) NOT NULL,
  correct_option ENUM('A','B','C','D') NOT NULL,
  marks INT DEFAULT 1,
  FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

CREATE TABLE exam_enrolls (                 -- Exam Enroll Form
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  exam_id INT NOT NULL,
  enrolled_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  admit_card_no VARCHAR(30),
  UNIQUE KEY one_enroll (student_id, exam_id),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

CREATE TABLE exam_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  exam_id INT NOT NULL,
  score INT NOT NULL,
  total INT NOT NULL,
  result ENUM('Pass','Fail') NOT NULL,
  taken_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- 10. WEBSITE CONTENT ----------------------------------
CREATE TABLE news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  body TEXT,
  posted_on DATE,
  is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('photo','video') NOT NULL,
  title VARCHAR(150),
  file_or_url VARCHAR(255) NOT NULL,        -- photo ka file, video ka YouTube link
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (                      -- institute name, phone, email, director message etc.
  setting_key VARCHAR(60) PRIMARY KEY,
  setting_value TEXT
);

-- 11. STARTER DATA -------------------------------------
INSERT INTO batches (timing) VALUES
 ('8am to 9am'),('9am to 10am'),('1 pm to 2 pm'),('2 pm to 3 pm');

INSERT INTO courses (category, name, short_name, duration, fee) VALUES
 ('Computer Program','Advance Diploma in Computer Application','ADCA','12 Months',6000),
 ('Computer Program','Diploma in Computer Application','DCA','6 Months',3500),
 ('Vocational Program','Tally with GST','TALLY','3 Months',3000),
 ('Vocational Training','Basic Computer Training','BCT','3 Months',1500);

INSERT INTO settings (setting_key, setting_value) VALUES
 ('institute_name','Your Institute Name'),
 ('phone',''),
 ('email',''),
 ('address','');

-- 12. UPDATE 1 TABLES (media, exam_attempts, login_fails) ------

-- Logo, banner, header ke chhote logos: database me save hote hain (Render restart par nahi mitte)
CREATE TABLE IF NOT EXISTS media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kind VARCHAR(20) NOT NULL,                -- logo / banner / strip / gallery
  filename VARCHAR(150),
  mime VARCHAR(50) NOT NULL,
  data_b64 MEDIUMTEXT NOT NULL,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_kind (kind)
);

-- Student ka exam shuru hone ka time aur save hue jawab
CREATE TABLE IF NOT EXISTS exam_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  exam_id INT NOT NULL,
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  finished_at DATETIME NULL,
  answers TEXT,
  UNIQUE KEY one_attempt (student_id, exam_id),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- Galat DOB/roll number se bar bar login try rokne ke liye
CREATE TABLE IF NOT EXISTS login_fails (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_no VARCHAR(30) NOT NULL,
  ip VARCHAR(45),
  tried_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_reg (reg_no, tried_at)
);

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
 ('whatsapp',''),('facebook',''),('instagram',''),('twitter',''),('linkedin',''),('youtube','');

-- Admin user baad me PHP se banayenge (password hash ke saath)
