<?php
session_start();

header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

try {
    require_once 'config/db_connect.php';
    require_once 'verification_email.php';
    
    $data = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid request format");
    }
    
    $full_name = trim($data["full_name"] ?? "");
    $email = trim($data["email"] ?? "");
    $password = $data["password"] ?? "";
    $confirm_password = $data["confirm_password"] ?? "";
    $role = "client";
    
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new Exception("All fields are required");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }
    
    if ($password !== $confirm_password) {
        throw new Exception("Passwords do not match");
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        throw new Exception("Email already registered if you are not sure please check your inbox for the verification link.");
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Get role ID
    $stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
    $stmt->execute([$role]);
    $role_id = $stmt->fetchColumn();
    
    if (!$role_id) {
        // If role not found, create it
        $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
        $stmt->execute([$role]);
        $role_id = $conn->lastInsertId();
    }
    
    $conn->beginTransaction();
    
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role_id, email_verified) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$full_name, $email, $password_hash, $role_id, 0]);
    
    if (!$result) {
        throw new Exception("Failed to register user");
    }
    
    $userId = $conn->lastInsertId();
    
    $emailSent = processEmailVerification($conn, $userId, $email, $full_name);
    
    if (!$emailSent) {
        throw new Exception("Failed to send verification email");
    }
    
    $conn->commit();
    
    echo json_encode([
        "status" => "success", 
        "message" => "Registration successful! Please check your email for verification instructions."
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
}
?>