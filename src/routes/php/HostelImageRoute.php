<?php

namespace App\Route;

use App\Controller\HostelImageController;

return function ($app) {
    $hostelImageController = new HostelImageController();

    // Upload an image for a given hostel ID
    $app->post('/api/hostel/image/upload/{hostel_id}', function ($request, $response, $args) use ($hostelImageController) {
        $hostel_id = $args['hostel_id'];
      
        $result = $hostelImageController->uploadImage($hostel_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get all images for a given hostel ID
    $app->get('/api/hostel/images/{hostel_id}', function ($request, $response, $args) use ($hostelImageController) {
        $hostel_id = $args['hostel_id'];
        $result = $hostelImageController->getHostelImages($hostel_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get hoste image by id
    $app->get('/api/hostel/image/{id}', function ($request, $response, $args) use ($hostelImageController) {
        $id = $args['id'];
        $result = $hostelImageController->getHostelImageById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to update an image by ID
    $app->patch('/api/hostel/image/{id}', function ($request, $response, $args) use ($hostelImageController) {
        $id = $args['id'];
   
        $result = $hostelImageController->updateHostelImage($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to delete an image by ID
    $app->delete('/api/hostel/image/{id}', function ($request, $response, $args) use ($hostelImageController) {
        $id = $args['id'];
        $result = $hostelImageController->deleteImage($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

};