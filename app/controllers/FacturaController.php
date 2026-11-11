
<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Moneda.php';
require_once __DIR__ . '/../models/Serie.php';
require_once __DIR__ . '/../models/Factura.php';

class FacturaController
{
    private PDO $db;
    private Cliente $cliente;
    private Moneda $moneda;
    private Serie $serie;
    private Factura $factura;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();

        $this->cliente = new Cliente($this->db);
        $this->moneda = new Moneda($this->db);
        $this->serie = new Serie($this->db);
        $this->factura = new Factura($this->db);
    }

    // Mostrar formulario
    public function crear(): void
    {
        $clientes = $this->cliente->obtenerTodos();
        $monedas = $this->moneda->obtenerTodas();
        $series = $this->serie->obtenerTodas();

        require __DIR__ . '/../views/facturas/crear.php';
    }

    
    // Mostrar listado con filtros
    public function index(): void
    {
        // Recuperar parámetros del formulario
        $clijid = filter_var(
            $_GET['clijid'] ?? '',
            FILTER_VALIDATE_INT
        );

        $monid = filter_var(
            $_GET['monid'] ?? '',
            FILTER_VALIDATE_INT
        );

        // Normalizar identificadores
        if ($clijid === false || $clijid <= 0) {
            $clijid = null;
        }

        if ($monid === false || $monid <= 0) {
            $monid = null;
        }

        // Validar formato de las fechas
        $validarFecha = static function ($fecha): ?string {
            if (
                !is_string($fecha) ||
                !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)
            ) {
                return null;
            }

            [$anio, $mes, $dia] = array_map('intval', explode('-', $fecha));

            return checkdate($mes, $dia, $anio) ? $fecha : null;
        };

        $fechaInicio = $validarFecha($_GET['fechaInicio'] ?? '');
        $fechaFin = $validarFecha($_GET['fechaFin'] ?? '');

        // Conservar los valores para mostrarlos en el formulario
        $filtroCliente = $clijid ?? '';
        $filtroMoneda = $monid ?? '';
        $filtroFechaInicio = $fechaInicio ?? '';
        $filtroFechaFin = $fechaFin ?? '';

        $errorFiltro = null;

        // Comprobar que el rango de fechas sea válido
        if (
            $fechaInicio !== null &&
            $fechaFin !== null &&
            $fechaInicio > $fechaFin
        ) {
            $errorFiltro = 'La fecha inicial no puede ser posterior a la fecha final.';

            $fechaInicio = null;
            $fechaFin = null;
        }

        // Recuperar opciones de los filtros
        $clientes = $this->cliente->obtenerTodos();
        $monedas = $this->moneda->obtenerTodas();

        // Consultar facturas aplicando los filtros
        $facturas = $this->factura->obtenerTodas(
            $clijid,
            $monid,
            $fechaInicio,
            $fechaFin
        );

        require __DIR__ . '/../views/facturas/listado.php';
    }


    // Registrar factura
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        try {
            $serid = filter_input(INPUT_POST, 'serid', FILTER_VALIDATE_INT);
            $clijid = filter_input(INPUT_POST, 'clijid', FILTER_VALIDATE_INT);
            $monid = filter_input(INPUT_POST, 'monid', FILTER_VALIDATE_INT);

            $fecha = $_POST['facfec'] ?? '';
            $conceptos = $_POST['conceptos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $valores = $_POST['valores'] ?? [];

            if (!$serid || !$clijid || !$monid) {
                throw new Exception('Selecciona cliente, moneda y serie.');
            }

            if (!is_array($conceptos) || !is_array($cantidades) || !is_array($valores)) {
                throw new Exception('Las partidas no tienen un formato válido.');
            }

            if (empty($fecha) || !strtotime($fecha)) {
                throw new Exception('La fecha no es válida.');
            }

            if (count($conceptos) === 0 || count($conceptos) !== count($cantidades) || count($conceptos) !== count($valores)) {
                throw new Exception('Debes registrar al menos una partida válida.');
            }

            $partidas = [];
            $subtotal = 0;

            foreach ($conceptos as $i => $concepto) {
                $concepto = trim($concepto);
                $cantidad = filter_var($cantidades[$i], FILTER_VALIDATE_FLOAT);
                $valor = filter_var($valores[$i], FILTER_VALIDATE_FLOAT);

                if ($concepto === '' || $cantidad === false || $valor === false || $cantidad <= 0 || $valor < 0) {
                    throw new Exception('Verifica los datos de las partidas.');
                }

                $importe = round($cantidad * $valor, 2);
                $subtotal += $importe;

                $partidas[] = [
                    'concepto' => $concepto,
                    'cantidad' => $cantidad,
                    'valor' => $valor
                ];
            }

            $subtotal = round($subtotal, 2);
            $iva = round($subtotal * 0.16, 2);
            $total = round($subtotal + $iva, 2);

            $this->db->beginTransaction();

            // Bloquear la serie para evitar folios duplicados simultáneos
            $stmt = $this->db->prepare(
                "SELECT serid FROM series WHERE serid = :serid FOR UPDATE"
            );
            $stmt->execute(['serid' => $serid]);

            if (!$stmt->fetch()) {
                throw new Exception('La serie seleccionada no existe.');
            }

            $stmt = $this->db->prepare(
                "SELECT COALESCE(MAX(facid), 0) + 1
                 FROM facturas WHERE serid = :serid"
            );
            $stmt->execute(['serid' => $serid]);
            $facid = (int) $stmt->fetchColumn();

            // Verificar cliente y moneda
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM clientes WHERE clijid = :id"
            );
            $stmt->execute(['id' => $clijid]);

            if (!$stmt->fetchColumn()) {
                throw new Exception('El cliente seleccionado no existe.');
            }

            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM monedas WHERE monid = :id"
            );
            $stmt->execute(['id' => $monid]);

            if (!$stmt->fetchColumn()) {
                throw new Exception('La moneda seleccionada no existe.');
            }

            // Insertar factura
            $sql = "INSERT INTO facturas
                    (serid, facid, clijid, monid, factc,
                     facfec, facsub, faciva, factot)
                    VALUES
                    (:serid, :facid, :clijid, :monid, 1,
                     :fecha, :subtotal, :iva, :total)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                'serid' => $serid,
                'facid' => $facid,
                'clijid' => $clijid,
                'monid' => $monid,
                'fecha' => $fecha,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total
            ]);

            // Insertar partidas
            $sqlDetalle = "INSERT INTO facturas_detalle
                           (serid, facid, facdetid,
                            facdetcan, facdetval, facdetcon)
                           VALUES
                           (:serid, :facid, :detid,
                            :cantidad, :valor, :concepto)";

            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($partidas as $i => $partida) {
                $stmtDetalle->execute([
                    'serid' => $serid,
                    'facid' => $facid,
                    'detid' => $i + 1,
                    'cantidad' => $partida['cantidad'],
                    'valor' => $partida['valor'],
                    'concepto' => $partida['concepto']
                ]);
            }

            $this->db->commit();

            header("Location: index.php?action=show&serid=$serid&facid=$facid&success=1");
            exit;
        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log($e->getMessage());

            $error = 'No fue posible registrar la factura. Verifica los datos e inténtalo nuevamente.';

            require __DIR__ . '/../views/facturas/crear.php';
        }
    }

    // Consultar factura
    public function mostrar(): void
    {
        $serid = filter_input(INPUT_GET, 'serid', FILTER_VALIDATE_INT);
        $facid = filter_input(INPUT_GET, 'facid', FILTER_VALIDATE_INT);

        if (!$serid || !$facid) {
            http_response_code(400);
            exit('Parámetros inválidos.');
        }

        $factura = $this->factura->obtenerPorId($serid, $facid);

        if (!$factura) {
            http_response_code(404);
            exit('Factura no encontrada.');
        }

        require __DIR__ . '/../views/facturas/detalle.php';
    }


    // Generar PDF de factura
    public function generarPdf(): void
    {
        $serid = filter_input(INPUT_GET, 'serid', FILTER_VALIDATE_INT);
        $facid = filter_input(INPUT_GET, 'facid', FILTER_VALIDATE_INT);

        if (!$serid || !$facid) {
            http_response_code(400);
            exit('Parámetros inválidos.');
        }

        // Recuperar información de la factura
        $factura = $this->factura->obtenerPorId($serid, $facid);

        if (!$factura) {
            http_response_code(404);
            exit('Factura no encontrada.');
        }

        // Cargar Dompdf
        require_once __DIR__ . '/../../vendor/autoload.php';

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);

        // Generar contenido HTML
        ob_start();

        require __DIR__ . '/../views/facturas/pdf.php';

        $html = ob_get_clean();

        // Convertir HTML a PDF
        $dompdf->loadHtml($html, 'UTF-8');

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        // Mostrar PDF en navegador
        $dompdf->stream(
            'Factura_' . $serid . '_' . $facid . '.pdf',
            ['Attachment' => false]
        );

        exit;
    }
}
