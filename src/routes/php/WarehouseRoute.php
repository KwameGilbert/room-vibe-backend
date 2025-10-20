<?php

require_once __DIR__ . '/../../controller/WarehouseController.php';

return function ($app) {
    $warehouseController = new WarehouseController();

    // Create a new warehouse
    $app->post('/api/warehouse', function ($request, $response, $args) use ($warehouseController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $warehouseController->createWarehouse($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get warehouse by ID
    $app->get('/api/warehouse/{id}', function ($request, $response, $args) use ($warehouseController) {
        $id = (int)$args['id'];
        $result = $warehouseController->getWarehouseById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get all warehouses
    $app->get('/api/warehouse', function ($request, $response, $args) use ($warehouseController) {
        $result = $warehouseController->getAllWarehouses();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Update a warehouse
    $app->put('/api/warehouse/{id}', function ($request, $response, $args) use ($warehouseController) {
        $id = (int)$args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $warehouseController->updateWarehouse($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Delete a warehouse
    $app->delete('/api/warehouse/{id}', function ($request, $response, $args) use ($warehouseController) {
        $id = (int)$args['id'];
        $result = $warehouseController->deleteWarehouse($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};
