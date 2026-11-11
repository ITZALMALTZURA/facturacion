
<?php

class Moneda
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function obtenerTodas(): array
    {
        $sql = "SELECT monid, moncve, monnom
                FROM monedas
                ORDER BY monnom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
