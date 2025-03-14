<?php

namespace App\Controller;

use App\Model\Manager;

class ManagerController
{
    private $manager;

    public function __construct()
    {
        $this->manager = new Manager();
    }

    // Create a new manager
    public function createManager($data)
    {
        if ($this->manager->createManager($data)) {
            return json_encode([
                "status" => true,
                "message" => "Manager created successfully.",
                "data" => $data
            ], 201);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to create manager."
            ], 500);
        }
    }

    // Get a single manager by ID
    public function getManagerById($id)
    {
        $manager = $this->manager->getManagerById($id);
        if ($manager) {
            return json_encode([
                "status" => true,
                "manager" => $manager
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Manager not found."
            ], 404);
        }
    }

    // Get all managers
    public function getAllManagers()
    {
        $managers = $this->manager->getAllManagers();
        if (is_array($managers) && count($managers) > 0) {
            return json_encode([
                "status" => true,
                "managers" => $managers
            ], 200);
        } else if ($managers === 0) {
            return json_encode([
                "status" => false,
                "message" => "Error retrieving managers. Please try again."
            ], 500);
        } else {
            return json_encode([
                "status" => false,
                "message" => "No managers found in the system."
            ], 404);
        }
    }

    // Get Manager of a specific hostel
    public function getManagerByHostelId($hostel_id)
    {
        $manager = $this->manager->getManagerByHostelId($hostel_id);
        if (is_array($manager) && count($manager) > 0) {
            return json_encode([
                "status" => true,
                "manager" => $manager
            ], 200);
        } else if ($manager === 0) {
            return json_encode([
                "status" => false,
                "message" => "Error retrieving manager. Please try again."
            ], 500);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Manager not found for this hostel."
            ], 404);
        }
    }

    //Get manager by email
    public function getManagerByEmail($email)
    {
        $manager = $this->manager->getManagerByEmail($email);
        if ($manager) {
            return json_encode([
                "status" => true,
                "manager" => $manager
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Manager not found."
            ], 404);
        }
    }

    //Get manager by phone number
    public function getManagerByPhone($phone)
    {
        $manager = $this->manager->getManagerByPhone($phone);
        if ($manager) {
            return json_encode([
                "status" => true,
                "manager" => $manager
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Manager not found."
            ], 404);
        }
    }

    // Update a manager
    public function updateManager($id, $data)
    {
        if ($this->manager->updateManager($id, $data)) {
            return json_encode([
                "status" => true,
                "message" => "Manager updated successfully.",
                "data" => $data
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to update manager."
            ], 500);
        }
    }

    // Delete a manager
    public function deleteManager($id)
    {
        if ($this->manager->deleteManager($id)) {
            return json_encode([
                "status" => true,
                "message" => "Manager deleted successfully."
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to delete manager."
            ], 500);
        }
    }
}
