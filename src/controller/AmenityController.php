<?php 

namespace App\Controller;

use App\Model\Amenity;

class AmenityController{
    private $amenity;

    public function _construct(){
        $this->amenity = new Amenity();
    }

    public function addHostelAmenity($hostel_id, array $amenity){
        if($this->amenity->addHostelAmenity($hostel_id, $amenity)){
            return json_encode([
                "status" => true,
                "message" => "Hostel amenity added successfully",
                "hostel_id" => $hostel_id,
                "data" => $amenity
            ], flags: 201);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to add amenity"
            ],flags: 500);
        }
    }

    public function getHostelAmenity($id){
        $hostelAmenity = $this->amenity->getHostelAmenity($id);
        if($hostelAmenity > 0){
            return json_encode([
                "status" => true,
                "hostelAmenity" => $hostelAmenity
            ], flags: 201);
        }elseif($hostelAmenity == 0){
            return json_encode(["status" => true,
            "hostelAmenity" => []
        ], 200);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to fetch hostel amenity"
            ], 500);
        }
    }

    public function updateHostelAmenity($id, $amenity){
        if($this->amenity->updateHostelAmenity($id, $amenity)){
            return json_encode([
                "status" => true,
                "message" => "Hostel Amenity updated successfully"
            ], 201);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to update hostel amenity"
            ], 500);
        }
    }

    public function deleteHostelAmenity($id){
        if($this->amenity->deleteHostelAmenity($id)){
            return json_encode([
                "status" => true,
                "message" => "Hostel Amenity deleted successfully"
            ], 201);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to delete hostel amenity"
            ], 500);
        }
    }

}