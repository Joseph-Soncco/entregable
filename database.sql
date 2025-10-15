
-- Crear la tabla con IDENTITY (auto-incremento)
CREATE TABLE animes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    genero VARCHAR(50),
    episodios INT,
    año INT,
    puntuacion DECIMAL(3,1)
);

-- Insertar datos de ejemplo
INSERT INTO animes (titulo, genero, episodios, año, puntuacion) VALUES 
('Attack on Titan', 'Acción', 75, 2013, 9.0),
('Demon Slayer', 'Acción', 44, 2019, 8.7),
('One Piece', 'Aventura', 1000, 1999, 9.1),
('Naruto', 'Acción', 720, 2002, 8.7),
('Death Note', 'Thriller', 37, 2006, 9.0);
