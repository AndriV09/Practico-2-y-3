SET NAMES utf8mb4;

CREATE TABLE ROLES (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE USUARIOS (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES ROLES(id_rol)
);

CREATE TABLE CATEGORIAS (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE AUTORES (
    id_autor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_autor VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE ARTICULOS (
    id_articulo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    imagen VARCHAR(255) NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1, 
    id_categoria INT NOT NULL,
    id_autor INT NOT NULL,
    id_usuario INT NULL, 
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIAS(id_categoria),
    FOREIGN KEY (id_autor) REFERENCES AUTORES(id_autor),
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario)
);


INSERT INTO ROLES (id_rol, nombre_rol) VALUES
(1, 'Administrador'),
(2, 'Usuario');


INSERT INTO USUARIOS (usuario, password_hash, nombre, id_rol) VALUES
('admin', '$2y$10$kboXncU0gkVwvRTJJHLshu6foRW8aUECUPs5t/R0m1/KPJ422A5vC', 'Administrador', 1),
('usuario', '$2y$10$8LU3qfjKiz8WUKnsf.OHv.E6FzwVEIJGpjfzkkmlMKrUnof6KvddS', 'Usuario de prueba', 2);

INSERT INTO CATEGORIAS (id_categoria, nombre_categoria) VALUES
(1, 'Libro'),
(2, 'Revista'),
(3, 'Audiolibro');

INSERT INTO AUTORES (id_autor, nombre_autor) VALUES
(1, 'J.K. Rowling'),
(2, 'Antoine de Saint-Exupéry'),
(3, 'Gabriel García Márquez');

INSERT INTO ARTICULOS (titulo, descripcion, imagen, disponible, id_categoria, id_autor, id_usuario) VALUES
('Harry Potter y la piedra filosofal', 'Un niño descubre que es mago y comienza su primer año en Hogwarts.', 'dist/assets/harry-potter.webp', 1, 1, 1, 1),
('El Principito', 'Un piloto perdido en el desierto conoce a un pequeño príncipe de otro planeta.', 'dist/assets/el-principito.webp', 1, 1, 2, 1),
('Cien años de soledad', 'La historia de la familia Buendía a lo largo de siete generaciones en Macondo.', 'dist/assets/cien-anos-de-soledad.webp', 1, 1, 3, 1);
