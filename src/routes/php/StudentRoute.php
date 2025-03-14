<?php

namespace App\Route;

use App\Controller\StudentController;

return function ($app) {
    $studentController = new StudentController();

    // Route to get all students
    $app->get('/api/students', function ($request, $response) use ($studentController) {
        $result = $studentController->getAllStudents();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to get a student by ID
    $app->get('/api/student/{id:[0-9]+}', function ($request, $response, $args) use ($studentController) {
        $id = $args['id'];
        $result = $studentController->getStudentById($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to create a new student
    $app->post('/api/student', function ($request, $response) use ($studentController) {
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $studentController->createStudent($data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to update a student by ID
    $app->patch('/api/student/{id}', function ($request, $response, $args) use ($studentController) {
        $id = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $studentController->updateStudent($id, $data);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Route to delete a student by ID
    $app->delete('/api/student/{id}', function ($request, $response, $args) use ($studentController) {
        $id = $args['id'];
        $result = $studentController->deleteStudent($id);
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    });
};
