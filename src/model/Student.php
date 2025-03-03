<?php

namespace App\Model;

use App\Config\Database;

class Student
{
    private $conn;
    private $table_name = "student";

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

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user;
    }

    public function updateStudent($id, $student)
    {
        $query = "UPDATE " . $this->table_name . " SET firstName = :firstName, lastName = :lastName, email = :email, phone = :phone, gender = :gender WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':firstName', $student['firstName']);
        $stmt->bindParam(':lastName', $student['lastName']);
        $stmt->bindParam(':email', $student['email']);
        $stmt->bindParam(':phone', $student['phone']);
        $stmt->bindParam(':gender', $student['gender']);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Delete a Student
    public function deleteStudent($id){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function searchStudent($search)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE firstName LIKE :search OR lastName LIKE :search OR email LIKE :search OR phone LIKE :search";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $students;
    }

}


