-- Resetar senha do admin existente para 'admin123'
UPDATE usuarios 
SET senha = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE nivel = 'admin';

-- Criar novo usuário admin
INSERT INTO usuarios (nome, email, senha, nivel, status) 
VALUES (
    'Novo Admin',
    'novo.admin@empresa.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'ativo'
); 