
<?php

class Serie
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function obtenerTodas(): array
    {
        $sql = "SELECT serid, sertpo, sernom
                FROM series
                ORDER BY sernom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
