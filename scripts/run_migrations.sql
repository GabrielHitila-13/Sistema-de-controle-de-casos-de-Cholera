-- Adicionar colunas de diagnóstico de cólera à tabela pacientes
ALTER TABLE pacientes 
ADD COLUMN diagnostico_colera ENUM('suspeito', 'provavel', 'confirmado', 'descartado') NULL AFTER risco,
ADD COLUMN probabilidade_colera DECIMAL(5,2) NULL AFTER diagnostico_colera,
ADD COLUMN sintomas_colera JSON NULL AFTER probabilidade_colera,
ADD COLUMN fatores_risco JSON NULL AFTER sintomas_colera,
ADD COLUMN data_diagnostico TIMESTAMP NULL AFTER fatores_risco;

-- Adicionar colunas de estabelecimento aos veículos
ALTER TABLE veiculos 
ADD COLUMN estabelecimento_id BIGINT UNSIGNED NULL AFTER id,
ADD COLUMN modelo VARCHAR(255) NULL AFTER placa,
ADD COLUMN ano YEAR NULL AFTER modelo,
ADD COLUMN equipamentos JSON NULL AFTER ano,
ADD COLUMN latitude DECIMAL(10,8) NULL AFTER equipamentos,
ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude,
ADD COLUMN ultima_localizacao TIMESTAMP NULL AFTER longitude;

-- Adicionar foreign key para estabelecimento nos veículos
ALTER TABLE veiculos 
ADD CONSTRAINT veiculos_estabelecimento_id_foreign 
FOREIGN KEY (estabelecimento_id) REFERENCES estabelecimentos(id) ON DELETE SET NULL;

-- Adicionar colunas de condutor aos usuários
ALTER TABLE users 
ADD COLUMN veiculo_id BIGINT UNSIGNED NULL AFTER estabelecimento_id,
ADD COLUMN permissoes_especiais JSON NULL AFTER veiculo_id,
ADD COLUMN numero_licenca VARCHAR(255) NULL AFTER permissoes_especiais,
ADD COLUMN validade_licenca DATE NULL AFTER numero_licenca;

-- Adicionar foreign key para veículo nos usuários
ALTER TABLE users 
ADD CONSTRAINT users_veiculo_id_foreign 
FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE SET NULL;

-- Atualizar enum de papéis para incluir 'condutor'
ALTER TABLE users 
MODIFY COLUMN papel ENUM('administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro', 'condutor', 'visualizacao') 
NOT NULL DEFAULT 'visualizacao';

-- Inserir dados de exemplo para testar
INSERT INTO veiculos (placa, tipo, status, estabelecimento_id, modelo, ano, equipamentos) VALUES
('LD-001-AA', 'ambulancia', 'disponivel', 1, 'Mercedes Sprinter', 2020, '["desfibrilador", "oxigenio", "maca", "kit_emergencia"]'),
('LD-002-BB', 'ambulancia', 'em_atendimento', 1, 'Ford Transit', 2019, '["oxigenio", "maca", "kit_primeiros_socorros"]'),
('BG-003-CC', 'ambulancia', 'disponivel', 2, 'Volkswagen Crafter', 2021, '["desfibrilador", "oxigenio", "maca", "ventilador"]');

-- Atualizar alguns pacientes com diagnóstico de cólera para teste
UPDATE pacientes SET 
    diagnostico_colera = 'confirmado',
    probabilidade_colera = 85.5,
    sintomas_colera = '["diarreia aquosa", "vomito", "desidratacao"]',
    fatores_risco = '["area_surto", "agua_contaminada"]',
    data_diagnostico = NOW()
WHERE id <= 3;

UPDATE pacientes SET 
    diagnostico_colera = 'suspeito',
    probabilidade_colera = 45.2,
    sintomas_colera = '["diarreia", "nauseas"]',
    fatores_risco = '["contato_caso_confirmado"]',
    data_diagnostico = NOW()
WHERE id BETWEEN 4 AND 6;

UPDATE pacientes SET 
    diagnostico_colera = 'provavel',
    probabilidade_colera = 72.8,
    sintomas_colera = '["diarreia liquida", "vomito intenso", "sede intensa"]',
    fatores_risco = '["area_surto", "saneamento_precario"]',
    data_diagnostico = NOW()
WHERE id BETWEEN 7 AND 9;
