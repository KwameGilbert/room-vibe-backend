<?php

namespace App\Model;

use App\Config\Database;

class HostelImage
{
    private $conn;
    private $table_name = "hostel_images";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new hostel image record
    public function createHostelImage($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
            (hostel_id, public_id, url, created_at, updated_at)
            VALUES (:hostel_id, :public_id, :url, NOW(), NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $data['hostel_id']);
        $stmt->bindParam(':public_id', $data['public_id']);
        $stmt->bindParam(':url', $data['url']);
        
        try{
           return $stmt->execute();
        }catch(\PDOException $e){
            
            return false;
        }
    }

    // Fetch all images for a given hostel
    public function getImagesByHostelId($hostel_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE hostel_id = :hostel_id 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // fetch an image by ID
    public function getImageById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    // Update an image by ID
    public function updateHostelImage($id, $data)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET url = :url, updated_at = NOW() 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':url', $data['url']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // Delete an image by ID
    public function deleteImage($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
