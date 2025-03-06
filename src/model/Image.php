<?php
class HostelImage
{
    private $conn;
    private $table_name = "hostel_images";

    public $id;
    public $hostel_id;
    public $public_id;
    public $url;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a new hostel image record
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
            (hostel_id, public_id, url, created_at, updated_at)
            VALUES (:hostel_id, :public_id, :url, NOW(), NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $data['hostel_id']);
        $stmt->bindParam(':public_id', $data['public_id']);
        $stmt->bindParam(':url', $data['url']);
        $stmt->execute();
        return $stmt;
    }

    // Fetch all images for a given hostel
    public function getByHostelId($hostel_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE hostel_id = :hostel_id 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete an image record
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }


}
