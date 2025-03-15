<?php

require_once __DIR__ . '/../../controller/AmenityController.php';

return function ($app) {
    $amenityController = new AmenityController();

    $app->post('/api/amenity', function ($request, $response) use ($amenityController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $amenityController->addHostelAmenity($data['hostel_id'], $data['amenity']);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/amenity/{id}', function ($request, $response, $args) use ($amenityController) {
        $id = $args['id'];
        $result = $amenityController->getHostelAmenity($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->patch('/api/amenity/{id}', function ($request, $response, $args) use ($amenityController) {
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $amenityController->updateHostelAmenity($id, $data['amenity']);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/api/amenity/{id}', function ($request, $response, $args) use ($amenityController) {
        $id = $args['id'];
        $result = $amenityController->deleteHostelAmenity($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};