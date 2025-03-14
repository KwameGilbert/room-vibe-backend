<?php

namespace App\Route;

use App\Controller\ManagerController;

return function ($app) {
    $managerController = new ManagerController();

    // Route to create a new manager
    $app->post('/api/manager', function ($request, $response) use ($managerController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $managerController->createManager($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get a manager by ID
    $app->get('/api/manager/{id:[0-9]+}', function ($request, $response, $args) use ($managerController) {
        $id = $args['id'];
        $result = $managerController->getManagerById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get all managers
    $app->get('/api/managers', function ($request, $response) use ($managerController) {
        $result = $managerController->getAllManagers();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get manager by email
    $app->get('/api/manager/email/{email}', function ($request, $response, $args) use ($managerController) {
        $email = $args['email'];
        $result = $managerController->getManagerByEmail($email);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get manager by phone
    $app->get('/api/manager/phone/{phone}', function ($request, $response, $args) use ($managerController) {
        $phone = $args['phone'];
        $result = $managerController->getManagerByPhone($phone);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    // Route to get manager of a specific hostel
    $app->get('/api/manager/hostel/{id:[0-9]+}', function ($request, $response, $args) use ($managerController) {
        $hostel_id = $args['id'];
        $result = $managerController->getManagerByHostelId($hostel_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to update a manager by ID
    $app->patch('/api/manager/{id}', function ($request, $response, $args) use ($managerController) {
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $managerController->updateManager($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to delete a manager by ID
    $app->delete('/api/manager/{id}', function ($request, $response, $args) use ($managerController) {
        $id = $args['id'];
        $result = $managerController->deleteManager($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });


};