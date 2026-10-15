<?php
session_start();

header('Content-Type: application/json');

// Establish connection to MySQL server on port 3307
$data = mysqli_connect("localhost", "root", "", "collegepr", 3307);

if($data === false) {
    echo json_encode(["status" => "error", "message" => "Database connection error!"]);
    exit;
}

if(isset($_POST['apply']) || isset($_POST['name'])) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // 1. Full Name Validation
    if(empty($name)) {
        echo json_encode(["status" => "error", "message" => "Please enter your full name."]);
        exit;
    }
    if(strlen($name) < 2 || strlen($name) > 60) {
        echo json_encode(["status" => "error", "message" => "Full name must be between 2 and 60 characters."]);
        exit;
    }
    if(!preg_match("/^[a-zA-Z\s\.\']+$/", $name)) {
        echo json_encode(["status" => "error", "message" => "Full name can only contain letters, spaces, and periods."]);
        exit;
    }

    // 2. Email Validation (Strict RFC 5322 & mandatory domain TLD check)
    if(empty($email)) {
        echo json_encode(["status" => "error", "message" => "Please enter your email address."]);
        exit;
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $email)) {
        echo json_encode(["status" => "error", "message" => "Please enter a valid email address with a domain (e.g. name@example.com)."]);
        exit;
    }

    // 3. Phone Number Validation (10 digits starting with 6, 7, 8, or 9; reject repeating dummy digits)
    if(empty($phone)) {
        echo json_encode(["status" => "error", "message" => "Please enter your mobile phone number."]);
        exit;
    }
    if(!preg_match('/^[6-9]\d{9}$/', $phone)) {
        echo json_encode(["status" => "error", "message" => "Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9."]);
        exit;
    }
    if(preg_match('/^(\d)\1{9}$/', $phone)) {
        echo json_encode(["status" => "error", "message" => "Invalid mobile number: all digits cannot be identical (e.g. 1111111111)."]);
        exit;
    }

    // 4. Statement of Purpose / Message Validation (Must be meaningful, min 10 chars)
    if(empty($message)) {
        echo json_encode(["status" => "error", "message" => "Please provide a statement of purpose or academic introduction."]);
        exit;
    }
    if(strlen($message) < 10) {
        echo json_encode(["status" => "error", "message" => "Statement of purpose must be at least 10 characters long (cannot be a single character like 'w')."]);
        exit;
    }

    // 5. Sanitization & Safe Database Insertion
    $data_name = mysqli_real_escape_string($data, $name);
    $data_email = mysqli_real_escape_string($data, $email);
    $data_phone = mysqli_real_escape_string($data, $phone);
    $data_message = mysqli_real_escape_string($data, $message);

    $sql = "INSERT INTO admission(name, email, phone, message) VALUES('$data_name', '$data_email', '$data_phone', '$data_message')";
    $result = mysqli_query($data, $sql);

    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_POST['ajax']);

    if($result) {
        if($is_ajax) {
            echo json_encode(["status" => "success", "message" => "Your application has been received successfully! Our admissions committee will contact you."]);
        } else {
            $_SESSION['message'] = "Your application has been received successfully!";
            header("location:index.php#admission");
        }
    } else {
        if($is_ajax) {
            echo json_encode(["status" => "error", "message" => "Database insertion failed. Please try again."]);
        } else {
            $_SESSION['message'] = "Apply Failed!";
            header("location:index.php#admission");
        }
    }
    exit;
}
