<?php
namespace App\Controllers;

use App\Models\Auth\AuthModel;

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // POST /api/login
    public function login() {
        // 1. Leemos el JSON que mando api.js
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }

        $usuario = trim((string) ($datos['usuario'] ?? ''));
        $password = (string) ($datos['password'] ?? '');

        if ($usuario === '' || $password === '') {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Usuario y contraseña son obligatorios'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 2. Le pedimos el usuario al Modelo
        $authModel = new AuthModel($this->db);
        $fila = $authModel->buscarPorUsuario($usuario);

        // 3. Si no existe o el hash no coincide, no hay sesion
        if (!$fila || !password_verify($password, $fila['password_hash'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Usuario o contraseña incorrectos'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 4. Login correcto: guardamos la sesion, incluido el ROL
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $fila['id_usuario'];
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['nombre'] = $fila['nombre'];
        $_SESSION['rol'] = $fila['nombre_rol'];

        echo json_encode([
            'status' => 'ok',
            'message' => 'Login correcto',
            'data' => [
                'usuario' => $fila['usuario'],
                'nombre' => $fila['nombre'],
                'rol' => $fila['nombre_rol']
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    // GET /api/sesion  -> dice quien esta conectado y con que rol
    public function sesion() {
        if (empty($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'No hay sesión'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => [
                'usuario' => $_SESSION['usuario'],
                'nombre' => $_SESSION['nombre'],
                'rol' => $_SESSION['rol']
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    // POST /api/logout
    public function logout() {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        echo json_encode([
            'status' => 'ok',
            'message' => 'Sesión cerrada'
        ], JSON_UNESCAPED_UNICODE);
    }
}
