<?php
// api/routes.php
// Acá se anota: "cuando pidan ESTA url, ejecutá ESTE controlador@método"
// Las URLs reales llevan /api adelante (ej: /api/login).

$router->get('/estado', 'EstadoController@ver');

// --- Login y sesión (Práctico 2) ---
$router->post('/login', 'AuthController@login');
$router->get('/sesion', 'AuthController@sesion');
$router->post('/logout', 'AuthController@logout');

// --- Catálogo público: lo usa la Landing Page (Práctico 3) ---
$router->get('/articulos', 'ArticuloController@listar');
$router->get('/articulos/:id', 'ArticuloController@porId');
$router->get('/categorias', 'ArticuloController@categorias');

// --- Administración de artículos: solo rol Administrador (Práctico 3) ---
$router->post('/articulos', 'ArticuloController@crear');
$router->put('/articulos/:id', 'ArticuloController@actualizar');
$router->delete('/articulos/:id', 'ArticuloController@eliminar');
