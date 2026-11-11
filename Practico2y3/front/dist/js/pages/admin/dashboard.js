// Logica del Dashboard: protege la pagina y maneja agregar / modificar / eliminar articulos.

const RAIZ = '../../../';   // como llegar a front/ desde dist/pages/admin/
let articulos = [];         // ultima lista que devolvio la API
let modal = null;

document.addEventListener('DOMContentLoaded', async () => {
    // ---- 1. Seguridad: solo entra un Administrador con sesion iniciada ----
    const sesion = await API.request('/sesion');

    if (sesion.status !== 'ok') {
        window.location.href = '../login.html';
        return;
    }

    if (sesion.data.rol !== 'Administrador') {
        window.location.href = '../user/inicio.html';
        return;
    }

    // Recien ahora mostramos la pagina
    document.body.classList.remove('d-none');
    document.getElementById('nombre-usuario').textContent =
        (sesion.data.nombre || sesion.data.usuario) + ' (' + sesion.data.rol + ')';

    document.getElementById('btn-logout').addEventListener('click', async () => {
        await API.request('/logout', 'POST');
        window.location.href = '../login.html';
    });

    // ---- 2. Articulos ----
    modal = new bootstrap.Modal(document.getElementById('modal-articulo'));

    document.getElementById('btn-nuevo').addEventListener('click', abrirFormularioNuevo);
    document.getElementById('form-articulo').addEventListener('submit', guardarArticulo);
    document.getElementById('tabla-articulos').addEventListener('click', clickEnTabla);

    await cargarCategorias();
    await cargarArticulos();
});

async function cargarCategorias() {
    const respuesta = await API.request('/categorias');
    const select = document.getElementById('id_categoria');

    if (respuesta.status !== 'ok') {
        mostrarAlerta('No se pudieron cargar las categorías', 'danger');
        return;
    }

    select.innerHTML = respuesta.data.map(categoria =>
        `<option value="${categoria.id_categoria}">${escaparHtml(categoria.nombre_categoria)}</option>`
    ).join('');
}

async function cargarArticulos() {
    const tbody = document.getElementById('tabla-articulos');
    const respuesta = await API.request('/articulos');

    if (respuesta.status !== 'ok') {
        tbody.innerHTML = '<tr><td colspan="6" class="text-danger">No se pudieron cargar los artículos</td></tr>';
        return;
    }

    articulos = respuesta.data;

    if (articulos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Todavía no hay artículos cargados</td></tr>';
        return;
    }

    tbody.innerHTML = articulos.map(articulo => {
        const disponible = Number(articulo.disponible) === 1;

        return `
            <tr>
                <td>
                    <img src="${escaparHtml(rutaImagen(articulo.imagen, RAIZ))}" class="miniatura-articulo" alt=""
                         onerror="this.onerror=null; this.src='${RAIZ}dist/assets/sin-imagen.svg';">
                </td>
                <td class="fw-semibold">${escaparHtml(articulo.titulo)}</td>
                <td>${escaparHtml(articulo.nombre_autor)}</td>
                <td>${escaparHtml(articulo.nombre_categoria)}</td>
                <td>
                    <span class="badge ${disponible ? 'text-bg-success' : 'text-bg-secondary'}">
                        ${disponible ? 'Disponible' : 'No disponible'}
                    </span>
                </td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-outline-primary" data-accion="editar" data-id="${articulo.id_articulo}">Modificar</button>
                    <button class="btn btn-sm btn-outline-danger" data-accion="eliminar" data-id="${articulo.id_articulo}">Eliminar</button>
                </td>
            </tr>
        `;
    }).join('');
}

// Un solo "escuchador" para todos los botones de la tabla
function clickEnTabla(evento) {
    const boton = evento.target.closest('button[data-accion]');
    if (!boton) {
        return;
    }

    const id = Number(boton.dataset.id);

    if (boton.dataset.accion === 'editar') {
        abrirFormularioEditar(id);
    } else if (boton.dataset.accion === 'eliminar') {
        eliminarArticulo(id);
    }
}

function abrirFormularioNuevo() {
    document.getElementById('form-articulo').reset();
    document.getElementById('id_articulo').value = '';
    document.getElementById('titulo-modal').textContent = 'Agregar artículo';
    document.getElementById('alerta-form').classList.add('d-none');
    modal.show();
}

function abrirFormularioEditar(id) {
    const articulo = articulos.find(a => Number(a.id_articulo) === id);
    if (!articulo) {
        return;
    }

    document.getElementById('id_articulo').value = articulo.id_articulo;
    document.getElementById('titulo').value = articulo.titulo;
    document.getElementById('autor').value = articulo.nombre_autor;
    document.getElementById('id_categoria').value = articulo.id_categoria;
    document.getElementById('descripcion').value = articulo.descripcion || '';
    document.getElementById('imagen').value = articulo.imagen || '';
    document.getElementById('disponible').checked = Number(articulo.disponible) === 1;

    document.getElementById('titulo-modal').textContent = 'Modificar artículo';
    document.getElementById('alerta-form').classList.add('d-none');
    modal.show();
}

async function guardarArticulo(evento) {
    evento.preventDefault();

    const id = document.getElementById('id_articulo').value;
    const datos = {
        titulo: document.getElementById('titulo').value.trim(),
        autor: document.getElementById('autor').value.trim(),
        id_categoria: document.getElementById('id_categoria').value,
        descripcion: document.getElementById('descripcion').value.trim(),
        imagen: document.getElementById('imagen').value.trim(),
        disponible: document.getElementById('disponible').checked
    };

    // Si hay id es una modificacion (PUT); si no, es un articulo nuevo (POST)
    let respuesta;
    if (id) {
        respuesta = await API.request('/articulos/' + id, 'PUT', datos);
    } else {
        respuesta = await API.request('/articulos', 'POST', datos);
    }

    if (respuesta.status !== 'ok') {
        const alertaForm = document.getElementById('alerta-form');
        alertaForm.textContent = respuesta.message || 'No se pudo guardar el artículo';
        alertaForm.classList.remove('d-none');
        return;
    }

    modal.hide();
    mostrarAlerta(respuesta.message, 'success');
    await cargarArticulos();
}

async function eliminarArticulo(id) {
    const articulo = articulos.find(a => Number(a.id_articulo) === id);
    const titulo = articulo ? articulo.titulo : 'este artículo';

    if (!confirm('¿Seguro que querés eliminar "' + titulo + '"?')) {
        return;
    }

    const respuesta = await API.request('/articulos/' + id, 'DELETE');

    if (respuesta.status !== 'ok') {
        mostrarAlerta(respuesta.message || 'No se pudo eliminar el artículo', 'danger');
        return;
    }

    mostrarAlerta(respuesta.message, 'success');
    await cargarArticulos();
}

function mostrarAlerta(mensaje, tipo) {
    const alerta = document.getElementById('alerta-admin');
    alerta.className = 'alert alert-' + tipo;
    alerta.textContent = mensaje;
}
