<?php

namespace App\Route;

use App\Controller\HostelController;

return function ($app) {
    $hostelController = new HostelController();

    // Route to get all hostels
    $app->get('/api/explore/hostels', function ($request, $response) use ($hostelController) {
        $result = $hostelController->getAllHostels();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get a hostel by ID
    $app->get('/api/hostel/{id:[0-9]+}', function ($request, $response, $args) use ($hostelController) {
        $id = $args['id'];
        $result = $hostelController->getHostelById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to create a new hostel
    $app->post('/api/hostel', function ($request, $response) use ($hostelController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $hostelController->createHostel($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to update a hostel by ID
    $app->patch('/api/hostel/{id}', function ($request, $response, $args) use ($hostelController) {
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $hostelController->updateHostel($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to delete a hostel by ID
    $app->delete('/api/hostel/{id}', function ($request, $response, $args) use ($hostelController) {
        $id = $args['id'];
        $result = $hostelController->deleteHostel($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/hostels/manager/{id}', function ($request, $response, $args) use ($hostelController) {
        $id = $args['id'];
        $result = $hostelController->getHostelsByManagerId($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/hostels/school/{id}', function ($request, $response, $args) use ($hostelController) {
        $id = $args['id'];
        $result = $hostelController->getHostelsBySchoolId($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    //Get all hostels by location
    $app->get('/api/hostels/location/{location}', function ($request, $response, $args) use ($hostelController) {
        $location = $args['location'];
        $result = $hostelController->getHostelsByLocation($location);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get all hostels within a price range
    $app->get('/api/hostels/price/{min}/{max}', function ($request, $response, $args) use ($hostelController) {
        $minPrice = $args['min'];
        $maxPrice = $args['max'];
        $result = $hostelController->getHostelsByPriceRange($minPrice, $maxPrice);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get all hostesls within a certain distance
    $app->get('/api/hostels/distance/{distance}', function ($request, $response, $args) use ($hostelController) {
        $distance = $args['distance'];
        $result = $hostelController->getHostelsByDistance($distance);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });


};
