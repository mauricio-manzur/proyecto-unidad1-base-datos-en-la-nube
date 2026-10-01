CREATE DATABASE IF NOT EXISTS repositorio_archivos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE repositorio_archivos;

-- 1. Usuarios
CREATE TABLE usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Carpetas (Estructura jerárquica con autorreferencia)
CREATE TABLE carpetas (
    id_carpeta INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_carpeta_padre INT UNSIGNED NULL,
    nombre VARCHAR(150) NOT NULL,
    eliminado TINYINT(1) NOT NULL DEFAULT 0,
    fecha_eliminacion TIMESTAMP NULL DEFAULT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_carpetas_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    CONSTRAINT fk_carpetas_padre
        FOREIGN KEY (id_carpeta_padre) REFERENCES carpetas(id_carpeta)
        ON DELETE RESTRICT,
    INDEX idx_carpetas_usuario_padre (id_usuario, id_carpeta_padre, eliminado)
) ENGINE=InnoDB;

-- 3. Archivos (Metadatos y puntero al objeto en Amazon S3)
CREATE TABLE archivos (
    id_archivo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_carpeta INT UNSIGNED NULL,
    nombre_original VARCHAR(255) NOT NULL,
    s3_key VARCHAR(500) NOT NULL UNIQUE,
    s3_bucket VARCHAR(100) NOT NULL,
    tamano_bytes BIGINT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    eliminado TINYINT(1) NOT NULL DEFAULT 0,
    fecha_eliminacion TIMESTAMP NULL DEFAULT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_archivos_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    CONSTRAINT fk_archivos_carpeta
        FOREIGN KEY (id_carpeta) REFERENCES carpetas(id_carpeta)
        ON DELETE RESTRICT,
    INDEX idx_archivos_usuario_carpeta (id_usuario, id_carpeta, eliminado)
) ENGINE=InnoDB;

-- 4. Archivos Compartidos (Relación N:M entre Archivos y Usuarios con permisos)
CREATE TABLE archivos_compartidos (
    id_comparticion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_archivo INT UNSIGNED NOT NULL,
    id_usuario_destino INT UNSIGNED NOT NULL,
    permiso ENUM('lectura', 'escritura') NOT NULL DEFAULT 'lectura',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_compartidos_archivo
        FOREIGN KEY (id_archivo) REFERENCES archivos(id_archivo)
        ON DELETE CASCADE,
    CONSTRAINT fk_compartidos_usuario
        FOREIGN KEY (id_usuario_destino) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    CONSTRAINT uq_archivo_usuario UNIQUE (id_archivo, id_usuario_destino),
    INDEX idx_compartidos_destino (id_usuario_destino)
) ENGINE=InnoDB;

-- 5. Enlaces Públicos (Tokens para acceso sin sesión)
CREATE TABLE enlaces_publicos (
    id_enlace INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_archivo INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_expiracion TIMESTAMP NULL DEFAULT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    revocado_en TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_enlaces_archivo
        FOREIGN KEY (id_archivo) REFERENCES archivos(id_archivo)
        ON DELETE CASCADE,
    INDEX idx_token_activo (token, activo)
) ENGINE=InnoDB;
