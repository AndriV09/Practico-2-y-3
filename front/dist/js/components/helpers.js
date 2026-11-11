// Funciones chicas que se usan en mas de una pagina.

// Evita que un texto escrito por el administrador se interprete como HTML
function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML.replace(/"/g, '&quot;');
}

// Arma la ruta de la imagen de un articulo.
// "raiz" es como se llega a la carpeta front/ desde la pagina actual ('./' o '../../../').
function rutaImagen(imagen, raiz) {
    if (!imagen) {
        return raiz + 'dist/assets/sin-imagen.svg';
    }
    if (imagen.startsWith('http://') || imagen.startsWith('https://')) {
        return imagen;
    }
    return raiz + imagen;
}
