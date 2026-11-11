<?php
namespace App\Controllers;

use App\Models\ArticuloModel;

class ArticuloController {
    private $db;

    public function __construct($db) {
        $this->db = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ---------- Rutas publicas (las usa la Landing Page) ----------

    // GET /api/articulos
    public function listar() {
        $articuloModel = new ArticuloModel($this->db);
        $articulos = $articuloModel->listar();

        if ($articulos === false) {
            $this->responder(500, 'error', 'No se pudieron obtener los artículos');
            return;
        }

        $this->responder(200, 'ok', 'Listado de artículos', $articulos);
    }

    // GET /api/articulos/:id
    public function porId($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->responder(400, 'error', 'El ID debe ser un número válido');
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $articulo = $articuloModel->obtenerPorId((int) $id);

        if (!$articulo) {
            $this->responder(404, 'error', 'Artículo no encontrado');
            return;
        }

        $this->responder(200, 'ok', 'Artículo encontrado', $articulo);
    }

    // GET /api/categorias
    public function categorias() {
        $articuloModel = new ArticuloModel($this->db);
        $categorias = $articuloModel->listarCategorias();

        if ($categorias === false) {
            $this->responder(500, 'error', 'No se pudieron obtener las categorías');
            return;
        }

        $this->responder(200, 'ok', 'Listado de categorías', $categorias);
    }

    // ---------- Rutas de administracion (solo rol Administrador) ----------

    // POST /api/articulos
    public function crear() {
        if (!$this->esAdmin()) {
            return;
        }

        $datos = $this->leerDatos();
        if ($datos === false) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $idAutor = $articuloModel->obtenerOCrearAutor($datos['autor']);

        if ($idAutor === false) {
            $this->responder(500, 'error', 'No se pudo guardar el autor');
            return;
        }

        $idNuevo = $articuloModel->crear(
            $datos['titulo'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['disponible'],
            $datos['id_categoria'],
            $idAutor,
            (int) $_SESSION['usuario_id']
        );

        if ($idNuevo === false) {
            $this->responder(500, 'error', 'No se pudo crear el artículo');
            return;
        }

        $this->responder(201, 'ok', 'Artículo creado', ['id_articulo' => $idNuevo]);
    }

    // PUT /api/articulos/:id
    public function actualizar($id) {
        if (!$this->esAdmin()) {
            return;
        }

        if (!is_numeric($id) || $id <= 0) {
            $this->responder(400, 'error', 'El ID debe ser un número válido');
            return;
        }

        $datos = $this->leerDatos();
        if ($datos === false) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);

        if (!$articuloModel->obtenerPorId((int) $id)) {
            $this->responder(404, 'error', 'Artículo no encontrado');
            return;
        }

        $idAutor = $articuloModel->obtenerOCrearAutor($datos['autor']);

        if ($idAutor === false) {
            $this->responder(500, 'error', 'No se pudo guardar el autor');
            return;
        }

        $resultado = $articuloModel->actualizar(
            (int) $id,
            $datos['titulo'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['disponible'],
            $datos['id_categoria'],
            $idAutor
        );

        if ($resultado === false) {
            $this->responder(500, 'error', 'No se pudo modificar el artículo');
            return;
        }

        $this->responder(200, 'ok', 'Artículo modificado');
    }

    // DELETE /api/articulos/:id
    public function eliminar($id) {
        if (!$this->esAdmin()) {
            return;
        }

        if (!is_numeric($id) || $id <= 0) {
            $this->responder(400, 'error', 'El ID debe ser un número válido');
            return;
        }

        $articuloModel = new ArticuloModel($this->db);

        if (!$articuloModel->eliminar((int) $id)) {
            $this->responder(404, 'error', 'Artículo no encontrado');
            return;
        }

        $this->responder(200, 'ok', 'Artículo eliminado');
    }

    // ---------- Funciones internas ----------

    // Seguridad: corta el pedido si no hay sesion o si el rol no es Administrador
    private function esAdmin() {
        if (empty($_SESSION['usuario_id'])) {
            $this->responder(401, 'error', 'Debe iniciar sesión');
            return false;
        }

        if ($_SESSION['rol'] !== 'Administrador') {
            $this->responder(403, 'error', 'No tiene permisos de administrador');
            return false;
        }

        return true;
    }

    // Lee y valida el JSON del formulario. Devuelve false si hay algun dato mal.
    private function leerDatos() {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }

        $titulo = trim((string) ($datos['titulo'] ?? ''));
        $autor = trim((string) ($datos['autor'] ?? ''));
        $descripcion = trim((string) ($datos['descripcion'] ?? ''));
        $imagen = trim((string) ($datos['imagen'] ?? ''));
        $idCategoria = $datos['id_categoria'] ?? '';
        $disponible = !empty($datos['disponible']) ? 1 : 0;

        if ($titulo === '' || $autor === '') {
            $this->responder(400, 'error', 'El título y el autor son obligatorios');
            return false;
        }

        if (!is_numeric($idCategoria) || $idCategoria <= 0) {
            $this->responder(400, 'error', 'Debe elegir una categoría');
            return false;
        }

        return [
            'titulo' => $titulo,
            'autor' => $autor,
            'descripcion' => $descripcion,
            'imagen' => $imagen,
            'id_categoria' => (int) $idCategoria,
            'disponible' => $disponible
        ];
    }

    private function responder($codigo, $status, $mensaje, $data = null) {
        http_response_code($codigo);
        echo json_encode([
            'status' => $status,
            'message' => $mensaje,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    }
}
