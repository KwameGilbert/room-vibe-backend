<?php

require_once __DIR__ . '/../model/Warehouse.php';

class WarehouseController {
    private $warehouse;

    public function __construct() {
        $this->warehouse = new Warehouse();
    }

    public function createWarehouse(array $data) {
        $id = $this->warehouse->createWarehouse($data);
        if ($id) {
            return json_encode([
                "status" => true,
                "message" => "Warehouse created successfully",
                "data" => ["id" => $id]
            ], 201);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to create warehouse"
            ], 500);
        }
    }

    public function getWarehouseById($id) {
        $warehouse = $this->warehouse->getWarehouseById($id);
        if ($warehouse) {
            return json_encode([
                "status" => true,
                "warehouse" => $warehouse
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Warehouse not found"
            ], 404);
        }
    }

    public function getAllWarehouses() {
        $warehouses = $this->warehouse->getAllWarehouses();
        return json_encode([
            "status" => true,
            "warehouses" => $warehouses
        ], 200);
    }

    public function updateWarehouse($id, array $data) {
        if ($this->warehouse->updateWarehouse($id, $data)) {
            return json_encode([
                "status" => true,
                "message" => "Warehouse updated successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to update warehouse"
            ], 500);
        }
    }

    public function deleteWarehouse($id) {
        if ($this->warehouse->deleteWarehouse($id)) {
            return json_encode([
                "status" => true,
                "message" => "Warehouse deleted successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to delete warehouse"
            ], 500);
        }
    }
}
