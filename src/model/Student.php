<?php

namespace App\Model;

use App\Helpers\Database;

class Student
{
    private $conn;
    private $table_name = "students";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new student
    public function createStudent($student)
    {
        $query = "INSERT INTO students (firstName, lastName, email, phone, gender) VALUE (:firstName, :lastName, :email, :phone, :gender)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':firstName', $student['firstName']);
        $stmt->bindParam(':lastName', $student['lastName']);
        $stmt->bindParam(':email', $student['email']);
        $stmt->bindParam(':phone', $student['phone']);
        $stmt->bindParam(':gender', $student['gender']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public function getAllStudents(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $students;
    }

    public function getStudentById($id){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}


