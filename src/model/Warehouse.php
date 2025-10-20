<?php

require_once __DIR__ . '/../config/Database.php';

use PDO;
use PDOException;

class Warehouse {
    private $conn;
    private $table_name = "warehouse";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new warehouse record.
     *
     * Expected keys in $warehouse:
     * - name: string (required)
     * - location: string (required)
     * - address: string (required)
     * - capacity: int (optional)
     * - description: string (optional)
     *
     * @param array $warehouse
     * @return bool|int Returns the warehouse ID on success, false on failure.
     */
    public function createWarehouse(array $warehouse) {
        $query = "INSERT INTO " . $this->table_name . " (
                        name,
                        location,
                        address,
                        capacity,
                        description
                  ) VALUES (
                        :name,
                        :location,
                        :address,
                        :capacity,
                        :description
                  )";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $warehouse['name'], PDO::PARAM_STR);
        $stmt->bindValue(':location', $warehouse['location'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $warehouse['address'], PDO::PARAM_STR);
        $stmt->bindValue(':capacity', $warehouse['capacity'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':description', $warehouse['description'] ?? null, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Retrieve a warehouse record by its ID.
     *
     * @param int $id Warehouse ID.
     * @return mixed Associative array of warehouse details or false if not found.
     */
    public function getWarehouseById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a warehouse exists by its ID.
     *
     * @param int $id Warehouse ID.
     * @return bool True if warehouse exists, false otherwise.
     */
    public function warehouseExists($id): bool {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Retrieve all warehouse records.
     *
     * @return array Array of all warehouses.
     */
    public function getAllWarehouses() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update an existing warehouse record.
     *
     * @param int $id The warehouse ID to update.
     * @param array $warehouse Associative array with updated warehouse data.
     * @return bool True on success, false on failure.
     */
    public function updateWarehouse($id, array $warehouse): bool {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      name = :name,
                      location = :location,
                      address = :address,
                      capacity = :capacity,
                      description = :description
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':name', $warehouse['name'], PDO::PARAM_STR);
        $stmt->bindValue(':location', $warehouse['location'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $warehouse['address'], PDO::PARAM_STR);
        $stmt->bindValue(':capacity', $warehouse['capacity'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':description', $warehouse['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a warehouse record by its ID.
     *
     * @param int $id The warehouse ID.
     * @return bool True on success, false on failure.
     */
    public function deleteWarehouse($id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
