<?php
// Include database connection
require_once __DIR__ . '/../config/Database.php';

// Set response header for JSON
header('Content-Type: application/json');

try {
    // Create database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Get JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data is received
    if (!$data) {
        throw new Exception('No data received.');
    }

    // Validate data (basic validation, you should add more robust validation)
    if (empty($data['email']) || empty($data['password']) || empty($data['firstname']) || empty($data['lastname']) || empty($data['gender']) || empty($data['phone']) || empty($data['school']) || empty($data['year'])) {
        throw new Exception('Missing required fields.');
    }

    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $firstname = htmlspecialchars($data['firstname'], ENT_QUOTES, 'UTF-8');
    $lastname = htmlspecialchars($data['lastname'], ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars($data['gender'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8');
    $schoolId = filter_var($data['school'], FILTER_SANITIZE_NUMBER_INT);
    $yearOfStudy = htmlspecialchars($data['year'], ENT_QUOTES, 'UTF-8');
    $studentId = isset($data['studentId']) ? htmlspecialchars($data['studentId'], ENT_QUOTES, 'UTF-8') : null;
    $schoolName = isset($data['schoolName']) ? htmlspecialchars($data['schoolName'], ENT_QUOTES, 'UTF-8') : null; // Added school name


    // Check if email already exists
    $checkEmailStmt = $conn->prepare("SELECT id FROM student WHERE email = :email");
    $checkEmailStmt->bindParam(':email', $email);
    $checkEmailStmt->execute();

    if ($checkEmailStmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('Email already exists.');
    }

    // Insert student data into the database
    $insertStudentStmt = $conn->prepare("INSERT INTO student (email, password, firstname, lastname, gender, phone, school_id, student_id, year_of_study) 
                                     VALUES (:email, :password, :firstname, :lastname, :gender, :phone, :school_id, :student_id, :year_of_study)");
    $insertStudentStmt->bindParam(':email', $email);
    $insertStudentStmt->bindParam(':password', $password);
    $insertStudentStmt->bindParam(':firstname', $firstname);
    $insertStudentStmt->bindParam(':lastname', $lastname);
    $insertStudentStmt->bindParam(':gender', $gender);
    $insertStudentStmt->bindParam(':phone', $phone);
    $insertStudentStmt->bindParam(':school_id', $schoolId);
    $insertStudentStmt->bindParam(':student_id', $studentId);
    $insertStudentStmt->bindParam(':year_of_study', $yearOfStudy);

    if ($insertStudentStmt->execute()) {
       $studentId = $conn->lastInsertId();

        //If the school is a new school, add to the school table
        if($schoolName != null){
            $checkSchoolStmt = $conn->prepare("SELECT id FROM school WHERE name = :schoolName");
            $checkSchoolStmt->bindParam(':schoolName', $schoolName);
            $checkSchoolStmt->execute();
            $existingSchool = $checkSchoolStmt->fetch(PDO::FETCH_ASSOC);

            if(!$existingSchool){
                 $insertSchoolStmt = $conn->prepare("INSERT INTO school (name) VALUES (:schoolName)");
                 $insertSchoolStmt->bindParam(':schoolName', $schoolName);
                 $insertSchoolStmt->execute();
                 $schoolId = $conn->lastInsertId();
            }
            else{
                $schoolId = $existingSchool['id'];
            }

           $updateStudentStmt = $conn->prepare("UPDATE student SET school_id = :school_id WHERE id = :student_id");
           $updateStudentStmt->bindParam(':school_id', $schoolId);
           $updateStudentStmt->bindParam(':student_id', $studentId);
           $updateStudentStmt->execute();
        }
        
        $response = ['success' => true, 'message' => 'Registration successful.'];
    } else {
        throw new Exception('Failed to insert student data.');
    }

    echo json_encode($response);

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
    echo json_encode($response);
} finally {
    // Close the database connection
    if ($conn) {
        $conn = null;
    }
}
?>