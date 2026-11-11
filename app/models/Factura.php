
<?php

class Factura
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    // Obtener facturas con filtros opcionales
    public function obtenerTodas(
        ?int $clijid = null,
        ?int $monid = null,
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ): array {

        $sql = "SELECT
                f.serid,
                f.facid,
                f.facfec,
                f.facsub,
                f.faciva,
                f.factot,
                c.clinom,
                m.moncve,
                s.sernom
            FROM facturas f
            INNER JOIN clientes c
                ON f.clijid = c.clijid
            INNER JOIN monedas m
                ON f.monid = m.monid
            INNER JOIN series s
                ON f.serid = s.serid
            WHERE 1 = 1";

        $parametros = [];

        // Filtrar por cliente
        if ($clijid !== null) {
            $sql .= " AND f.clijid = :clijid";
            $parametros['clijid'] = $clijid;
        }

        // Filtrar por moneda
        if ($monid !== null) {
            $sql .= " AND f.monid = :monid";
            $parametros['monid'] = $monid;
        }

        // Filtrar desde una fecha
        if ($fechaInicio !== null) {
            $sql .= " AND f.facfec >= :fechaInicio";
            $parametros['fechaInicio'] = $fechaInicio;
        }

        // Filtrar hasta una fecha
        if ($fechaFin !== null) {
            $sql .= " AND f.facfec <= :fechaFin";
            $parametros['fechaFin'] = $fechaFin;
        }

        $sql .= " ORDER BY f.facfec DESC, f.facid DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }


    // Obtener una factura y sus partidas
    public function obtenerPorId(
        int $serid,
        int $facid
    ): ?array {

        $sql = "SELECT
                    f.*,
                    c.clinom,
                    c.clirfc,
                    m.moncve,
                    m.monnom,
                    s.sernom
                FROM facturas f
                INNER JOIN clientes c
                    ON f.clijid = c.clijid
                INNER JOIN monedas m
                    ON f.monid = m.monid
                INNER JOIN series s
                    ON f.serid = s.serid
                WHERE f.serid = :serid
                AND f.facid = :facid";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'serid' => $serid,
            'facid' => $facid
        ]);

        $factura = $stmt->fetch();

        if (!$factura) {
            return null;
        }

        $sqlDetalle = "SELECT
                            facdetid,
                            facdetcan,
                            facdetval,
                            facdetcon
                       FROM facturas_detalle
                       WHERE serid = :serid
                       AND facid = :facid
                       ORDER BY facdetid ASC";

        $stmtDetalle = $this->db->prepare($sqlDetalle);

        $stmtDetalle->execute([
            'serid' => $serid,
            'facid' => $facid
        ]);

        $factura['partidas'] = $stmtDetalle->fetchAll();

        return $factura;
    }
}
