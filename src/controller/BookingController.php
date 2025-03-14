<?php

namespace App\Controller;

use App\Model\Booking;

class BookingController{
    private $booking;

    public function __construct(){
        $this->booking = new Booking();
    }

    public function createBooking(int $student_id, array $booking){
        if($this->booking->createBooking($student_id, $booking)){
            return json_encode([
                "status" => true,
                "message" => "Booking created successfully",
                "data" => $booking
            ], flags: 201);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to create booking"
            ], flags: 500);
        }
    }

    public function getBookingById($booking_id){
        $booking = $this->booking->getBookingById($booking_id);
        if($booking ){
            return json_encode([
                "status" => true,
                "booking" => $booking
            ], 200);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Booking not found"
            ], 404);
        }
    }

    // Get bookings By Student Id
    public function getBookingsByStudentId($student_id){
        $bookings = $this->booking->getBookingsByStudentId($student_id);
        if($bookings){
            return json_encode([
                "status" => true,
                "studentBookings" => $bookings
            ], 200);
        }else{
            return json_encode([
                "status" => false,
                "message" => "No bookings found"
            ], 404);
        }
    }

    // Get bookings By Hostel Id
    public function getBookingsByHostelId($hostel_id){
        $bookings = $this->booking->getBookingsByHostelId($hostel_id);
        if($bookings){
            return json_encode([
                "status" => true,
                "hostelBookings" => $bookings
            ], 200);
        }else{
            return json_encode([
                "status" => false,
                "message" => "No bookings found"
            ], 404);
        }
    }

    public function updateBooking($id, $booking){
        if($this->booking->updateBooking($id, $booking)){
            return json_encode(value: [
                "status" => true,
                "message" => "Booking updated successfully"
            ], flags: 201);
        }else{
            return json_encode(value: [
                "status" => false,
                "message" => "Failed to update booking"
            ], flags: 500);
        }
    }

    // Cancel Booking
    public function cancelBooking($booking_id){
        if($this->booking->cancelBooking($booking_id)){
            return json_encode([
                "status" => true,
                "message" => "Booking cancelled successfully"
            ], 201);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to cancel booking"
            ], 500);
        }
    }

    public function deleteBooking($booking_id){
        if($this->booking->deleteBooking($booking_id)){
            return json_encode([
                "status" => true,
                "message" => "Booking deleted successfully"
            ], 201);
        }else{
            return json_encode([
                "status" => false,
                "message" => "Failed to delete booking"
            ], 500);
        }
    }
}