CREATE DATABASE IF NOT EXISTS firme_apoio 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE firme_apoio;

-- =====================================================
-- TABELA: usuario
-- Armazena as informações de Usuários
-- =====================================================
CREATE TABLE usuario (
    usuarioID INT AUTO_INCREMENT PRIMARY KEY,
    CPF VARCHAR(11) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    dtNascimento DATE NOT NULL,
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: voluntario
-- Armazena as informações de Voluntários
-- =====================================================
CREATE TABLE voluntario (
    voluntarioID INT AUTO_INCREMENT PRIMARY KEY,
    CPF VARCHAR(11) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    dtNascimento DATE NOT NULL,
    areaAtuacao VARCHAR(50) NOT NULL,
    instituicao VARCHAR(100) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE NOT NULL,
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: publicacao
-- Armazena as Publicações
-- =====================================================
CREATE TABLE publicacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    subtitulo VARCHAR(255) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    link VARCHAR(2048),
    texto TEXT NOT NULL,
    imagem_url VARCHAR(2048) DEFAULT NULL,
    dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_titulo (titulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: videos
-- Armazena as informações dos vídeos
-- =====================================================
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    link VARCHAR(2048) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_titulo (titulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: locaisAjuda
-- Armazena as informações de locais de apoio e ajuda
-- =====================================================
CREATE TABLE locaisAjuda (
    localID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    horario VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: frequencia
-- Armazena as frequências dos hábitos e exercícios
-- =====================================================
CREATE TABLE frequencia (
    frequenciaID INT AUTO_INCREMENT PRIMARY KEY,
    frequencia VARCHAR(30) NOT NULL UNIQUE,
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: habitos
-- Armazena os Hábitos dos usuários
-- =====================================================
CREATE TABLE habitos (
    habitoID INT AUTO_INCREMENT PRIMARY KEY,
    usuarioID INT NOT NULL,
    frequenciaID INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuarioID) REFERENCES usuario(usuarioID) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (frequenciaID) REFERENCES frequencia(frequenciaID) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_usuario (usuarioID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: exercicios
-- Armazena os exercícios dos usuários
-- =====================================================
CREATE TABLE exercicios (
    exercicioID INT AUTO_INCREMENT PRIMARY KEY,
    usuarioID INT NOT NULL,
    frequenciaID INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuarioID) REFERENCES usuario(usuarioID) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (frequenciaID) REFERENCES frequencia(frequenciaID) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_usuario (usuarioID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: contatosEmergencia
-- Armazena contatos de emergência do Usuário
-- =====================================================
CREATE TABLE contatosEmergencia (
    contatoID INT AUTO_INCREMENT,
    usuarioID INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    PRIMARY KEY (contatoID, usuarioID),
    FOREIGN KEY (usuarioID) REFERENCES usuario(usuarioID) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: acompanhamento
-- Armazena o usuário e um voluntário que o acompanhe
-- =====================================================
CREATE TABLE acompanhamento (
    usuarioID INT NOT NULL,
    voluntarioID INT NOT NULL,
    dataInicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (usuarioID, voluntarioID),
    FOREIGN KEY (usuarioID) REFERENCES usuario(usuarioID) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (voluntarioID) REFERENCES voluntario(voluntarioID) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: autoavaliacao
-- Armazena as informações da autoavaliação
-- =====================================================
CREATE TABLE autoavaliacao (
    avaliacaoID INT AUTO_INCREMENT PRIMARY KEY,
    usuarioID INT NOT NULL,
    dataRealizacao DATE NOT NULL,
    notaHumor INT NOT NULL CHECK (notaHumor BETWEEN 1 AND 5),
    perguntaUm TEXT NOT NULL,
    perguntaDois TEXT NOT NULL,
    perguntaTres TEXT NOT NULL,
    FOREIGN KEY (usuarioID) REFERENCES usuario(usuarioID) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uk_usuario_data (usuarioID, dataRealizacao),
    INDEX idx_usuario_data (usuarioID, dataRealizacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VIEWS ÚTEIS
-- =====================================================

-- View para listar usuários com seus hábitos
CREATE VIEW vw_usuarios_habitos AS
SELECT 
    u.usuarioID,
    u.nome AS usuario_nome,
    h.habitoID,
    h.nome AS habito_nome,
    f.frequencia
FROM usuario u
LEFT JOIN habitos h ON u.usuarioID = h.usuarioID
LEFT JOIN frequencia f ON h.frequenciaID = f.frequenciaID;

-- View para acompanhamento de voluntários
CREATE VIEW vw_acompanhamento_ativo AS
SELECT 
    v.voluntarioID,
    v.nome AS voluntario_nome,
    u.usuarioID,
    u.nome AS usuario_nome,
    a.dataInicio
FROM acompanhamento a
INNER JOIN voluntario v ON a.voluntarioID = v.voluntarioID
INNER JOIN usuario u ON a.usuarioID = u.usuarioID
WHERE a.ativo = TRUE;

-- =====================================================
-- PROCEDURES ÚTEIS
-- =====================================================

-- Procedure para criar um novo hábito
DELIMITER //
CREATE PROCEDURE sp_criar_habito(
    IN p_usuarioID INT,
    IN p_nome VARCHAR(30),
    IN p_frequenciaID INT
)
BEGIN
    INSERT INTO habitos (usuarioID, nome, frequenciaID)
    VALUES (p_usuarioID, p_nome, p_frequenciaID);
END //
DELIMITA ;

-- Procedure para registrar autoavaliação
DELIMITER //
CREATE PROCEDURE sp_registrar_autoavaliacao(
    IN p_usuarioID INT,
    IN p_notaHumor INT,
    IN p_perguntaUm TEXT,
    IN p_perguntaDois TEXT,
    IN p_perguntaTres TEXT
)
BEGIN
    INSERT INTO autoavaliacao (usuarioID, dataRealizacao, notaHumor, perguntaUm, perguntaDois, perguntaTres)
    VALUES (p_usuarioID, CURDATE(), p_notaHumor, p_perguntaUm, p_perguntaDois, p_perguntaTres);
END //
DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger para validar CPF antes de inserir usuário
DELIMITER //
CREATE TRIGGER tr_validar_cpf_usuario BEFORE INSERT ON usuario
FOR EACH ROW
BEGIN
    IF LENGTH(NEW.CPF) != 11 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'CPF deve conter 11 dígitos';
    END IF;
END //
DELIMITER ;

-- Trigger para validar CPF antes de inserir voluntário
DELIMITER //
CREATE TRIGGER tr_validar_cpf_voluntario BEFORE INSERT ON voluntario
FOR EACH ROW
BEGIN
    IF LENGTH(NEW.CPF) != 11 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'CPF deve conter 11 dígitos';
    END IF;
END //
DELIMITER ;