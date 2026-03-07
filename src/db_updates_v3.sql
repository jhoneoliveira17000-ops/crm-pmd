-- db_updates_v3.sql
-- Altera colunas foto_perfil para MEDIUMTEXT para suportar imagens base64

ALTER TABLE users MODIFY COLUMN foto_perfil MEDIUMTEXT DEFAULT NULL;
ALTER TABLE usuarios MODIFY COLUMN foto_perfil MEDIUMTEXT DEFAULT NULL;
