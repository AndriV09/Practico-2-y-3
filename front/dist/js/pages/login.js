// Logica del login: manda usuario y clave, y redirige segun el ROL que devuelve la API.

// Decide a donde va cada rol (las rutas son relativas a dist/pages/login.html)
function redirigirSegunRol(rol) {
    if (rol === 'Administrador') {
        window.location.href = './admin/dashboard.html';
    } else {
        window.location.href = './user/inicio.html';
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    // Si ya hay sesion iniciada no tiene sentido mostrar el login
    const sesion = await API.request('/sesion');
    if (sesion.status === 'ok') {
        redirigirSegunRol(sesion.data.rol);
        return;
    }

    const form = document.getElementById('form-login');
    const alerta = document.getElementById('alerta-login');

    form.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        alerta.classList.add('d-none');
        alerta.textContent = '';

        const usuario = document.getElementById('usuario').value.trim();
        const password = document.getElementById('password').value;

        const respuesta = await API.request('/login', 'POST', {
            usuario: usuario,
            password: password
        });

        if (respuesta.status === 'ok') {
            redirigirSegunRol(respuesta.data.rol);
            return;
        }

        alerta.textContent = respuesta.message || 'Usuario o contraseña incorrectos';
        alerta.classList.remove('d-none');
    });
});
