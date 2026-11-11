<?php
namespace App\Controllers;

// Importamos el molde del Modelo
use App\Models\ArticuloModel;

class EstadoController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // GET /api/estado -> sirve para comprobar que la API y la base responden
    public function ver() {
        // 1. Preparamos la respuesta base
        $respuesta = [
            'status' => 'ok',
            'message' => 'La API responde',
            'base_datos' => 'conectada',
            'tabla_articulos' => 'Falta crear la tabla ARTICULOS (phpMyAdmin o init.sql)',
            'cantidad_articulos' => 0
        ];

        // 2. Instanciamos el Modelo pasándole la conexión
        $articuloModel = new ArticuloModel($this->db);

        // 3. Le pedimos los datos al Modelo
        $cantidad = $articuloModel->contar();

        // 4. Lógica de negocio (Controlador evaluando qué pasó)
        if ($cantidad !== false) {
            $respuesta['tabla_articulos'] = 'ok';
            $respuesta['cantidad_articulos'] = $cantidad;
        }

        // 5. Entregamos el JSON
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }
}
