<?php

require_once __DIR__ . '/../../controller/BookingController.php';

return function ($app) {
    $bookingController = new BookingController();

    // Create a new booking
    $app->post('/api/booking/student/{student_id}', function ($request, $response, $args) use ($bookingController) {
        $student_id = (int)$args['student_id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $bookingController->createBooking($student_id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get booking by ID
    $app->get('/api/booking/{booking_id}', function ($request, $response, $args) use ($bookingController) {
        $booking_id = (int)$args['booking_id'];
        $result = $bookingController->getBookingById($booking_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get bookings by student ID
    $app->get('/api/booking/student/{student_id}', function ($request, $response, $args) use ($bookingController) {
        $student_id = (int)$args['student_id'];
        $result = $bookingController->getBookingsByStudentId($student_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get bookings by hostel ID
    $app->get('/api/booking/hostel/{hostel_id}', function ($request, $response, $args) use ($bookingController) {
        $hostel_id = (int)$args['hostel_id'];
        $result = $bookingController->getBookingsByHostelId($hostel_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Update a booking
    $app->put('/api/booking/{booking_id}', function ($request, $response, $args) use ($bookingController) {
        $booking_id = (int)$args['booking_id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $bookingController->updateBooking($booking_id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Cancel a booking
    $app->delete('/api/booking/{booking_id}', function ($request, $response, $args) use ($bookingController) {
        $booking_id = (int)$args['booking_id'];
        $result = $bookingController->cancelBooking($booking_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Delete a booking
    $app->delete('/api/booking/{booking_id}/delete', function ($request, $response, $args) use ($bookingController) {
        $booking_id = (int)$args['booking_id'];
        $result = $bookingController->deleteBooking($booking_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};