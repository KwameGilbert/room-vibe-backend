<?php

require_once __DIR__ . '/../../controller/ParcelController.php';

return function ($app) {
    $parcelController = new ParcelController();

    // Create a new parcel
    $app->post('/api/parcel', function ($request, $response, $args) use ($parcelController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $parcelController->createParcel($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get parcel by ID
    $app->get('/api/parcel/{id}', function ($request, $response, $args) use ($parcelController) {
        $id = (int)$args['id'];
        $result = $parcelController->getParcelById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get all parcels
    $app->get('/api/parcel', function ($request, $response, $args) use ($parcelController) {
        $result = $parcelController->getAllParcels();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get parcels by shipment ID
    $app->get('/api/parcel/shipment/{shipment_id}', function ($request, $response, $args) use ($parcelController) {
        $shipment_id = (int)$args['shipment_id'];
        $result = $parcelController->getParcelsByShipmentId($shipment_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Update a parcel
    $app->put('/api/parcel/{id}', function ($request, $response, $args) use ($parcelController) {
        $id = (int)$args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $parcelController->updateParcel($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Delete a parcel
    $app->delete('/api/parcel/{id}', function ($request, $response, $args) use ($parcelController) {
        $id = (int)$args['id'];
        $result = $parcelController->deleteParcel($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};
