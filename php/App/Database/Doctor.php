<?php

namespace Database;

class Doctor
{
    protected \PDO $connection;

    public function __construct()
    {
        $this->connection = Database::connect();
    }

    public function getAllDoctors()
    {
        $sql = "SELECT * FROM doctors";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function doctorExists($id)
    {
        $sql = "SELECT COUNT(*) FROM doctors WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);

        $count = $stmt->fetchColumn();
        
        return ($count > 0);
    }

    public function getDoctor($id)
    {
        $sql = "SELECT * FROM doctors WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
}


?>