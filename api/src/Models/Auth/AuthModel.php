<?php
namespace App\Models\Auth;

class AuthModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function buscarPorUsuario($usuario) {
        try {
            $stmt = $this->db->prepare(
                'SELECT u.id_usuario, u.usuario, u.password_hash, u.nombre, r.nombre_rol
                 FROM USUARIOS u
                 INNER JOIN ROLES r ON r.id_rol = u.id_rol
                 WHERE u.usuario = :usuario
                 LIMIT 1'
            );
            $stmt->execute(['usuario' => $usuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }
}
