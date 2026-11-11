<?php
$pageTitle = 'Detalle de factura';
$currentPage = '';

$h = fn($value) => htmlspecialchars(
    (string) $value,
    ENT_QUOTES,
    'UTF-8'
);

$simbolo = $factura['moncve'] === 'USD' ? 'US$' : '$';

$dinero = fn($valor) => $simbolo . ' ' .
    number_format((float) $valor, 2);

require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row
            justify-content-between align-items-md-center mb-4">

    <div>
        <h1 class="h3 fw-bold mb-1" data-i18n="invoice_detail.title">
            Detalle de factura
        </h1>
        <p class="text-muted mb-0" data-i18n="invoice_detail.description">
            Consulta la información del comprobante registrado.
        </p>
    </div>

    <div class="mt-3 mt-md-0 d-flex gap-2">

        <a href="<?= $routerUrl ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            <span data-i18n="common.back">Regresar</span>
        </a>

        <a href="<?= $routerUrl ?>?action=pdf&serid=<?= $h($factura['serid']) ?>&facid=<?= $h($factura['facid']) ?>"
            target="_blank"
            class="btn btn-danger">

            <i class="bi bi-file-earmark-pdf"></i>
            <span data-i18n="invoice_detail.generate_pdf">Generar PDF</span>

        </a>

    </div>

</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="row g-4">

            <div class="col-md-6">
                <h5 class="fw-bold text-primary mb-3"
                    data-i18n="invoice_detail.document_information">
                    Información del comprobante
                </h5>

                <p class="mb-2">
                    <strong data-i18n="invoice_detail.series">Serie:</strong>
                    <?= $h($factura['sernom']) ?>
                </p>

                <p class="mb-2">
                    <strong data-i18n="invoice_detail.number">Número:</strong>
                    <?= $h($factura['facid']) ?>
                </p>

                <p class="mb-2">
                    <strong data-i18n="invoice_detail.date">Fecha:</strong>
                    <?= $h($factura['facfec']) ?>
                </p>

                <p class="mb-0">
                    <strong data-i18n="invoice_detail.currency">Moneda:</strong>
                    <?= $h($factura['moncve']) ?>
                    - <?= $h($factura['monnom']) ?>
                </p>
            </div>

            <div class="col-md-6">
                <h5 class="fw-bold text-primary mb-3"
                    data-i18n="invoice_detail.client_information">
                    Información del cliente
                </h5>

                <p class="mb-2">
                    <strong data-i18n="invoice_detail.name">Nombre:</strong>
                    <?= $h($factura['clinom']) ?>
                </p>

                <p class="mb-0">
                    <strong>RFC:</strong>
                    <?= $h($factura['clirfc']) ?>
                </p>
            </div>

        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <h5 class="fw-bold mb-3" data-i18n="invoice_detail.items">
            Partidas de la factura
        </h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th data-i18n="create_invoice.concept">Concepto</th>
                        <th class="text-end" data-i18n="create_invoice.quantity">Cantidad</th>
                        <th class="text-end" data-i18n="create_invoice.unit_price">Valor unitario</th>
                        <th class="text-end" data-i18n="create_invoice.amount">Importe</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($factura['partidas'] as $partida): ?>

                        <?php
                        $importe = (float) $partida['facdetcan']
                            * (float) $partida['facdetval'];
                        ?>

                        <tr>
                            <td><?= $h($partida['facdetid']) ?></td>

                            <td>
                                <?= $h($partida['facdetcon']) ?>
                            </td>

                            <td class="text-end">
                                <?= number_format(
                                    (float) $partida['facdetcan'],
                                    2
                                ) ?>
                            </td>

                            <td class="text-end">
                                <?= $dinero($partida['facdetval']) ?>
                            </td>

                            <td class="text-end fw-semibold">
                                <?= $dinero($importe) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="row justify-content-end">
    <div class="col-md-5 col-lg-4">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted" data-i18n="common.subtotal">Subtotal</span>
                    <strong><?= $dinero($factura['facsub']) ?></strong>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted" data-i18n="common.tax">IVA (16%)</span>
                    <strong><?= $dinero($factura['faciva']) ?></strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold" data-i18n="common.total">Total</span>

                    <span class="fs-4 fw-bold text-primary">
                        <?= $dinero($factura['factot']) ?>
                    </span>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>