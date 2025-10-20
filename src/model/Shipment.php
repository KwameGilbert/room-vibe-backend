<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Warehouse.php';

use PDO;
use PDOException;

class Shipment {
    private $conn;
    private $table_name = "shipment";
    private $warehouse;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->warehouse = new Warehouse();
    }

    /**
     * Generate a unique shipment number.
     *
     * @param int $length The length of the shipment number. Default is 10.
     * @return string The generated shipment number.
     */
    private function generateShipmentNumber($length = 10): string {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $number = 'SHP-';
            for ($i = 0; $i < $length; $i++) {
                $number .= $characters[rand(0, strlen($characters) - 1)];
            }

            // Check if number exists
            $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE shipment_number = :number";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':number', $number, PDO::PARAM_STR);
            $stmt->execute();
            $exists = (bool)$stmt->fetchColumn();

        } while ($exists);

        return $number;
    }

    /**
     * Create a new shipment record.
     * Validates that the warehouse exists before creating the shipment.
     *
     * Expected keys in $shipment:
     * - warehouse_id: int (required)
     * - origin: string (required)
     * - destination: string (required)
     * - shipment_date: string (required, datetime format)
     * - delivery_date: string (optional, datetime format)
     * - status: string (optional, default 'pending')
     * - notes: string (optional)
     *
     * @param array $shipment
     * @return array Returns array with 'success' boolean and 'id' or 'error' message.
     */
    public function createShipment(array $shipment): array {
        // Validate that warehouse exists
        if (!isset($shipment['warehouse_id']) || !$this->warehouse->warehouseExists($shipment['warehouse_id'])) {
            return [
                'success' => false,
                'error' => 'Warehouse does not exist'
            ];
        }

        // Generate shipment number if not provided
        $shipment_number = $shipment['shipment_number'] ?? $this->generateShipmentNumber();

        $query = "INSERT INTO " . $this->table_name . " (
                        warehouse_id,
                        shipment_number,
                        origin,
                        destination,
                        shipment_date,
                        delivery_date,
                        status,
                        notes
                  ) VALUES (
                        :warehouse_id,
                        :shipment_number,
                        :origin,
                        :destination,
                        :shipment_date,
                        :delivery_date,
                        :status,
                        :notes
                  )";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':warehouse_id', $shipment['warehouse_id'], PDO::PARAM_INT);
        $stmt->bindValue(':shipment_number', $shipment_number, PDO::PARAM_STR);
        $stmt->bindValue(':origin', $shipment['origin'], PDO::PARAM_STR);
        $stmt->bindValue(':destination', $shipment['destination'], PDO::PARAM_STR);
        $stmt->bindValue(':shipment_date', $shipment['shipment_date'], PDO::PARAM_STR);
        $stmt->bindValue(':delivery_date', $shipment['delivery_date'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $shipment['status'] ?? 'pending', PDO::PARAM_STR);
        $stmt->bindValue(':notes', $shipment['notes'] ?? null, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'id' => $this->conn->lastInsertId(),
                    'shipment_number' => $shipment_number
                ];
            }
            return [
                'success' => false,
                'error' => 'Failed to create shipment'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve a shipment record by its ID.
     *
     * @param int $id Shipment ID.
     * @return mixed Associative array of shipment details or false if not found.
     */
    public function getShipmentById($id) {
        $query = "SELECT s.*, w.name as warehouse_name, w.location as warehouse_location 
                  FROM " . $this->table_name . " s
                  LEFT JOIN warehouse w ON s.warehouse_id = w.id
                  WHERE s.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a shipment exists by its ID.
     *
     * @param int $id Shipment ID.
     * @return bool True if shipment exists, false otherwise.
     */
    public function shipmentExists($id): bool {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Retrieve all shipments.
     *
     * @return array Array of all shipments.
     */
    public function getAllShipments() {
        $query = "SELECT s.*, w.name as warehouse_name, w.location as warehouse_location 
                  FROM " . $this->table_name . " s
                  LEFT JOIN warehouse w ON s.warehouse_id = w.id
                  ORDER BY s.id DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all shipments for a specific warehouse.
     *
     * @param int $warehouse_id The warehouse ID.
     * @return array Array of shipments.
     */
    public function getShipmentsByWarehouseId($warehouse_id) {
        $query = "SELECT s.*, w.name as warehouse_name, w.location as warehouse_location 
                  FROM " . $this->table_name . " s
                  LEFT JOIN warehouse w ON s.warehouse_id = w.id
                  WHERE s.warehouse_id = :warehouse_id 
                  ORDER BY s.shipment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':warehouse_id', $warehouse_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update an existing shipment record.
     * Validates that the warehouse exists before updating.
     *
     * @param int $id The shipment ID to update.
     * @param array $shipment Associative array with updated shipment data.
     * @return array Returns array with 'success' boolean and 'error' message if applicable.
     */
    public function updateShipment($id, array $shipment): array {
        // Validate that warehouse exists if warehouse_id is being updated
        if (isset($shipment['warehouse_id']) && !$this->warehouse->warehouseExists($shipment['warehouse_id'])) {
            return [
                'success' => false,
                'error' => 'Warehouse does not exist'
            ];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      warehouse_id = :warehouse_id,
                      origin = :origin,
                      destination = :destination,
                      shipment_date = :shipment_date,
                      delivery_date = :delivery_date,
                      status = :status,
                      notes = :notes
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':warehouse_id', $shipment['warehouse_id'], PDO::PARAM_INT);
        $stmt->bindValue(':origin', $shipment['origin'], PDO::PARAM_STR);
        $stmt->bindValue(':destination', $shipment['destination'], PDO::PARAM_STR);
        $stmt->bindValue(':shipment_date', $shipment['shipment_date'], PDO::PARAM_STR);
        $stmt->bindValue(':delivery_date', $shipment['delivery_date'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $shipment['status'], PDO::PARAM_STR);
        $stmt->bindValue(':notes', $shipment['notes'] ?? null, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return ['success' => true];
            }
            return [
                'success' => false,
                'error' => 'Failed to update shipment'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a shipment record by its ID.
     *
     * @param int $id The shipment ID.
     * @return bool True on success, false on failure.
     */
    public function deleteShipment($id): bool {
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
