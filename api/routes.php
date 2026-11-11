<?php

$router->get('/estado', 'EstadoController@ver');

$router->post('/login', 'AuthController@login');
$router->get('/sesion', 'AuthController@sesion');
$router->post('/logout', 'AuthController@logout');

$router->get('/articulos', 'ArticuloController@listar');
$router->get('/articulos/:id', 'ArticuloController@porId');
$router->get('/categorias', 'ArticuloController@categorias');

$router->post('/articulos', 'ArticuloController@crear');
$router->put('/articulos/:id', 'ArticuloController@actualizar');
$router->delete('/articulos/:id', 'ArticuloController@eliminar');
