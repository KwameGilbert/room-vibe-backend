<?php

require_once __DIR__ . '/../config/Database.php';

use PDO;
use PDOException;

class Room {
    private $conn;
    private $table_name = "rooms";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new room record.
     *
     * Expected keys in $room:
     * - capacity: int
     * - number_occupied: int (default to 0 if not provided)
     * - price: string or decimal (e.g., '150.00')
     * - specification: string (details about the room)
     * - hostel_id: int (ID of the associated hostel)
     *
     * @param array $room
     * @return bool True on success, false on failure.
     */
    public function createRoom(array $room): bool {
        $query = "INSERT INTO " . $this->table_name . " (
                        capacity,
                        number_occupied,
                        price,
                        specification,
                        hostel_id
                  ) VALUES (
                        :capacity,
                        :number_occupied,
                        :price,
                        :specification,
                        :hostel_id
                  )";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':capacity', $room['capacity'], PDO::PARAM_INT);
        $stmt->bindValue(':number_occupied', $room['number_occupied'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':price', $room['price'], PDO::PARAM_STR);
        $stmt->bindValue(':specification', $room['specification'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':hostel_id', $room['hostel_id'], PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error (e.g., $this->logger->error($e->getMessage());)
            return false;
        }
    }

    /**
     * Retrieve a room record by its ID.
     *
     * @param int $id Room ID.
     * @return mixed Associative array of room details or false if not found.
     */
    public function getRoomById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all room records.
     *
     * @return array Array of all rooms.
     */
    public function getAllRooms() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all rooms by hostel ID.
     *
     * @param int $hostel_id The associated hostel's ID.
     * @return array Array of rooms for the given hostel.
     */
    public function getRoomsByHostelId($hostel_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE hostel_id = :hostel_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update an existing room record.
     *
     * Expected keys in $room:
     * - capacity, number_occupied, price, specification, hostel_id.
     *
     * @param int $id The room ID to update.
     * @param array $room Associative array with updated room data.
     * @return bool True on success, false on failure.
     */
    public function updateRoom($id, array $room): bool {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      capacity = :capacity,
                      number_occupied = :number_occupied,
                      price = :price,
                      specification = :specification,
                      hostel_id = :hostel_id
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':capacity', $room['capacity'], PDO::PARAM_INT);
        $stmt->bindValue(':number_occupied', $room['number_occupied'], PDO::PARAM_INT);
        $stmt->bindValue(':price', $room['price'], PDO::PARAM_STR);
        $stmt->bindValue(':specification', $room['specification'], PDO::PARAM_STR);
        $stmt->bindValue(':hostel_id', $room['hostel_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error
            return false;
        }
    }

    /**
     * Delete a room record by its ID.
     *
     * @param int $id The room ID.
     * @return bool True on success, false on failure.
     */
    public function deleteRoom($id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error
            return false;
        }
    }
}