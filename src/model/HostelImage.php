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
    public function getImageByHostelId($hostel_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE hostel_id = :hostel_id 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
