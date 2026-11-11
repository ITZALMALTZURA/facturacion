
document.addEventListener('DOMContentLoaded', () => {

    const botonTema = document.getElementById('btnTema');
    const iconoTema = document.getElementById('iconoTema');
    const textoTema = document.getElementById('textoTema');

    document.addEventListener('idiomaCambiado', () => {
        const temaActual =
            document.documentElement.getAttribute('data-theme') || 'light';

        actualizarBoton(temaActual);
    });

    if (!botonTema) return;

    function actualizarBoton(tema) {
        const esOscuro = tema === 'dark';

        iconoTema.className = esOscuro
            ? 'bi bi-sun-fill'
            : 'bi bi-moon-stars-fill';

        const clave = esOscuro ? 'theme.light' : 'theme.dark';

        textoTema.textContent = window.i18next?.isInitialized
            ? window.i18next.t(clave)
            : (esOscuro ? 'Modo claro' : 'Modo oscuro');
    }

    function cambiarTema() {
        const temaActual =
            document.documentElement.getAttribute('data-theme') || 'light';

        const nuevoTema = temaActual === 'light' ? 'dark' : 'light';

        document.documentElement.setAttribute('data-theme', nuevoTema);
        localStorage.setItem('tema', nuevoTema);

        actualizarBoton(nuevoTema);
    }

    const temaInicial =
        document.documentElement.getAttribute('data-theme') || 'light';

    actualizarBoton(temaInicial);

    botonTema.addEventListener('click', cambiarTema);
});
