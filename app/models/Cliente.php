
<?php

class Cliente
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT clijid, clirfc, clinom
                FROM clientes
                ORDER BY clinom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT clijid, clirfc, clinom
                FROM clientes
                WHERE clijid = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }
}
