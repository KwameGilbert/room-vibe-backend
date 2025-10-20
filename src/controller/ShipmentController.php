<?php

require_once __DIR__ . '/../model/Shipment.php';

class ShipmentController {
    private $shipment;

    public function __construct() {
        $this->shipment = new Shipment();
    }

    public function createShipment(array $data) {
        $result = $this->shipment->createShipment($data);
        
        if ($result['success']) {
            return json_encode([
                "status" => true,
                "message" => "Shipment created successfully",
                "data" => [
                    "id" => $result['id'],
                    "shipment_number" => $result['shipment_number']
                ]
            ], 201);
        } else {
            return json_encode([
                "status" => false,
                "message" => $result['error']
            ], 400);
        }
    }

    public function getShipmentById($id) {
        $shipment = $this->shipment->getShipmentById($id);
        if ($shipment) {
            return json_encode([
                "status" => true,
                "shipment" => $shipment
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Shipment not found"
            ], 404);
        }
    }

    public function getAllShipments() {
        $shipments = $this->shipment->getAllShipments();
        return json_encode([
            "status" => true,
            "shipments" => $shipments
        ], 200);
    }

    public function getShipmentsByWarehouseId($warehouse_id) {
        $shipments = $this->shipment->getShipmentsByWarehouseId($warehouse_id);
        return json_encode([
            "status" => true,
            "shipments" => $shipments
        ], 200);
    }

    public function updateShipment($id, array $data) {
        $result = $this->shipment->updateShipment($id, $data);
        
        if ($result['success']) {
            return json_encode([
                "status" => true,
                "message" => "Shipment updated successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => $result['error']
            ], 400);
        }
    }

    public function deleteShipment($id) {
        if ($this->shipment->deleteShipment($id)) {
            return json_encode([
                "status" => true,
                "message" => "Shipment deleted successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to delete shipment"
            ], 500);
        }
    }
}
