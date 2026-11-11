# Biblioteca Virtual — Prácticos 1, 2 y 3

Proyecto continuo: la Landing Page del Práctico 1 pasó a ser una aplicación con base de datos,
login con roles, panel de administración y catálogo dinámico.

Tecnologías: HTML5, CSS3, JavaScript, Bootstrap 5, PHP (API con MVC) y MySQL.

## Cómo levantarlo (Docker)

```bash
docker compose up -d --build
```

- Sitio: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (usuario `root`, clave `root_password`)
- Estado de la API: http://localhost:8080/api/estado

La base se crea sola con `api/config/init.sql` la primera vez. Si ya existía un volumen
de otro proyecto con el mismo nombre, hay que recrearlo: `docker compose down -v` y volver a levantar.

Los archivos `.env` no se suben al repositorio. Si no existen, el proyecto usa los mismos datos
del `docker-compose.yml`. Para personalizarlos: copiar `.env.example` a `.env` (raíz) y
`api/.env.example` a `api/.env`.

## Usuarios de prueba

| Usuario | Contraseña | Rol | A dónde lo lleva el login |
|---|---|---|---|
| `admin` | `admin` | Administrador | `front/dist/pages/admin/dashboard.html` |
| `usuario` | `usuario` | Usuario | `front/dist/pages/user/inicio.html` |

## Práctico 2 — dónde está cada punto

| Punto de la letra | Dónde verlo |
|---|---|
| MER | [`docs/base-de-datos.md`](docs/base-de-datos.md) (imagen `docs/mer.png`) |
| Pasaje a tablas | [`docs/base-de-datos.md`](docs/base-de-datos.md) |
| Glosario de datos | [`docs/base-de-datos.md`](docs/base-de-datos.md) |
| Base de datos implementada | `api/config/init.sql` |
| Login | `front/dist/pages/login.html`, `front/dist/js/pages/login.js`, `api/src/Controllers/AuthController.php`, `api/src/Models/Auth/AuthModel.php` |
| Roles y redirección | tablas `ROLES` y `USUARIOS`; función `redirigirSegunRol` en `login.js` |
| Dashboard del administrador | `front/dist/pages/admin/dashboard.html` |
| Protección del acceso | `front/dist/js/pages/admin/dashboard.js` (consulta `/api/sesion` y el rol) y función `esAdmin()` en `ArticuloController.php` |

## Práctico 3 — dónde está cada punto

| Punto de la letra | Dónde verlo |
|---|---|
| Agregar, modificar y eliminar artículos | Dashboard → `dashboard.js`, `ArticuloController.php`, `ArticuloModel.php` |
| Catálogo dinámico en la Landing | `front/index.html` (sección `#catalogo`) y `front/dist/js/pages/landing.js` |

Flujo para probar: entrar con `admin` → agregar, modificar o eliminar un artículo →
"Ver sitio público" → el catálogo muestra el cambio.

## Rutas de la API

| Método | Ruta | Para qué | Quién |
|---|---|---|---|
| POST | `/api/login` | Iniciar sesión | Todos |
| GET | `/api/sesion` | Saber quién está conectado y su rol | Todos |
| POST | `/api/logout` | Cerrar sesión | Todos |
| GET | `/api/articulos` | Listar artículos | Todos |
| GET | `/api/articulos/:id` | Ver un artículo | Todos |
| GET | `/api/categorias` | Listar categorías | Todos |
| POST | `/api/articulos` | Agregar artículo | Administrador |
| PUT | `/api/articulos/:id` | Modificar artículo | Administrador |
| DELETE | `/api/articulos/:id` | Eliminar artículo | Administrador |

## Estructura

```
api/
  config/        database.php, init.sql
  src/
    Controllers/ AuthController, ArticuloController, EstadoController
    Models/      ArticuloModel, Auth/AuthModel
    Utils/       Router, EnvLoader
  index.php, routes.php, autoload.php
front/
  index.html     Landing Page
  dist/
    assets/      imágenes
    css/         style.css, admin.css
    js/          api.js, components/, pages/
    pages/       login.html, admin/dashboard.html, user/inicio.html
docs/            MER, pasaje a tablas y glosario de datos
```
