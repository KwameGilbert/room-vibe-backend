<?php

namespace App\Route;

use App\Controller\BookingController;

return function ($app){
    $bookingController = new BookingController();

    $app->post('/api/booking/student/{student_id}', function ($request, $response, $args) use ($bookingController){
        $student_id = $args['student_id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $bookingController->createBooking($student_id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/booking/{booking_id}', function ($request, $response, $args) use ($bookingController) {
        $booking_id = $args['booking_id'];
        $result = $bookingController->getBookingById($booking_id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    
};