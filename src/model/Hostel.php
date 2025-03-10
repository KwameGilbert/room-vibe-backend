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

    public function createHostel($manager_id, array $hostel): bool
    {
        // The SQL query now matches the new schema for the hostel table.
        $query = "INSERT INTO " . $this->table_name . " (
                    hostel_name, 
                    location, 
                    distance, 
                    school_id, 
                    manager_id,
                    rating, 
                    description, 
                    address, 
                    price_min, 
                    price_max
              ) VALUES (
                    :hostel_name, 
                    :location, 
                    :distance, 
                    :school_id, 
                    :manager_id,
                    :rating, 
                    :description, 
                    :address, 
                    :price_min, 
                    :price_max
              )";

        $stmt = $this->conn->prepare($query);

        // Bind parameters with default values if not provided
        $stmt->bindValue(':hostel_name', $hostel['hostel_name'] ?? '', \PDO::PARAM_STR);
        $stmt->bindValue(':location', $hostel['location'] ?? '', \PDO::PARAM_STR);
        $stmt->bindValue(':distance', isset($hostel['distance']) ? $hostel['distance'] : null, \PDO::PARAM_STR);
        $stmt->bindValue(':school_id', $hostel['school_id'] ?? null, \PDO::PARAM_INT);
        $stmt->bindValue(':manager_id', $manager_id, \PDO::PARAM_INT);
        $stmt->bindValue(':rating', $hostel['rating'] ?? 0.00, \PDO::PARAM_STR);
        $stmt->bindValue(':description', $hostel['description'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':address', $hostel['address'] ?? '', \PDO::PARAM_STR);
        $stmt->bindValue(':price_min', $hostel['price_min'] ?? 0.00, \PDO::PARAM_STR);
        $stmt->bindValue(':price_max', $hostel['price_max'] ?? 0.00, \PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            // Optionally log the error: e.g., $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function getAllHostels()
    {
        // Get all hostels
        $stmt = $this->conn->query("SELECT * FROM " . $this->table_name);
        $hostels = $stmt->fetchAll( \PDO::FETCH_ASSOC);

        $result = [];
        foreach ($hostels as $hostel) {
            $hostelId = $hostel['id'];

            // Get hostel images
            $stmtImages = $this->conn->prepare("SELECT public_id, url FROM hostel_image WHERE hostel_id = ?");
            $stmtImages->execute([$hostelId]);
            $images = $stmtImages->fetchAll(\PDO::FETCH_COLUMN);

            // Get manager record (assuming one manager per hostel)
            $stmtManager = $this->conn->prepare("SELECT id, name, email, phone FROM manager WHERE id = ? LIMIT 1");
            $stmtManager->execute([$hostelId]);
            $manager = $stmtManager->fetch(\PDO::FETCH_ASSOC);
            if (!$manager) {
                $manager = null;
            }

            // Get amenities as a simple array of names
            $stmtAmenities = $this->conn->prepare("SELECT amenity_name FROM amenity WHERE hostel_id = ?");
            $stmtAmenities->execute([$hostelId]);
            $amenities = $stmtAmenities->fetchAll(\PDO::FETCH_COLUMN);

            // Get rooms and group them by room_type
            $stmtRooms = $this->conn->prepare("SELECT id, room_type, price, specification FROM room WHERE hostel_id = ?");
            $stmtRooms->execute([$hostelId]);
            $rooms = $stmtRooms->fetchAll(\PDO::FETCH_ASSOC);
            $roomTypes = [
                'one_in_one'   => [],
                'two_in_one'   => [],
                'three_in_one' => [],
                'four_in_one'  => [],
                'five_in_one'  => [],
                'six_in_one'   => []
            ];
            foreach ($rooms as $room) {
                $rtype = $room['room_type'];
                if (isset($roomTypes[$rtype])) {
                    $roomTypes[$rtype][] = [
                        'id'    => $room['id'],
                        'price' => $room['price'],
                        'specification'  => $room['specification']
                    ];
                }
            }

            // Get reviews
            $stmtReviews = $this->conn->prepare("SELECT id, rating, text, review_date AS date, review_time AS time FROM review WHERE hostel_id = ?");
            $stmtReviews->execute(params: [$hostelId]);
            $reviews = $stmtReviews->fetchAll(\PDO::FETCH_ASSOC);

            // Build final structure for this hostel
            $result[] = [
                'id'          => (int)$hostel['id'],
                'hostel_name' => $hostel['hostel_name'],
                'location'    => $hostel['location'],
                'distance'    => $hostel['distance'],
                'image'       => $images,
                'school_id'   => (int)$hostel['school_id'],
                'manager'     => $manager,
                'amenities'   => $amenities,
                'room_type'   => $roomTypes,
                'price_range' => [$hostel['price_min'], $hostel['price_max']],
                'rating'      => $hostel['rating'],
                'description' => $hostel['description'],
                'address'     => $hostel['address'],
                'reviews'     => $reviews
            ];
        }
        
        return $result;
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
                  SET hostel_name = :hostel_name, location = :location, distance = :distance, school_id = :school_id, manager_id = :manager_id, rating = :rating, description = :description, address = :address, price_min = :price_min, price_max = :price_max WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':hostel_name', $hostel['hostel_name']);
        $stmt->bindParam(':location', $hostel['location']);
        $stmt->bindParam(':distance', $hostel['distance']);
        $stmt->bindParam(':school_id', $hostel['school_id']);
        $stmt->bindParam(':manager_id', $hostel['manager_id']);
        $stmt->bindParam(':rating', $hostel['rating']);
        $stmt->bindParam(':description', $hostel['description']);
        $stmt->bindParam(':address', $hostel['address']);
        $stmt->bindParam(':price_min', $hostel['price_min']);
        $stmt->bindParam(':price_max', $hostel['price_max']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete a hostel
    public function deleteHostel($id): bool
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
              JOIN manager m ON h.manager_id = m.id 
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
              JOIN school s ON h.school_id = s.id 
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
        $query = "SELECT * FROM " . $this->table_name . " WHERE price_min >= :min AND price_max <= :max";
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