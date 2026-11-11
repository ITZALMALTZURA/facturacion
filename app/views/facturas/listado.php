<?php
$pageTitle = 'Listado de facturas';
$currentPage = 'listado';

$h = fn($value) => htmlspecialchars(
    (string) $value,
    ENT_QUOTES,
    'UTF-8'
);

require __DIR__ . '/../layouts/header.php';
?>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <!-- Título -->
        <h5 class="fw-bold mb-3">
            <i class="bi bi-funnel me-2"></i>
            <span data-i18n="invoices.filter_title">
                Filtrar facturas
            </span>
        </h5>

        <!-- Mensaje de error -->
        <?php if (!empty($errorFiltro)): ?>
            <div class="alert alert-warning" role="alert">
                <span data-i18n="invoices.invalid_date_range">
                    La fecha inicial no puede ser posterior a la fecha final.
                </span>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="<?= $routerUrl ?>" method="GET">

            <div class="row g-3">

                <!-- Filtro por cliente -->
                <div class="col-md-6 col-lg-3">

                    <label for="clijid"
                        class="form-label"
                        data-i18n="invoices.filter_client">
                        Cliente
                    </label>

                    <select
                        name="clijid"
                        id="clijid"
                        class="form-select">

                        <option
                            value=""
                            data-i18n="invoices.all_clients">
                            Todos los clientes
                        </option>

                        <?php foreach ($clientes as $cliente): ?>
                            <option
                                value="<?= $h($cliente['clijid']) ?>"
                                <?= (string) $filtroCliente === (string) $cliente['clijid'] ? 'selected' : '' ?>>

                                <?= $h($cliente['clinom']) ?>

                            </option>
                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Filtro por moneda -->
                <div class="col-md-6 col-lg-3">

                    <label for="monid"
                        class="form-label"
                        data-i18n="invoices.filter_currency">
                        Moneda
                    </label>

                    <select
                        name="monid"
                        id="monid"
                        class="form-select">

                        <option
                            value=""
                            data-i18n="invoices.all_currencies">
                            Todas las monedas
                        </option>

                        <?php foreach ($monedas as $moneda): ?>
                            <option
                                value="<?= $h($moneda['monid']) ?>"
                                <?= (string) $filtroMoneda === (string) $moneda['monid'] ? 'selected' : '' ?>>

                                <?= $h($moneda['moncve']) ?> -
                                <?= $h($moneda['monnom']) ?>

                            </option>
                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Fecha inicial -->
                <div class="col-md-6 col-lg-3">

                    <label for="fechaInicio"
                        class="form-label"
                        data-i18n="invoices.start_date">
                        Fecha inicial
                    </label>

                    <input
                        type="date"
                        name="fechaInicio"
                        id="fechaInicio"
                        class="form-control"
                        value="<?= $h($filtroFechaInicio) ?>">

                </div>

                <!-- Fecha final -->
                <div class="col-md-6 col-lg-3">

                    <label for="fechaFin"
                        class="form-label"
                        data-i18n="invoices.end_date">
                        Fecha final
                    </label>

                    <input
                        type="date"
                        name="fechaFin"
                        id="fechaFin"
                        class="form-control"
                        value="<?= $h($filtroFechaFin) ?>">

                </div>

            </div>

            <!-- Botones -->
            <div class="d-flex flex-wrap gap-2 mt-4">

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-search me-1"></i>

                    <span data-i18n="invoices.filter_button">
                        Filtrar
                    </span>

                </button>

                <a
                    href="<?= $routerUrl ?>"
                    class="btn btn-outline-secondary">

                    <i class="bi bi-x-circle me-1"></i>

                    <span data-i18n="invoices.clear_filters">
                        Limpiar filtros
                    </span>

                </a>

            </div>

        </form>

    </div>
</div>


<div class="d-flex flex-column flex-md-row
            justify-content-between align-items-md-center mb-4">

    <div>
        <h1 class="h3 fw-bold mb-1" data-i18n="invoices.title">
            Facturas registradas
        </h1>

        <p class="text-muted mb-0"
            data-i18n="invoices.description">
            Consulta y administra los comprobantes registrados.
        </p>
    </div>

    <a href="<?= $routerUrl ?>?action=crear"
        class="btn btn-primary mt-3 mt-md-0">
        <i class="bi bi-plus-lg"></i>
        <span data-i18n="invoices.new_invoice">Nueva factura</span>
    </a>

</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between
                    align-items-center mb-3">

            <h5 class="fw-bold mb-0"
                data-i18n="invoices.history">
                Historial de facturas
            </h5>

            <span class="badge text-bg-primary">
                <?= count($facturas) ?>
                <span data-i18n="invoices.records">registros</span>
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th data-i18n="invoices.folio">Folio</th>
                        <th data-i18n="invoices.client">Cliente</th>
                        <th data-i18n="invoices.date">Fecha</th>
                        <th data-i18n="invoices.currency">Moneda</th>
                        <th class="text-end" data-i18n="invoices.total">
                            Total
                        </th>
                        <th class="text-center"
                            data-i18n="invoices.actions">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($facturas)): ?>

                        <tr>
                            <td colspan="6"
                                class="text-center text-muted py-4"
                                data-i18n="invoices.empty">
                                No hay facturas registradas.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($facturas as $factura): ?>

                            <?php
                            $simbolo = $factura['moncve'] === 'USD'
                                ? 'US$'
                                : '$';
                            ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= $h($factura['serid']) ?>-<?= $h($factura['facid']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        <?= $h($factura['clinom']) ?>
                                    </div>
                                </td>

                                <td>
                                    <?= $h($factura['facfec']) ?>
                                </td>

                                <td>
                                    <span class="badge text-bg-light border">
                                        <?= $h($factura['moncve']) ?>
                                    </span>
                                </td>

                                <td class="text-end fw-semibold">
                                    <?= $simbolo ?>
                                    <?= number_format(
                                        (float) $factura['factot'],
                                        2
                                    ) ?>
                                </td>

                                <td class="text-center">
                                    <a href="<?= $routerUrl ?>?action=show&serid=<?= urlencode((string)$factura['serid']) ?>&facid=<?= urlencode((string)$factura['facid']) ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                        <span data-i18n="invoices.view_detail">
                                            Ver detalle
                                        </span>
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>