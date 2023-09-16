<?php

namespace Database;

class Service
{
    protected \PDO $connection;

    public function __construct()
    {
        $this->connection = Database::connect();
    }

    public function getAllServices()
    {
        $sql = "SELECT * FROM doctor_services";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function serviceExists($id)
    {
        $sql = "SELECT * FROM doctor_services WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);

        return (bool) $stmt->rowCount();
    }

}


?>