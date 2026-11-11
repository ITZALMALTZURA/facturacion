
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formFactura');
    const tablaPartidas = document.getElementById('partidas');
    const btnAgregar = document.getElementById('agregarPartida');
    const selectorMoneda = document.getElementById('monid');

    const subtotalElemento = document.getElementById('subtotal');
    const ivaElemento = document.getElementById('iva');
    const totalElemento = document.getElementById('total');

    let confirmando = false;

    // Obtener traducciones
    function t(clave, respaldo) {
        return window.i18next?.isInitialized
            ? window.i18next.t(clave)
            : respaldo;
    }

    // Obtener idioma actual
    function obtenerIdioma() {
        return window.i18next?.isInitialized
            ? window.i18next.language
            : 'es';
    }

    // Formato de moneda
    function formatoMoneda(valor) {
        const opcion = selectorMoneda?.selectedOptions[0];
        const codigo = opcion?.dataset.codigo || 'MXN';

        const locale = obtenerIdioma() === 'en' ? 'en-US' : 'es-MX';

        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: codigo
        }).format(valor);
    }

    // Obtener los valores de las partidas
    function calcularTotales() {
        let subtotal = 0;

        tablaPartidas.querySelectorAll('tr').forEach(fila => {
            const cantidad = parseFloat(
                fila.querySelector('[name="cantidades[]"]').value
            ) || 0;

            const valor = parseFloat(
                fila.querySelector('[name="valores[]"]').value
            ) || 0;

            subtotal += cantidad * valor;

            fila.querySelector('.importe').textContent =
                formatoMoneda(cantidad * valor);
        });

        const iva = subtotal * 0.16;
        const total = subtotal + iva;

        subtotalElemento.textContent = formatoMoneda(subtotal);
        ivaElemento.textContent = formatoMoneda(iva);
        totalElemento.textContent = formatoMoneda(total);
    }

    // Agregar una partida
    function agregarPartida() {
        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td>
                <input type="text"
                    name="conceptos[]"
                    class="form-control"
                    maxlength="255"
                    placeholder="${t(
                        'create_invoice.item_placeholder',
                        'Descripción del producto o servicio'
                    )}"
                    required>
            </td>

            <td>
                <input type="number"
                    name="cantidades[]"
                    class="form-control"
                    min="0.01"
                    step="0.01"
                    value="1"
                    required>
            </td>

            <td>
                <input type="number"
                    name="valores[]"
                    class="form-control"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    required>
            </td>

            <td class="text-end">
                <span class="importe">${formatoMoneda(0)}</span>
            </td>

            <td class="text-center">
                <button type="button"
                    class="btn btn-outline-danger btn-sm eliminarPartida"
                    title="${t(
                        'create_invoice.delete_item',
                        'Eliminar partida'
                    )}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tablaPartidas.appendChild(fila);
        calcularTotales();
    }

    // Actualizar cálculos al modificar cantidades o valores
    tablaPartidas.addEventListener('input', event => {
        if (
            event.target.matches('[name="cantidades[]"]') ||
            event.target.matches('[name="valores[]"]')
        ) {
            const fila = event.target.closest('tr');

            const cantidad = parseFloat(
                fila.querySelector('[name="cantidades[]"]').value
            ) || 0;

            const valor = parseFloat(
                fila.querySelector('[name="valores[]"]').value
            ) || 0;

            fila.querySelector('.importe').textContent =
                formatoMoneda(cantidad * valor);

            calcularTotales();
        }
    });

    // Eliminar partida
    tablaPartidas.addEventListener('click', async event => {
        const boton = event.target.closest('.eliminarPartida');

        if (!boton) return;

        const resultado = await Swal.fire({
            title: t(
                'alerts.delete_item_title',
                '¿Eliminar partida?'
            ),
            text: t(
                'alerts.delete_item_text',
                'Esta partida será retirada de la factura.'
            ),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: t(
                'alerts.delete',
                'Sí, eliminar'
            ),
            cancelButtonText: t(
                'common.cancel',
                'Cancelar'
            ),
            confirmButtonColor: '#dc3545'
        });

        if (resultado.isConfirmed) {
            boton.closest('tr').remove();
            calcularTotales();
        }
    });

    // Botón agregar
    btnAgregar.addEventListener('click', agregarPartida);

    // Actualizar formato al cambiar moneda
    selectorMoneda.addEventListener('change', calcularTotales);

    // Actualizar elementos dinámicos cuando cambia el idioma
    document.addEventListener('idiomaCambiado', () => {

        tablaPartidas.querySelectorAll('tr').forEach(fila => {
            const concepto = fila.querySelector('[name="conceptos[]"]');
            const botonEliminar = fila.querySelector('.eliminarPartida');

            if (concepto) {
                concepto.placeholder = t(
                    'create_invoice.item_placeholder',
                    'Descripción del producto o servicio'
                );
            }

            if (botonEliminar) {
                botonEliminar.title = t(
                    'create_invoice.delete_item',
                    'Eliminar partida'
                );
            }
        });

        calcularTotales();
    });

    // Validar antes de enviar
    form.addEventListener('submit', async event => {
        if (confirmando) return;

        event.preventDefault();

        if (!form.reportValidity()) return;

        const filas = tablaPartidas.querySelectorAll('tr');

        if (filas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: t(
                    'alerts.no_items_title',
                    'Sin partidas'
                ),
                text: t(
                    'alerts.no_items_text',
                    'Debes agregar al menos un producto o servicio.'
                )
            });
            return;
        }

        for (const fila of filas) {
            const cantidad = parseFloat(
                fila.querySelector('[name="cantidades[]"]').value
            );

            const valor = parseFloat(
                fila.querySelector('[name="valores[]"]').value
            );

            const concepto = fila.querySelector('[name="conceptos[]"]').value.trim();

            if (!concepto || !Number.isFinite(cantidad) ||
                cantidad <= 0 || !Number.isFinite(valor) || valor < 0) {

                Swal.fire({
                    icon: 'error',
                    title: t(
                        'alerts.invalid_item_title',
                        'Partida inválida'
                    ),
                    text: t(
                        'alerts.invalid_item_text',
                        'Revisa la descripción, cantidad y valor de tus partidas.'
                    )
                });

                return;
            }
        }

        const resultado = await Swal.fire({
            title: t(
                'alerts.save_invoice_title',
                '¿Registrar factura?'
            ),
            text: t(
                'alerts.save_invoice_text',
                'Se guardará la factura con sus partidas correspondientes.'
            ),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: t(
                'alerts.confirm_register',
                'Sí, registrar'
            ),
            cancelButtonText: t(
                'alerts.review',
                'Revisar'
            ),
            confirmButtonColor: '#0d6efd'
        });

        if (resultado.isConfirmed) {
            confirmando = true;
            form.requestSubmit();
        }
    });

    // Iniciar con una partida vacía
    agregarPartida();
});
