// Logica de la Landing Page: trae los articulos de la API y arma las cards del catalogo.
document.addEventListener('DOMContentLoaded', async () => {
    const contenedor = document.getElementById('lista-articulos');
    const respuesta = await API.request('/articulos');

    if (respuesta.status !== 'ok') {
        contenedor.innerHTML = '<p class="text-danger">No se pudo cargar el catálogo. Intente más tarde.</p>';
        return;
    }

    if (respuesta.data.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">Todavía no hay artículos en el catálogo.</p>';
        return;
    }

    contenedor.innerHTML = respuesta.data.map(articulo => {
        const disponible = Number(articulo.disponible) === 1;

        return `
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm tarjeta-libro">
                    <img src="${escaparHtml(rutaImagen(articulo.imagen, './'))}"
                         class="card-img-top imagen-libro"
                         alt="${escaparHtml(articulo.titulo)}"
                         onerror="this.onerror=null; this.src='./dist/assets/sin-imagen.svg';">
                    <div class="card-body">
                        <span class="badge text-bg-light border mb-2">${escaparHtml(articulo.nombre_categoria)}</span>
                        <h3 class="h5 card-title fw-bold text-dark">${escaparHtml(articulo.titulo)}</h3>
                        <p class="card-text text-muted mb-2">${escaparHtml(articulo.nombre_autor)}</p>
                        <p class="card-text small texto-oscuro">${escaparHtml(articulo.descripcion)}</p>
                        <span class="badge ${disponible ? 'text-bg-success' : 'text-bg-secondary'}">
                            ${disponible ? 'Disponible' : 'No disponible'}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
});
