CREATE TABLE IF NOT EXISTS config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Initial Keys (if they don't exist)
INSERT IGNORE INTO config (key_name, value) VALUES 
('meta_verify_token', ''),
('meta_page_access_token', ''),
('meta_page_id', ''),
('whatsapp_default_msg', 'Olá, vi seu interesse no nosso anúncio. Podemos conversar?');
