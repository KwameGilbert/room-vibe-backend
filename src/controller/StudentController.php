<?php

namespace App\Controller;

use App\Model\Student;

class StudentController
{
    private $student;

    public function __construct()
    {
        $this->student = new Student();
    }

    // Create a new student
    public function createStudent($student): bool|string
    {
        if ($this->student->createStudent($student)) {
            return json_encode([
                "status" => true,
                "message" => "Student created successfully.",
                "data" => $student
            ], 201);
        } else {
            return json_encode([
                "message" => "Failed to create student."
            ], 500);
        }
    }

    // Read all students
    public function getAllStudents(): bool|string
    {
        $students = $this->student->getAllStudents();
        if ($students > 0) {
            return json_encode(["allStudents" => $students], 200);
        } elseif ($students == 0) {
            return json_encode(["allStudents" => []], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to fetch all students"
            ], 500);
        }
    }

    // Read a single student by Id
    public function getStudentById($id)
    {
        $student = $this->student->getStudentById($id);
        if ($student > 0) {
            return json_encode([
                "status" => true,
                "student" => $student
            ], 200);
        } else if ($student == 0) {
            return json_encode([
                "status" => false,
                "message" => "Student not found"
            ], 404);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to fetch student"
            ], 500);
        }
    }

    // Update a student
    public function updateStudent($id, $student): bool|string
    {
        if ($this->student->updateStudent($id, $student)) {
            return json_encode([
                "status" => true,
                "message" => "Student updated successfully.",
                "data" => $student
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to update student."
            ], 500);
        }
    }

    // Delete a student
    public function deleteStudent($id): bool|string
    {
        if ($this->student->deleteStudent($id)) {
            return json_encode([
                "status" => true,
                "message" => "Student deleted successfully."
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to delete student."
            ], 500);
        }
    }   

}