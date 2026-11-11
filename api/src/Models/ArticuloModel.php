<?php
namespace App\Models;

class ArticuloModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function contar() {
        try {
            $stmt = $this->db->query('SELECT COUNT(*) AS total FROM ARTICULOS');
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $fila['total'];
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listar() {
        try {
            $stmt = $this->db->query(
                'SELECT a.id_articulo, a.titulo, a.descripcion, a.imagen, a.disponible,
                        a.id_categoria, c.nombre_categoria, au.nombre_autor
                 FROM ARTICULOS a
                 INNER JOIN CATEGORIAS c ON c.id_categoria = a.id_categoria
                 INNER JOIN AUTORES au ON au.id_autor = a.id_autor
                 ORDER BY a.id_articulo'
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare(
                'SELECT a.id_articulo, a.titulo, a.descripcion, a.imagen, a.disponible,
                        a.id_categoria, c.nombre_categoria, au.nombre_autor
                 FROM ARTICULOS a
                 INNER JOIN CATEGORIAS c ON c.id_categoria = a.id_categoria
                 INNER JOIN AUTORES au ON au.id_autor = a.id_autor
                 WHERE a.id_articulo = :id'
            );
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listarCategorias() {
        try {
            $stmt = $this->db->query('SELECT id_categoria, nombre_categoria FROM CATEGORIAS ORDER BY id_categoria');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function obtenerOCrearAutor($nombreAutor) {
        try {
            $stmt = $this->db->prepare('SELECT id_autor FROM AUTORES WHERE nombre_autor = :nombre');
            $stmt->execute(['nombre' => $nombreAutor]);
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($fila) {
                return (int) $fila['id_autor'];
            }

            $stmt = $this->db->prepare('INSERT INTO AUTORES (nombre_autor) VALUES (:nombre)');
            $stmt->execute(['nombre' => $nombreAutor]);
            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function crear($titulo, $descripcion, $imagen, $disponible, $idCategoria, $idAutor, $idUsuario) {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO ARTICULOS (titulo, descripcion, imagen, disponible, id_categoria, id_autor, id_usuario)
                 VALUES (:titulo, :descripcion, :imagen, :disponible, :id_categoria, :id_autor, :id_usuario)'
            );
            $stmt->execute([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'imagen' => $imagen,
                'disponible' => $disponible,
                'id_categoria' => $idCategoria,
                'id_autor' => $idAutor,
                'id_usuario' => $idUsuario
            ]);
            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actualizar($id, $titulo, $descripcion, $imagen, $disponible, $idCategoria, $idAutor) {
        try {
            $stmt = $this->db->prepare(
                'UPDATE ARTICULOS
                 SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen,
                     disponible = :disponible, id_categoria = :id_categoria, id_autor = :id_autor
                 WHERE id_articulo = :id'
            );
            return $stmt->execute([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'imagen' => $imagen,
                'disponible' => $disponible,
                'id_categoria' => $idCategoria,
                'id_autor' => $idAutor,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->db->prepare('DELETE FROM ARTICULOS WHERE id_articulo = :id');
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
