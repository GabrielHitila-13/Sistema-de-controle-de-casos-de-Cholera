-- Fix pacientes table structure to prevent QueryException errors
-- Run this script to ensure all required fields have proper defaults or are nullable

-- First, check if the table exists and get its current structure
DESCRIBE pacientes;

-- Make endereco nullable if it's not already
ALTER TABLE pacientes MODIFY COLUMN endereco VARCHAR(500) NULL;

-- Make bi nullable if it's not already  
ALTER TABLE pacientes MODIFY COLUMN bi VARCHAR(255) NULL;

-- Make estabelecimento_id nullable if it's not already
ALTER TABLE pacientes MODIFY COLUMN estabelecimento_id BIGINT UNSIGNED NULL;

-- Add missing columns if they don't exist
ALTER TABLE pacientes 
ADD COLUMN IF NOT EXISTS idade INT NULL AFTER data_nascimento,
ADD COLUMN IF NOT EXISTS status ENUM('aguardando', 'em_atendimento', 'finalizado', 'transferido') DEFAULT 'aguardando' AFTER risco,
ADD COLUMN IF NOT EXISTS prioridade ENUM('baixa', 'media', 'alta', 'critica') DEFAULT 'media' AFTER status,
ADD COLUMN IF NOT EXISTS observacoes TEXT NULL AFTER sintomas,
ADD COLUMN IF NOT EXISTS diagnostico_colera ENUM('pendente', 'suspeito', 'provavel', 'confirmado', 'descartado') DEFAULT 'pendente' AFTER qr_code,
ADD COLUMN IF NOT EXISTS probabilidade_colera DECIMAL(5,2) NULL AFTER diagnostico_colera,
ADD COLUMN IF NOT EXISTS data_diagnostico TIMESTAMP NULL AFTER probabilidade_colera,
ADD COLUMN IF NOT EXISTS sintomas_colera JSON NULL AFTER data_diagnostico,
ADD COLUMN IF NOT EXISTS fatores_risco JSON NULL AFTER sintomas_colera,
ADD COLUMN IF NOT EXISTS recomendacoes TEXT NULL AFTER fatores_risco,
ADD COLUMN IF NOT EXISTS numero_caso VARCHAR(255) NULL AFTER recomendacoes,
ADD COLUMN IF NOT EXISTS contato_caso_confirmado BOOLEAN DEFAULT FALSE AFTER numero_caso,
ADD COLUMN IF NOT EXISTS area_surto BOOLEAN DEFAULT FALSE AFTER contato_caso_confirmado,
ADD COLUMN IF NOT EXISTS agua_contaminada BOOLEAN DEFAULT FALSE AFTER area_surto,
ADD COLUMN IF NOT EXISTS veiculo_id BIGINT UNSIGNED NULL AFTER agua_contaminada,
ADD COLUMN IF NOT EXISTS hospital_destino_id BIGINT UNSIGNED NULL AFTER veiculo_id,
ADD COLUMN IF NOT EXISTS ponto_atendimento_id BIGINT UNSIGNED NULL AFTER hospital_destino_id;

-- Add foreign key constraints if they don't exist
ALTER TABLE pacientes 
ADD CONSTRAINT IF NOT EXISTS fk_pacientes_estabelecimento 
    FOREIGN KEY (estabelecimento_id) REFERENCES estabelecimentos(id) ON DELETE SET NULL,
ADD CONSTRAINT IF NOT EXISTS fk_pacientes_veiculo 
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE SET NULL,
ADD CONSTRAINT IF NOT EXISTS fk_pacientes_hospital_destino 
    FOREIGN KEY (hospital_destino_id) REFERENCES estabelecimentos(id) ON DELETE SET NULL;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_pacientes_risco_status ON pacientes(risco, status);
CREATE INDEX IF NOT EXISTS idx_pacientes_diagnostico_colera ON pacientes(diagnostico_colera, data_diagnostico);
CREATE INDEX IF NOT EXISTS idx_pacientes_estabelecimento_created ON pacientes(estabelecimento_id, created_at);
CREATE INDEX IF NOT EXISTS idx_pacientes_data_triagem ON pacientes(data_triagem);
CREATE INDEX IF NOT EXISTS idx_pacientes_numero_caso ON pacientes(numero_caso);

-- Update existing records to have proper default values
UPDATE pacientes SET 
    endereco = COALESCE(endereco, ''),
    observacoes = COALESCE(observacoes, ''),
    status = COALESCE(status, 'aguardando'),
    prioridade = COALESCE(prioridade, 'media'),
    diagnostico_colera = COALESCE(diagnostico_colera, 'pendente'),
    contato_caso_confirmado = COALESCE(contato_caso_confirmado, FALSE),
    area_surto = COALESCE(area_surto, FALSE),
    agua_contaminada = COALESCE(agua_contaminada, FALSE)
WHERE endereco IS NULL 
   OR observacoes IS NULL 
   OR status IS NULL 
   OR prioridade IS NULL 
   OR diagnostico_colera IS NULL 
   OR contato_caso_confirmado IS NULL 
   OR area_surto IS NULL 
   OR agua_contaminada IS NULL;

-- Generate case numbers for existing patients without them
UPDATE pacientes 
SET numero_caso = CONCAT('CASO-', YEAR(created_at), '-', LPAD(id, 6, '0'))
WHERE numero_caso IS NULL OR numero_caso = '';

-- Calculate age for existing patients without age
UPDATE pacientes 
SET idade = TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE())
WHERE idade IS NULL AND data_nascimento IS NOT NULL;

-- Show final table structure
DESCRIBE pacientes;

-- Show count of records
SELECT COUNT(*) as total_pacientes FROM pacientes;

-- Show sample of updated records
SELECT id, nome, endereco, status, prioridade, diagnostico_colera, numero_caso, idade 
FROM pacientes 
ORDER BY created_at DESC 
LIMIT 5;
