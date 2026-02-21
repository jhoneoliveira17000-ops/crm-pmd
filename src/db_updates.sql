-- Add Risk Status to Clientes
ALTER TABLE clientes ADD COLUMN status_risco ENUM('verde', 'amarelo', 'vermelho') DEFAULT 'verde';

-- Client Services (Platform & Service Type)
CREATE TABLE IF NOT EXISTS client_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    plataforma VARCHAR(100) NOT NULL, -- Meta Ads, Google Ads, etc.
    tipo_servico VARCHAR(100) NOT NULL, -- Trafego, Social Media, LP, Site
    status ENUM('ativo', 'pausado', 'cancelado') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Client Notes (Financial follow-up, general notes)
CREATE TABLE IF NOT EXISTS client_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    user_id INT NOT NULL, -- Who wrote the note
    conteudo TEXT NOT NULL,
    tipo ENUM('geral', 'financeiro', 'fechamento') DEFAULT 'geral',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    -- FOREIGN KEY (user_id) REFERENCES users(id) -- Optional if strict
);

-- Client Links (Drive is default, but allow others)
CREATE TABLE IF NOT EXISTS client_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Activity Logs (Timeline)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    user_id INT, -- Optional
    acao VARCHAR(255) NOT NULL, -- "Mudou status para Inativo", "Criou nota"
    detalhes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);
