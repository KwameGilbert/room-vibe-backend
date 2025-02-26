<?php

namespace App\Model;

use App\Config\Database;

class Hostel{
    private $conn;
    private $table_name = "hostel";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new hostel
    public function createHostel($hostel){
        $query = "INSERT INTO " . $this->table_name . " (name, description, location, distance, manager_id, school_id, views, rating, image) 
                  VALUES (:name, :description, :location, :distance, :manager_id, :school_id, :views, :rating, :image)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':name', $hostel['name']);
        $stmt->bindParam(':location', $hostel['location']);
        $stmt->bindParam(':description', $hostel['description']);
        $stmt->bindParam(':distance', $hostel['distance']);
        $stmt->bindParam(':manager_id', $hostel['manager_id']);
        $stmt->bindParam(':school_id', $hostel['school_id']);
        $stmt->bindParam(':views', $hostel['views']);
        $stmt->bindParam(':rating', $hostel['rating']);
        $stmt->bindParam(':image', $hostel['image']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Read all hostels
    public function getAllHostels()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $hostels = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $hostels;
    }

    // Read a single hostel by ID
    public function getHostelById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $hostel = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $hostel;
    }

    // Update a hostel
    public function updateHostel($id, $hostel)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, location = :location, distance = :distance, manager_id = :manager_id, school_id = :school_id, views = :views, price = :price, rating = :rating, image = :image
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $hostel['name']);
        $stmt->bindParam(':location', $hostel['location']);
        $stmt->bindParam(':description', $hostel['description']);
        $stmt->bindParam(':distance', $hostel['distance']);
        $stmt->bindParam(':manager_id', $hostel['manager_id']);
        $stmt->bindParam(':school_id', $hostel['school_id']);
        $stmt->bindParam(':views', $hostel['views']);
        $stmt->bindParam(':price', $hostel['price']);
        $stmt->bindParam(':rating', $hostel['rating']);
        $stmt->bindParam(':image', $hostel['image']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete a hostel
    public function deleteHostel($id)
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

    // Get all hostels of a manager
    public function getHostelsByManagerId($id)
    {
        $query = "SELECT h.*, m.name as manager_name, m.email as manager_email 
              FROM " . $this->table_name . " h 
              JOIN managers m ON h.manager_id = m.id 
              WHERE h.manager_id = :manager_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':manager_id', $id);
        $stmt->execute();

        $hostels = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $hostels;
    }

    // Get all hostels of a school
    public function getHostelsBySchoolId($id)
    {
        $query = "SELECT h.*, s.name as school_name, s.address as school_address 
              FROM " . $this->table_name . " h 
              JOIN schools s ON h.school_id = s.id 
              WHERE h.school_id = :school_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':school_id', $id);
        $stmt->execute();

        $hostels = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $hostels;
    }

    //Get all hostels by location
    public function getHostelsByLocation($location)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE location = :location";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':location', $location);
        $stmt->execute();

        $hostels = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $hostels;
    }

    // Get all hostels with price range 
    public function getHostelsByPriceRange($min, $max)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE min_price >= :min AND max_price <= :max";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':min', $min);
        $stmt->bindParam(':max', $max);
        $stmt->execute();

        $hostels = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $hostels;
    }

    // Get all hostels within a certain distance
    public function getHostelsByDistance($distance)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE distance <= :distance";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':distance', $distance);
        $stmt->execute();

        $hostels = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $hostels;
    }


}