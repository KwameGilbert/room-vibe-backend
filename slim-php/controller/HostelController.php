<?php 
 
namespace App\Controller;

use App\Model\Hostel;

class HostelController{
    private $hostel;

    public function __construct(){
        $this->hostel;
    }

    // Create a new hostel
    public function createHostel($hostel){
        if($this->hostel->createHostel($hostel)){
            return json_encode([
                "message" => "Hostel created successfully.",
                "data" => $hostel
            ], 201);
        } else {
            return json_encode([
                "message" => "Failed to create hostel."], 500);
        }
    }

    // Read all hostels
    public function getAllHostels(){
        $hostels = $this->getAllHostels();
        if($hostels >= 0){
            return json_encode(["allHostels" => $hostels], 200);
        } else {
            return json_encode([
                "message"=>"Failed to fetch all hostels"
            ], 500);
        }
    }

    // Read a single hostel by Id
    public function getHostelById($id){
        $hostel = $this->getHostelById($id);
        if($hostel >=0){
            return json_encode([
                "hostel" => $hostel 
            ], 200);
        }else{
            return json_encode([
                "message"=>"Failed to fetch hostel with ID {$id}"
            ], 500);
        }
    }

    public function updateHostel($id, $hostel){
        if($this->updateHostel($id, $hostel)){
            return json_encode([
                "status" => true,
                "message" => "Hostel ID {$id} Updated Successfully",
                "hostel" => $hostel
            ], 200);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to update Hostel {$id}"
            ], 500);
        }
    }

    public function deleteHostel($id){
        if($this->deleteHostel($id)){
            return json_encode([
                "status" => true,
                "message" => "Hostel ID {$id} Deleted Successfully"
            ], 200);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to delete hostel id: {$id}"
            ], 500);
        }
    }

    public function getHostelsByManagerId($id){
        $managerHostels = $this->getHostelById($id);
        if($managerHostels >= 0){
            return json_encode([
                "manager"=> $id,
                "managerHostels" => $managerHostels
            ], 200);
        } else{
            return json_encode([
                "message" => "Failed to fetch hostels for manager ID {$id}"
            ], 500);
        }

    }

    public function getHostelsBySchoolId($id){
        $schoolHostels = $this->getHostelsBySchoolId($id);
        if($schoolHostels >= 0){
            return json_encode([
                "school" => $id,
                "schoolHostels" => $schoolHostels
            ], 200);
        }else{
            return json_encode([
                "message" => "Failed to fetch hostels for school ID {$id}"
            ], 500);
        }
    }


}