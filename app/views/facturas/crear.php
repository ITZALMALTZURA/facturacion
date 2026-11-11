<?php

$pageTitle = 'Nueva factura';
$currentPage = 'crear';

$h = fn($value) => htmlspecialchars(
    (string) $value,
    ENT_QUOTES,
    'UTF-8'
);

require __DIR__ . '/../layouts/header.php';

?>

<!-- Encabezado -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

    <div>
        <h3 class="fw-bold mb-1">
            <i class="bi bi-file-earmark-plus text-primary me-2"></i>
            <span data-i18n="create_invoice.title">Nueva factura</span>
        </h3>

        <p class="text-muted mb-0"
            data-i18n="create_invoice.description">
            Registra una nueva factura y sus respectivas partidas.
        </p>
    </div>

    <a href="<?= $routerUrl ?>" class="btn btn-outline-secondary mt-3 mt-md-0">
        <i class="bi bi-arrow-left me-1"></i>
        <span data-i18n="common.back">Regresar</span>
    </a>

</div>

<form action="<?= $routerUrl ?>?action=guardar"
    method="POST"
    id="formFactura">

    <!-- DATOS GENERALES -->
    <div class="card mb-4">

        <div class="card-header">
            <i class="bi bi-info-circle text-primary me-2"></i>
            <span data-i18n="create_invoice.general_data">Datos generales</span>
        </div>

        <div class="card-body">

            <div class="row g-3">

                <!-- Cliente -->
                <div class="col-md-6 col-lg-4">

                    <label for="clijid" class="form-label">
                        <span data-i18n="common.client">Cliente</span>
                        <span class="text-danger">*</span>
                    </label>

                    <select name="clijid"
                        id="clijid"
                        class="form-select"
                        required>

                        <option value="" data-i18n="create_invoice.select_client">
                            Seleccionar cliente
                        </option>

                        <?php foreach ($clientes as $cliente): ?>

                            <option value="<?= $h($cliente['clijid']) ?>">
                                <?= $h($cliente['clinom']) ?>
                                (<?= $h($cliente['clirfc']) ?>)
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Moneda -->
                <div class="col-md-6 col-lg-4">

                    <label for="monid" class="form-label">
                        <span data-i18n="create_invoice.currency">Moneda</span>
                        <span class="text-danger">*</span>
                    </label>

                    <select name="monid"
                        id="monid"
                        class="form-select"
                        required>

                        <option value="" data-i18n="create_invoice.select_currency">
                            Seleccionar moneda
                        </option>

                        <?php foreach ($monedas as $moneda): ?>

                            <option value="<?= $h($moneda['monid']) ?>"
                                data-codigo="<?= $h($moneda['moncve']) ?>">

                                <?= $h($moneda['moncve']) ?>
                                - <?= $h($moneda['monnom']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Serie -->
                <div class="col-md-6 col-lg-4">

                    <label for="serid" class="form-label">
                        <span data-i18n="create_invoice.series">Serie</span>
                        <span class="text-danger">*</span>
                    </label>

                    <select name="serid"
                        id="serid"
                        class="form-select"
                        required>

                        <option value="" data-i18n="create_invoice.select_series">
                            Seleccionar serie
                        </option>

                        <?php foreach ($series as $serie): ?>

                            <option value="<?= $h($serie['serid']) ?>">
                                <?= $h($serie['sernom']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Fecha -->
                <div class="col-md-6 col-lg-4">

                    <label for="facfec" class="form-label">
                        <span data-i18n="create_invoice.issue_date">Fecha de emisión</span>
                        <span class="text-danger">*</span>
                    </label>

                    <input type="date"
                        name="facfec"
                        id="facfec"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        required>

                </div>

            </div>

        </div>
    </div>


    <!-- PARTIDAS -->
    <div class="card mb-4">

        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">

            <div>
                <i class="bi bi-list-check text-primary me-2"></i>
                <span data-i18n="create_invoice.items">Partidas de la factura</span>
            </div>

            <button type="button"
                id="agregarPartida"
                class="btn btn-sm btn-primary">

                <i class="bi bi-plus-lg me-1"></i>
                <span data-i18n="create_invoice.add_item">Agregar partida</span>

            </button>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>
                        <tr>
                            <th style="min-width: 220px;" data-i18n="create_invoice.concept">
                                Concepto
                            </th>
                            <th style="min-width: 110px;" data-i18n="create_invoice.quantity">
                                Cantidad
                            </th>
                            <th style="min-width: 150px;" data-i18n="create_invoice.unit_price">
                                Valor unitario
                            </th>
                            <th style="min-width: 150px;" data-i18n="create_invoice.amount">
                                Importe
                            </th>
                            <th style="width: 90px;" class="text-center"
                                data-i18n="common.actions">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody id="partidas">

                        <!-- Las partidas se agregarán mediante JavaScript -->

                    </tbody>

                </table>

            </div>

            <div class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                <span data-i18n="create_invoice.items_help">
                    Agrega los conceptos correspondientes a la factura.
                </span>
            </div>

        </div>
    </div>


    <!-- RESUMEN -->
    <div class="row justify-content-end mb-4">

        <div class="col-md-6 col-lg-4">

            <div class="card">

                <div class="card-header">
                    <i class="bi bi-calculator text-primary me-2"></i>
                    <span data-i18n="create_invoice.summary">Resumen de factura</span>
                </div>

                <div class="card-body">

                    <div class="total-box">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted" data-i18n="common.subtotal">Subtotal:</span>
                            <strong id="subtotal">$0.00</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted" data-i18n="common.tax">IVA (16%):</span>
                            <strong id="iva">$0.00</strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" data-i18n="common.total">Total:</span>
                            <span class="total-final" id="total">
                                $0.00
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- BOTONES -->
    <div class="d-flex justify-content-end gap-2 mb-4">

        <a href="<?= $routerUrl ?>" class="btn btn-outline-secondary"
            data-i18n="common.cancel">
            Cancelar
        </a>

        <button type="submit" class="btn btn-success px-4">

            <i class="bi bi-save me-1"></i>
            <span data-i18n="create_invoice.save">Guardar factura</span>

        </button>

    </div>

</form>

<script src="<?= $baseUrl ?>/js/factura.js"></script>

<?php require __DIR__ . '/../layouts/flash.php'; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>