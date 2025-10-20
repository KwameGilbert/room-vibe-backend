<?php

require_once __DIR__ . '/../model/Parcel.php';

class ParcelController {
    private $parcel;

    public function __construct() {
        $this->parcel = new Parcel();
    }

    public function createParcel(array $data) {
        $result = $this->parcel->createParcel($data);
        
        if ($result['success']) {
            return json_encode([
                "status" => true,
                "message" => "Parcel created successfully",
                "data" => [
                    "id" => $result['id'],
                    "tracking_number" => $result['tracking_number']
                ]
            ], 201);
        } else {
            return json_encode([
                "status" => false,
                "message" => $result['error']
            ], 400);
        }
    }

    public function getParcelById($id) {
        $parcel = $this->parcel->getParcelById($id);
        if ($parcel) {
            return json_encode([
                "status" => true,
                "parcel" => $parcel
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Parcel not found"
            ], 404);
        }
    }

    public function getAllParcels() {
        $parcels = $this->parcel->getAllParcels();
        return json_encode([
            "status" => true,
            "parcels" => $parcels
        ], 200);
    }

    public function getParcelsByShipmentId($shipment_id) {
        $parcels = $this->parcel->getParcelsByShipmentId($shipment_id);
        return json_encode([
            "status" => true,
            "parcels" => $parcels
        ], 200);
    }

    public function updateParcel($id, array $data) {
        $result = $this->parcel->updateParcel($id, $data);
        
        if ($result['success']) {
            return json_encode([
                "status" => true,
                "message" => "Parcel updated successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => $result['error']
            ], 400);
        }
    }

    public function deleteParcel($id) {
        if ($this->parcel->deleteParcel($id)) {
            return json_encode([
                "status" => true,
                "message" => "Parcel deleted successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to delete parcel"
            ], 500);
        }
    }
}
