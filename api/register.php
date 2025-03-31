<?php
session_start(); 
require_once 'includes/db_connect.php';
require_once 'verification_email.php'; 

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests (CORS)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Get JSON input from React
$data = json_decode(file_get_contents("php://input"), true);

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

// Get input values from React
$full_name = trim($data["full_name"] ?? "");
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";
$confirm_password = $data["confirm_password"] ?? "";
$role = "client";  

// Validate input
if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit();
}

if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit();
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Get role ID 
$stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
$stmt->execute([$role]);
$role_id = $stmt->fetchColumn();

if (!$role_id) {
    echo json_encode(["status" => "error", "message" => "Role not found"]);
    exit();
}

try {
    // Begin transaction
    $conn->beginTransaction();
    
    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role_id, email_verified) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$full_name, $email, $password_hash, $role_id, 0]); // Changed false to 0
    
    if (!$result) {
        throw new Exception("Failed to register user");
    }
    
    // Get the new user ID
    $userId = $conn->lastInsertId();
    
    // Send verification email
    $emailSent = processEmailVerification($conn, $userId, $email, $full_name);
    
    if (!$emailSent) {
        throw new Exception("Failed to send verification email");
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(["status" => "success", "message" => "Registration successful! Please check your email for verification instructions."]);
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit();
}
?>
