<?php

require_once __DIR__ . '/../../controller/ReviewController.php';

return function ($app) {
    $reviewController = new ReviewController();

    // Route to create a new review
    $app->post('/api/review', function ($request, $response) use ($reviewController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $reviewController->createHostelReview($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get a review by ID
    $app->get('/api/review/{id:[0-9]+}', function ($request, $response, $args) use ($reviewController) {
        $id = $args['id'];
        $result = $reviewController->getReview($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get all reviews of a hostel
    $app->get('/api/reviews/hostel/{id:[0-9]+}', function ($request, $response, $args) use ($reviewController) {
        $hostel_id = $args['id'];
        $result = $reviewController->getHostelReviews($hostel_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to update a review by ID
    $app->patch('/api/review/{id}', function ($request, $response, $args) use ($reviewController) {
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $reviewController->updateReview($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to delete a review by ID
    $app->delete('/api/review/{id}', function ($request, $response, $args) use ($reviewController) {
        $id = $args['id'];
        $result = $reviewController->deleteReview($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};