<?php
session_start();
require_once 'config/db_connect.php';

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests (CORS)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Validate request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}

// Get JSON input from React
$data = json_decode(file_get_contents("php://input"), true);

// Get input values from React
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit();
}

try {
    // Check if user exists and get their data
    $stmt = $conn->prepare("SELECT u.id, u.full_name, u.password_hash, u.email_verified, r.role_name 
                           FROM users u 
                           JOIN roles r ON u.role_id = r.id 
                           WHERE u.email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user not found or password doesn't match
    if (!$user || !password_verify($password, $user["password_hash"])) {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
        exit();
    }

    // Check if email is verified
    if (!$user["email_verified"]) {
        echo json_encode([
            "status" => "error", 
            "message" => "Please verify your email before logging in. Check your inbox for the verification link."
        ]);
        exit();
    }

    // Generate a secure token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Store token in database
    $stmt = $conn->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user["id"], $token, $expires]);

    // Set session variables
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["full_name"];
    $_SESSION["user_email"] = $email;
    $_SESSION["user_role"] = $user["role_name"];

    // Send response
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "token" => $token,
        "user_name" => $user["full_name"],
        "user_email" => $email,
        "user_role" => $user["role_name"]
    ]);
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    
    echo json_encode([
        "status" => "error", 
        "message" => "Database error occurred. Please try again later."
    ]);
    exit();
}
?>