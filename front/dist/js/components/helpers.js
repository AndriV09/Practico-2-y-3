function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML.replace(/"/g, '&quot;');
}

function rutaImagen(imagen, raiz) {
    if (!imagen) {
        return raiz + 'dist/assets/sin-imagen.svg';
    }
    if (imagen.startsWith('http://') || imagen.startsWith('https://')) {
        return imagen;
    }
    return raiz + imagen;
}
