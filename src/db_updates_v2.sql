-- Add new fields to Clientes
ALTER TABLE clientes ADD COLUMN instagram VARCHAR(255) DEFAULT NULL;
ALTER TABLE clientes ADD COLUMN landing_page_url VARCHAR(255) DEFAULT NULL;
ALTER TABLE clientes ADD COLUMN produto_servico TEXT DEFAULT NULL;
