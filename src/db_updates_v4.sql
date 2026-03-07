-- db_updates_v4.sql
-- Admin Owner System: Plans, Activity Logs, User Status

-- Plans table (future monetization)
CREATE TABLE IF NOT EXISTS plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  max_clients INT DEFAULT 10,
  max_leads INT DEFAULT 100,
  max_integrations INT DEFAULT 1,
  price DECIMAL(10,2) DEFAULT 0,
  features JSON,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default plans
INSERT IGNORE INTO plans (id, name, max_clients, max_leads, max_integrations, price) VALUES
  (1, 'Free', 10, 100, 1, 0),
  (2, 'Pro', 50, 1000, 3, 97.00),
  (3, 'Enterprise', 999, 99999, 10, 297.00);

-- Add status + plan to usuarios
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS status ENUM('ativo','inativo','suspenso') DEFAULT 'ativo';
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS plan_id INT DEFAULT 1;

-- Activity logs (audit trail)
CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(100) NOT NULL,
  details TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (user_id),
  INDEX idx_action (action),
  INDEX idx_created (created_at)
);
