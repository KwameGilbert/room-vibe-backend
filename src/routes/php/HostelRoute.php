<?php

namespace App\Route;

use App\Controller\HostelController;

return function ($app) {
    $hostelController = new HostelController();
  
        // Route to get all hostels
        $app->get('/hostels', function ($request, $response) use ($hostelController) {
            $result = $hostelController->getAllHostels();
            $response->getBody()->write($result);
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Route to get a hostel by ID
        $app->get('/hostel/{id:[0-9]+}', function ($request, $response, $args) use ($hostelController) {
            $id = $args['id'];
            $result = $hostelController->getHostelById($id);
            $response->getBody()->write($result);
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Route to create a new hostel
        $app->post('/hostel', function ($request, $response) use ($hostelController) {
            $data = json_decode($request->getBody()->getContents(), true);
            $result = $hostelController->createHostel($data);
            $response->getBody()->write($result);
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Route to update a hostel by ID
        $app->patch('/{id}', function ($request, $response, $args) use ($hostelController) {
            $id = $args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            $result = $hostelController->updateHostel($id, $data);
            $response->getBody()->write($result);
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Route to delete a hostel by ID
        $app->delete('/{id}', function ($request, $response, $args) use ($hostelController) {
            $id = $args['id'];
            $result = $hostelController->deleteHostel($id);
            $response->getBody()->write($result);
            return $response->withHeader('Content-Type', 'application/json');
        });
   
};