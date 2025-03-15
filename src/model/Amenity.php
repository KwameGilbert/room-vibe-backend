<?php

require_once __DIR__ . '/../config/Database.php';

class Amenity
{
    private $conn;
    private $table_name = "amenity";
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addHostelAmenity($hostel_id, array $amenity)
    {
        $query = "INSERT INTO " . $this->table_name . "(hostel_id, amenity_name) VALUES (:hostel_id, :amenity_name)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":hostel_id", $hostel_id, \PDO::PARAM_STR);
        $stmt->bindValue(":amenity_name", $amenity);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getHostelAmenity($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE hostel_id = :hostel_id;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":hostel_id", $id);
        $stmt->execute();
        $amenity = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $amenity;
    }

    public function updateHostelAmenity($id, $amenity)
    {
        $query = "UPDATE {$this->table_name} SET(hostel_id = :hostel_id, amenity_name = :amenity_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':hostel_id', $id);
        $stmt->bindvalue(':amenity_name', $amenity);

        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function deleteHostelAmenity($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    
}