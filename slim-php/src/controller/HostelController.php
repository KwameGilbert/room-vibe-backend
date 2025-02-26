<?php

namespace App\Controller;

use App\Model\Hostel;

class HostelController
{
    private $hostel;

    public function __construct()
    {
        $this->hostel = new Hostel();
    }

    // Create a new hostel
    public function createHostel($hostel)
    {
        if ($this->hostel->createHostel($hostel)) {
            return json_encode([
                "message" => "Hostel created successfully.",
                "data" => $hostel
            ], 201);
        } else {
            return json_encode([
                "message" => "Failed to create hostel."
            ], 500);
        }
    }

    // Read all hostels
    public function getAllHostels()
    {
        $hostels = $this->hostel->getAllHostels();
        if ($hostels > 0) {
            return json_encode(["allHostels" => $hostels], 200);
        } elseif($hostels == 0) {
            return json_encode(["allHostels" => []], 200);  
        } else{
            return json_encode([
                "status" => false,
                "message" => "Failed to fetch all hostels"
            ], 500);
        }
    }

    // Read a single hostel by Id
    public function getHostelById($id)
    {
        $hostel = $this->hostel->getHostelById($id);
        if ($hostel > 0) {
            return json_encode([
                "status" => true,
                "hostel" => $hostel
            ], 200);
        } else if($hostel == 0){
            return json_encode([
                "status" => false,
                "message" => "Hostel with ID {$id} not found"
            ], 404);            
        }
        else {
            return json_encode([
                "message" => "Failed to fetch hostel with ID {$id}"
            ], 500);
        }
    }

    // Update a hostel by Id
    public function updateHostel($id, $hostel)
    {
        if ($this->hostel->updateHostel($id, $hostel)) {
            return json_encode([
                "status" => true,
                "message" => "Hostel ID {$id} Updated Successfully",
                "hostel" => $hostel
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to update Hostel {$id}"
            ], 500);
        }
    }

    // Delete a hostel by Id        
    public function deleteHostel($id)
    {
        if ($this->hostel->deleteHostel($id)) {
            return json_encode([
                "status" => true,
                "message" => "Hostel ID {$id} Deleted Successfully"
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to delete hostel id: {$id}"
            ], 500);
        }
    }

    // Get all hostels by manager id
    public function getHostelsByManagerId($id)
    {
        $managerHostels = $this->hostel->getHostelsByManagerId($id);
        if ($managerHostels >= 0) {
            return json_encode([
                "manager" => $id,
                "managerHostels" => $managerHostels
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to fetch hostels for manager ID {$id}"
            ], 500);
        }
    }

    // Get all hostels by school id
    public function getHostelsBySchoolId($id)
    {
        $schoolHostels = $this->hostel->getHostelsBySchoolId($id);
        if ($schoolHostels >= 0) {
            return json_encode([
                "school" => $id,
                "schoolHostels" => $schoolHostels
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to fetch hostels for school ID {$id}"
            ], 500);
        }
    }

    // Get all hostels by location
    public function getHostelsByLocation($location)
    {
        $locationHostels = $this->hostel->getHostelsByLocation($location);
        if ($locationHostels >= 0) {
            return json_encode([
                "location" => $location,
                "locationHostels" => $locationHostels
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to fetch hostels for location {$location}"
            ], 500);
        }
    }

    // Get all hostels with price range
    public function getHostelsByPriceRange($min, $max)
    {
        $priceHostels = $this->hostel->getHostelsByPriceRange($min, $max);
        if ($priceHostels >= 0) {
            return json_encode([
                "priceRange" => ["min" => $min, "max" => $max],
                "priceHostels" => $priceHostels
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to fetch hostels within price range {$min} - {$max}"
            ], 500);
        }
    }

    // Get all hostels within a certain distance
    public function getHostelsByDistance($distance)
    {
        $distanceHostels = $this->hostel->getHostelsByDistance($distance);
        if ($distanceHostels >= 0) {
            return json_encode([
                "distance" => $distance,
                "distanceHostels" => $distanceHostels
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to fetch hostels within distance {$distance}"
            ], 500);
        }
    }
}
