<?php

namespace App\Model;

use App\Config\Database;
use PDO;
use PDOException;

class Booking
{
    private $conn;
    private $table_name = "booking";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Generate a random alphanumerical booking code of a given length.
     *
     * @param int $length The length of the booking code. Default is 10.
     * @return string The generated booking code.
     */
    private function generateBookingCode($length = 10): string
    {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(min: 0, max: strlen(string: $characters) - 1)];
            }

            // Check if code exists
            $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE booking_code = :code";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            $exists = (bool)$stmt->fetchColumn();

        } while ($exists);

        return $code;
    }

    /**
     * Create a new booking.
     *
     * Expected keys in $booking:
     * - student_id: int
     * - room_id: int
     * - hostel_id: int
     * - start_date: string (YYYY-MM-DD format)
     * - end_date: string (YYYY-MM-DD format)
     * - status: string (optional, default 'pending')
     * - booking_code: string (optional, generated if not provided)
     * - student_name: string
     * - student_email: string
     * - contact: string
     * - gender: string
     * - program: string
     * - emergency_contact_name: string
     * - emergency_contact_number: string
     * - booking_date: string (optional, default current datetime)
     *
     * @param array $booking Associative array with booking details.
     * @return bool Returns true on success, false on failure.
     */
    public function createBooking(int $student_id, array $booking): bool
    {
        // Generate booking code
        $booking_code = $this->generateBookingCode();

        // Set booking_date to current datetime if not provided.
        $booking_date = date('Y-m-d H:i:s');

        $query = "INSERT INTO " . $this->table_name . " (
                        student_id,
                        room_id,
                        hostel_id,
                        start_date,
                        end_date,
                        status,
                        booking_code,
                        student_name,
                        student_email,
                        contact,
                        gender,
                        program,
                        emergency_contact_name,
                        emergency_contact_number,
                        booking_date
                  ) VALUES (
                        :student_id,
                        :room_id,
                        :hostel_id,
                        :start_date,
                        :end_date,
                        :status,
                        :booking_code,
                        :student_name,
                        :student_email,
                        :contact,
                        :gender,
                        :program,
                        :emergency_contact_name,
                        :emergency_contact_number,
                        :booking_date
                  )";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindValue(':room_id', $booking['room_id'], PDO::PARAM_INT);
        $stmt->bindValue(':hostel_id', $booking['hostel_id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $booking['start_date'], PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $booking['end_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $booking['status'] ?? 'pending', PDO::PARAM_STR);
        $stmt->bindValue(':booking_code', $booking_code, PDO::PARAM_STR);
        $stmt->bindValue(':student_name', $booking['student_name'], PDO::PARAM_STR);
        $stmt->bindValue(':student_email', $booking['student_email'], PDO::PARAM_STR);
        $stmt->bindValue(':contact', $booking['contact'], PDO::PARAM_STR);
        $stmt->bindValue(':gender', $booking['gender'], PDO::PARAM_STR);
        $stmt->bindValue(':program', $booking['program'], PDO::PARAM_STR);
        $stmt->bindValue(':emergency_contact_name', $booking['emergency_contact_name'], PDO::PARAM_STR);
        $stmt->bindValue(':emergency_contact_number', $booking['emergency_contact_number'], PDO::PARAM_STR);
        $stmt->bindValue(':booking_date', $booking_date, PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error if needed, e.g. using Monolog: $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve a booking by its ID.
     *
     * @param int $id The booking ID.
     * @return mixed Returns an associative array of booking data or false if not found.
     */
    public function getBookingById($booking_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE booking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all bookings for a specific student.
     *
     * @param int $student_id The student ID.
     * @return array Returns an array of bookings.
     */
    public function getBookingsByStudentId($student_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE student_id = :student_id ORDER BY booking_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all bookings of a specific hostel.
     *
     * @param int $hostel_id The hostel ID.
     * @return array Returns an array of all booking records for the hostel.
     */
    public function getBookingsByHostelId($hostel_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE hostel_id = :hostel_id ORDER BY booking_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update an existing booking.
     *
     * @param int $id The booking ID.
     * @param array $booking An associative array of updated booking details.
     * @return bool Returns true on success, false on failure.
     */
    public function updateBooking($id, array $booking): bool
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                      room_id = :room_id,
                      hostel_id = :hostel_id,
                      start_date = :start_date,
                      end_date = :end_date,
                      status = :status,
                      student_name = :student_name,
                      student_email = :student_email,
                      contact = :contact,
                      gender = :gender,
                      program = :program,
                      emergency_contact_name = :emergency_contact_name,
                      emergency_contact_number = :emergency_contact_number
                  WHERE booking_id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':room_id', $booking['room_id'], PDO::PARAM_INT);
        $stmt->bindValue(':hostel_id', $booking['hostel_id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $booking['start_date'], PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $booking['end_date'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $booking['status'], PDO::PARAM_STR);
        $stmt->bindValue(':student_name', $booking['student_name'], PDO::PARAM_STR);
        $stmt->bindValue(':student_email', $booking['student_email'], PDO::PARAM_STR);
        $stmt->bindValue(':contact', $booking['contact'], PDO::PARAM_STR);
        $stmt->bindValue(':gender', $booking['gender'], PDO::PARAM_STR);
        $stmt->bindValue(':program', $booking['program'], PDO::PARAM_STR);
        $stmt->bindValue(':emergency_contact_name', $booking['emergency_contact_name'], PDO::PARAM_STR);
        $stmt->bindValue(':emergency_contact_number', $booking['emergency_contact_number'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error
            return false;
        }
    }

    /**
     * Cancel a booking by updating its status to 'cancelled'.
     *
     * @param int $id The booking ID.
     * @return bool Returns true on success, false on failure.
     */
    public function cancelBooking($id): bool
    {
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelled' WHERE booking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error
            return false;
        }
    }

    /**
     * Delete a booking by its ID.
     *
     * @param int $id The booking ID.
     * @return bool Returns true on success, false on failure.
     */
    public function deleteBooking($id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE booking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Optionally log error
            return false;
        }
    }
}