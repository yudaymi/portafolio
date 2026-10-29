-- Ejecutar una sola vez, seleccionando entre_paginas en phpMyAdmin.
USE entre_paginas;
CREATE TABLE IF NOT EXISTS preferencias_lectura (
 usuario_id BIGINT UNSIGNED NOT NULL,
 genero VARCHAR(60) NOT NULL,
 PRIMARY KEY (usuario_id,genero),
 CONSTRAINT fk_preferencias_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
