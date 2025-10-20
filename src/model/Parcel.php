<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Shipment.php';

use PDO;
use PDOException;

class Parcel {
    private $conn;
    private $table_name = "parcel";
    private $shipment;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->shipment = new Shipment();
    }

    /**
     * Generate a unique tracking number.
     *
     * @param int $length The length of the tracking number. Default is 12.
     * @return string The generated tracking number.
     */
    private function generateTrackingNumber($length = 12): string {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $number = 'TRK-';
            for ($i = 0; $i < $length; $i++) {
                $number .= $characters[rand(0, strlen($characters) - 1)];
            }

            // Check if number exists
            $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE tracking_number = :number";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':number', $number, PDO::PARAM_STR);
            $stmt->execute();
            $exists = (bool)$stmt->fetchColumn();

        } while ($exists);

        return $number;
    }

    /**
     * Create a new parcel record.
     * Validates that the shipment exists before creating the parcel.
     * Note: shipment_id is REQUIRED - all parcels must belong to a shipment.
     *
     * Expected keys in $parcel:
     * - shipment_id: int (required)
     * - description: string (optional)
     * - weight: decimal (optional)
     * - dimensions: string (optional)
     * - status: string (optional, default 'pending')
     * - sender_name: string (optional)
     * - recipient_name: string (optional)
     *
     * @param array $parcel
     * @return array Returns array with 'success' boolean and 'id' or 'error' message.
     */
    public function createParcel(array $parcel): array {
        // Validate that shipment_id is provided
        if (!isset($parcel['shipment_id']) || empty($parcel['shipment_id'])) {
            return [
                'success' => false,
                'error' => 'Shipment ID is required - all parcels must belong to a shipment'
            ];
        }

        // Validate that shipment exists
        if (!$this->shipment->shipmentExists($parcel['shipment_id'])) {
            return [
                'success' => false,
                'error' => 'Shipment does not exist'
            ];
        }

        // Generate tracking number if not provided
        $tracking_number = $parcel['tracking_number'] ?? $this->generateTrackingNumber();

        $query = "INSERT INTO " . $this->table_name . " (
                        shipment_id,
                        tracking_number,
                        description,
                        weight,
                        dimensions,
                        status,
                        sender_name,
                        recipient_name
                  ) VALUES (
                        :shipment_id,
                        :tracking_number,
                        :description,
                        :weight,
                        :dimensions,
                        :status,
                        :sender_name,
                        :recipient_name
                  )";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':shipment_id', $parcel['shipment_id'], PDO::PARAM_INT);
        $stmt->bindValue(':tracking_number', $tracking_number, PDO::PARAM_STR);
        $stmt->bindValue(':description', $parcel['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $parcel['weight'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':dimensions', $parcel['dimensions'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $parcel['status'] ?? 'pending', PDO::PARAM_STR);
        $stmt->bindValue(':sender_name', $parcel['sender_name'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':recipient_name', $parcel['recipient_name'] ?? null, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'id' => $this->conn->lastInsertId(),
                    'tracking_number' => $tracking_number
                ];
            }
            return [
                'success' => false,
                'error' => 'Failed to create parcel'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve a parcel record by its ID.
     *
     * @param int $id Parcel ID.
     * @return mixed Associative array of parcel details or false if not found.
     */
    public function getParcelById($id) {
        $query = "SELECT p.*, s.shipment_number, s.origin, s.destination, s.status as shipment_status
                  FROM " . $this->table_name . " p
                  LEFT JOIN shipment s ON p.shipment_id = s.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all parcels.
     *
     * @return array Array of all parcels.
     */
    public function getAllParcels() {
        $query = "SELECT p.*, s.shipment_number, s.origin, s.destination, s.status as shipment_status
                  FROM " . $this->table_name . " p
                  LEFT JOIN shipment s ON p.shipment_id = s.id
                  ORDER BY p.id DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all parcels for a specific shipment.
     *
     * @param int $shipment_id The shipment ID.
     * @return array Array of parcels.
     */
    public function getParcelsByShipmentId($shipment_id) {
        $query = "SELECT p.*, s.shipment_number, s.origin, s.destination, s.status as shipment_status
                  FROM " . $this->table_name . " p
                  LEFT JOIN shipment s ON p.shipment_id = s.id
                  WHERE p.shipment_id = :shipment_id 
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shipment_id', $shipment_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update an existing parcel record.
     * Validates that the shipment exists if shipment_id is being updated.
     * Note: shipment_id cannot be set to null - parcels must always belong to a shipment.
     *
     * @param int $id The parcel ID to update.
     * @param array $parcel Associative array with updated parcel data.
     * @return array Returns array with 'success' boolean and 'error' message if applicable.
     */
    public function updateParcel($id, array $parcel): array {
        // Validate that shipment_id is not being set to null or empty
        if (array_key_exists('shipment_id', $parcel) && empty($parcel['shipment_id'])) {
            return [
                'success' => false,
                'error' => 'Shipment ID cannot be empty - all parcels must belong to a shipment'
            ];
        }

        // Validate that shipment exists if shipment_id is being updated
        if (array_key_exists('shipment_id', $parcel) && !empty($parcel['shipment_id']) && !$this->shipment->shipmentExists($parcel['shipment_id'])) {
            return [
                'success' => false,
                'error' => 'Shipment does not exist'
            ];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      shipment_id = :shipment_id,
                      description = :description,
                      weight = :weight,
                      dimensions = :dimensions,
                      status = :status,
                      sender_name = :sender_name,
                      recipient_name = :recipient_name
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':shipment_id', $parcel['shipment_id'], PDO::PARAM_INT);
        $stmt->bindValue(':description', $parcel['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $parcel['weight'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':dimensions', $parcel['dimensions'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $parcel['status'], PDO::PARAM_STR);
        $stmt->bindValue(':sender_name', $parcel['sender_name'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':recipient_name', $parcel['recipient_name'] ?? null, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return ['success' => true];
            }
            return [
                'success' => false,
                'error' => 'Failed to update parcel'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a parcel record by its ID.
     *
     * @param int $id The parcel ID.
     * @return bool True on success, false on failure.
     */
    public function deleteParcel($id): bool {
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
