
document.addEventListener('DOMContentLoaded', async () => {

    const idiomaGuardado = localStorage.getItem('idioma') || 'es';

    // Ruta base de los recursos públicos
    const baseUrl = '/facturacion/public';

    // Cargar los archivos de traducción
    try {
        const [espanol, ingles] = await Promise.all([
            fetch(`${baseUrl}/locales/es/translation.json`).then(r => {
                if (!r.ok) throw new Error('No se pudo cargar español');
                return r.json();
            }),
            fetch(`${baseUrl}/locales/en/translation.json`).then(r => {
                if (!r.ok) throw new Error('No se pudo cargar inglés');
                return r.json();
            })
        ]);

        await i18next.init({
            lng: idiomaGuardado,
            fallbackLng: 'es',
            resources: {
                es: { translation: espanol },
                en: { translation: ingles }
            }
        });

        // Aplicar traducciones
        function traducirPagina() {
            document.documentElement.lang = i18next.language;

            document.querySelectorAll('[data-i18n]').forEach(elemento => {
                const clave = elemento.getAttribute('data-i18n');
                elemento.textContent = i18next.t(clave);
            });

            const botonIdioma = document.getElementById('btnIdioma');

            if (botonIdioma) {
                botonIdioma.textContent =
                    i18next.language === 'es' ? 'EN' : 'ES';
            }

            document.dispatchEvent(new Event('idiomaCambiado'));
        }

        traducirPagina();

        // Cambiar idioma
        const botonIdioma = document.getElementById('btnIdioma');

        if (botonIdioma) {
            botonIdioma.addEventListener('click', async () => {
                const nuevoIdioma =
                    i18next.language === 'es' ? 'en' : 'es';

                await i18next.changeLanguage(nuevoIdioma);

                localStorage.setItem('idioma', nuevoIdioma);

                traducirPagina();
            });
        }

    } catch (error) {
        console.error('Error al cargar las traducciones:', error);
    }
});
