<?php

namespace App\Model;

use App\Config\Database;
use PDO;
use PDOException;

class Manager
{
    private $conn;
    private $table_name = "manager";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new manager
    public function createManager(array $manager): bool
    {
        $query = "INSERT INTO " . $this->table_name . " (name, email, phone) 
                  VALUES (:name, :email, :phone)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $manager['name'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':email', $manager['email'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':phone', $manager['phone'] ?? '', PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error message, e.g. $this->logger->error($e->getMessage());
            return false;
        }
    }

    // Get all managers
    public function getAllManagers()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single manager by ID
    public function getManagerById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get Manager of a hostel
    public function getManagerByHostelId($hostel_id)
    {
        $query = "SELECT m.* FROM " . $this->table_name . " m 
                  INNER JOIN hostel h ON h.manager_id = m.id 
                  WHERE h.id = :hostel_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    //Get manager by email
    public function getManagerByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Get manager by phone
    public function getManagerByPhone($phone)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE phone = :phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a manager
    public function updateManager($id, array $manager): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, email = :email, phone = :phone 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

    
        $stmt->bindValue(':name', $manager['name'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':email', $manager['email'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':phone', $manager['phone'] ?? '', PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log the error message
            return false;
        }
    }

    // Delete a manager
    public function deleteManager($id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log the error message
            return false;
        }
    }
}
