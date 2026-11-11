<?php

function escaparPdf($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Identificar símbolo de moneda
$moneda = strtoupper($factura['moncve']);

$simbolo = match ($moneda) {
    'USD' => 'US$',
    'MXN' => '$',
    default => $moneda
};

// Formatear cantidades
function formatoMoneda($valor, $simbolo): string
{
    return $simbolo . ' ' .
        number_format((float) $valor, 2, '.', ',');
}

// Formatear fecha
try {
    $fecha = (new DateTimeImmutable($factura['facfec']))
        ->format('d/m/Y');
} catch (Exception $e) {
    $fecha = $factura['facfec'];
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .encabezado {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .titulo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 0;
        }

        .folio {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        .seccion {
            margin-bottom: 20px;
        }

        .seccion-titulo {
            background: #f1f5f9;
            padding: 8px;
            font-weight: bold;
            color: #1e293b;
            border-left: 3px solid #2563eb;
        }

        .datos {
            padding: 10px;
            line-height: 1.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #2563eb;
            color: white;
            padding: 10px 6px;
            text-align: left;
        }

        td {
            padding: 9px 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .derecha {
            text-align: right;
        }

        .centro {
            text-align: center;
        }

        .totales {
            width: 45%;
            margin-left: auto;
            margin-top: 20px;
        }

        .total-final {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
        }

        .pie {
            margin-top: 50px;
            text-align: center;
            color: #64748b;
            font-size: 9px;
        }


        /* Repetir encabezados de tabla en páginas siguientes */
        thead {
            display: table-header-group;
        }

        /* Evitar dividir una partida entre páginas */
        tr {
            page-break-inside: avoid;
        }

        /* Evitar dividir los encabezados */
        .seccion-titulo {
            page-break-inside: avoid;
        }

        /* Mantener juntos los totales */
        .totales {
            page-break-inside: avoid;
        }

        /* Evitar cortes dentro de las celdas */
        td,
        th {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <!-- Encabezado -->
    <div class="encabezado">

        <table>
            <tr>
                <td style="border: none;">
                    <h1 class="titulo">FACTURA</h1>
                    <p>Documento de facturación</p>
                </td>

                <td style="border: none;" class="folio">
                    <?= escaparPdf($factura['sernom']) ?>
                    -
                    <?= escaparPdf($factura['facid']) ?>
                </td>
            </tr>
        </table>

    </div>

    <!-- Información de factura -->
    <div class="seccion">

        <div class="seccion-titulo">
            INFORMACIÓN DE LA FACTURA
        </div>

        <div class="datos">

            <strong>Serie:</strong>
            <?= escaparPdf($factura['sernom']) ?>

            <br>

            <strong>Folio:</strong>
            <?= escaparPdf($factura['facid']) ?>

            <br>

            <strong>Fecha:</strong>
            <?= escaparPdf($fecha) ?>

            <br>

            <strong>Moneda:</strong>
            <?= escaparPdf($factura['monnom']) ?>
            (<?= escaparPdf($factura['moncve']) ?>)

        </div>

    </div>

    <!-- Información del cliente -->
    <div class="seccion">

        <div class="seccion-titulo">
            DATOS DEL CLIENTE
        </div>

        <div class="datos">

            <strong>Nombre:</strong>
            <?= escaparPdf($factura['clinom']) ?>

            <br>

            <strong>RFC:</strong>
            <?= escaparPdf($factura['clirfc']) ?>

        </div>

    </div>

    <!-- Partidas -->
    <div class="seccion">

        <div class="seccion-titulo">
            DETALLE DE CONCEPTOS
        </div>

        <table>

            <thead>
                <tr>
                    <th class="centro">#</th>
                    <th>Concepto</th>
                    <th class="centro">Cantidad</th>
                    <th class="derecha">Valor unitario</th>
                    <th class="derecha">Importe</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($factura['partidas'] as $partida): ?>

                    <tr>

                        <td class="centro">
                            <?= escaparPdf($partida['facdetid']) ?>
                        </td>

                        <td>
                            <?= escaparPdf($partida['facdetcon']) ?>
                        </td>

                        <td class="centro">
                            <?= escaparPdf($partida['facdetcan']) ?>
                        </td>

                        <td class="derecha">
                            <?= formatoMoneda($partida['facdetval'], $simbolo) ?>
                        </td>

                        <td class="derecha">
                            <?= formatoMoneda(
                                $partida['facdetcan'] *
                                    $partida['facdetval'], $simbolo
                            ) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <!-- Totales -->
    <table class="totales">

        <tr>
            <td>Subtotal:</td>
            <td class="derecha">
                <?= formatoMoneda($factura['facsub'], $simbolo) ?>
            </td>
        </tr>

        <tr>
            <td>IVA (16%):</td>
            <td class="derecha">
                <?= formatoMoneda($factura['faciva'], $simbolo) ?>
            </td>
        </tr>

        <tr class="total-final">
            <td>TOTAL:</td>
            <td class="derecha">
                <?= formatoMoneda($factura['factot'], $simbolo) ?>
            </td>
        </tr>

    </table>

    <!-- Pie -->
    <div class="pie">
        Documento generado electrónicamente.
    </div>

</body>

</html>