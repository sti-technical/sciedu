-- ====== UPDATE 1: Logo/Banner, Online exam, Student login ======
-- TiDB SQL Editor me poora paste karke ek baar Run karein.
-- (Dobara chalane se koi nuksan nahi, tables pehle se hon to skip ho jaate hain.)
USE institute_db;

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
