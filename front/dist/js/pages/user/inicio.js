document.addEventListener('DOMContentLoaded', async () => {
    const sesion = await API.request('/sesion');

    if (sesion.status !== 'ok') {
        window.location.href = '../login.html';
        return;
    }

    const nombre = sesion.data.nombre || sesion.data.usuario;
    document.getElementById('nombre-usuario').textContent = nombre + ' (' + sesion.data.rol + ')';
    document.getElementById('saludo-usuario').textContent = nombre;

    document.getElementById('btn-logout').addEventListener('click', async () => {
        await API.request('/logout', 'POST');
        window.location.href = '../login.html';
    });
});
