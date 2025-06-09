-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_evaluaciones CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_evaluaciones;

-- Tabla de roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos iniciales de roles
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Evaluador', 'Gestión de tests y evaluaciones'),
('Usuario', 'Responder tests asignados');

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    rol_id INT NOT NULL,
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de zonas
CREATE TABLE zonas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero VARCHAR(10) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de centros de captación
CREATE TABLE centros (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    direccion TEXT NOT NULL,
    zona_id INT NOT NULL,
    responsable VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (zona_id) REFERENCES zonas(id)
);

-- Tabla de personas
CREATE TABLE personas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    documento VARCHAR(20) NOT NULL UNIQUE,
    fecha_nacimiento DATE,
    genero ENUM('M', 'F', 'O'),
    direccion TEXT,
    telefono VARCHAR(20),
    email VARCHAR(100),
    centro_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (centro_id) REFERENCES centros(id)
);

-- Tabla de tests
CREATE TABLE tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tiempo_limite INT, -- en minutos
    activo BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de preguntas
CREATE TABLE preguntas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT NOT NULL,
    texto TEXT NOT NULL,
    tipo ENUM('multiple', 'abierta', 'verdadero_falso') NOT NULL,
    orden INT NOT NULL,
    obligatoria BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id)
);

-- Tabla de opciones para preguntas de opción múltiple
CREATE TABLE opciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pregunta_id INT NOT NULL,
    texto TEXT NOT NULL,
    es_correcta BOOLEAN DEFAULT false,
    orden INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id)
);

-- Tabla de asignación de tests
CREATE TABLE asignaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    test_id INT NOT NULL,
    persona_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_limite DATE,
    estado ENUM('pendiente', 'en_proceso', 'completado', 'vencido') DEFAULT 'pendiente',
    asignado_por INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id),
    FOREIGN KEY (persona_id) REFERENCES personas(id),
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id)
);

-- Tabla de respuestas
CREATE TABLE respuestas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asignacion_id INT NOT NULL,
    pregunta_id INT NOT NULL,
    respuesta TEXT,
    opcion_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asignacion_id) REFERENCES asignaciones(id),
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id),
    FOREIGN KEY (opcion_id) REFERENCES opciones(id)
);

-- Usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (username, password, email, rol_id) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sistema.com', 1); 