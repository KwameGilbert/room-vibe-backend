<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    // Get the current student ID 
    // header("Location: ./splash.php");
    // exit();
}