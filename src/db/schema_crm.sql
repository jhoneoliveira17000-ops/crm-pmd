-- Tabela de Estágios do Kanban
CREATE TABLE IF NOT EXISTS kanban_stages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    ordem INT NOT NULL DEFAULT 0,
    cor VARCHAR(20) DEFAULT '#cbd5e1', -- Slate-300 default
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Leads
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20),
    origem VARCHAR(50) DEFAULT 'Manual', -- Facebook, Site, Indicação
    valor_estimado DECIMAL(10, 2) DEFAULT 0.00,
    status_id INT,
    anotacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (status_id) REFERENCES kanban_stages(id) ON DELETE SET NULL
);

-- Histórico de Movimentação
CREATE TABLE IF NOT EXISTS lead_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    de_estagio_id INT,
    para_estagio_id INT,
    usuario_id INT, -- Quem moveu (opcional se não tiver login sempre)
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
);

-- Integração Facebook Leads (Log)
CREATE TABLE IF NOT EXISTS facebook_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT, -- Link com o lead criado
    facebook_lead_id VARCHAR(100),
    form_id VARCHAR(100),
    payload_json JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Initial Stages
INSERT INTO kanban_stages (nome, ordem, cor) VALUES 
('Novo Lead', 1, '#3b82f6'), -- Blue
('Contato Inicial', 2, '#eab308'), -- Yellow
('Negociação', 3, '#f97316'), -- Orange
('Fechado', 4, '#22c55e') -- Green
ON DUPLICATE KEY UPDATE nome=nome;
