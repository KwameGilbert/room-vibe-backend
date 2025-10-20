<?php

require_once __DIR__ . '/../../controller/ShipmentController.php';

return function ($app) {
    $shipmentController = new ShipmentController();

    // Create a new shipment
    $app->post('/api/shipment', function ($request, $response, $args) use ($shipmentController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $shipmentController->createShipment($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get shipment by ID
    $app->get('/api/shipment/{id}', function ($request, $response, $args) use ($shipmentController) {
        $id = (int)$args['id'];
        $result = $shipmentController->getShipmentById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get all shipments
    $app->get('/api/shipment', function ($request, $response, $args) use ($shipmentController) {
        $result = $shipmentController->getAllShipments();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get shipments by warehouse ID
    $app->get('/api/shipment/warehouse/{warehouse_id}', function ($request, $response, $args) use ($shipmentController) {
        $warehouse_id = (int)$args['warehouse_id'];
        $result = $shipmentController->getShipmentsByWarehouseId($warehouse_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Update a shipment
    $app->put('/api/shipment/{id}', function ($request, $response, $args) use ($shipmentController) {
        $id = (int)$args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $shipmentController->updateShipment($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Delete a shipment
    $app->delete('/api/shipment/{id}', function ($request, $response, $args) use ($shipmentController) {
        $id = (int)$args['id'];
        $result = $shipmentController->deleteShipment($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};
