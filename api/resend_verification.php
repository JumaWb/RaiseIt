<?php
require_once 'includes/db_connect.php';
require_once 'verification_email.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Get JSON input from React
$data = json_decode(file_get_contents("php://input"), true);

// Validate request
$email = trim($data["email"] ?? "");

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is required"]);
    exit();
}

try {
    // Get user by email
    $stmt = $conn->prepare("SELECT id, full_name, email_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit();
    }

    if ($user["email_verified"]) {
        echo json_encode(["status" => "error", "message" => "Email is already verified"]);
        exit();
    }

    // Check if user has requested too many verification emails
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM email_verifications 
        WHERE user_id = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$user["id"]]);
    $requestCount = $stmt->fetchColumn();

    if ($requestCount >= 3) {
        echo json_encode(["status" => "error", "message" => "Too many verification attempts. Please try again later."]);
        exit();
    }

    // Process new verification email
    $emailSent = processEmailVerification($conn, $user["id"], $email, $user["full_name"]);

    if (!$emailSent) {
        echo json_encode(["status" => "error", "message" => "Failed to send verification email. Please try again later."]);
        exit();
    }

    echo json_encode(["status" => "success", "message" => "Verification email sent. Check your inbox."]);
    exit();

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit();
}
?>
