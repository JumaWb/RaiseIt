<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'verification_email.php';

$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (empty($email)) {
    $_SESSION['error'] = "Email is required.";
    header("Location: login.php");
    exit();
}

try {
    // Get user by email
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception("User not found.");
    }
    
    // Check if user is already verified
    $stmt = $conn->prepare("SELECT email_verified FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $isVerified = $stmt->fetchColumn();
    
    if ($isVerified) {
        $_SESSION['error'] = "Email is already verified.";
        header("Location: login.php");
        exit();
    }
    
    // Check if user has requested too many verification emails
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM email_verifications 
        WHERE user_id = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$user['id']]);
    $requestCount = $stmt->fetchColumn();
    
    if ($requestCount >= 3) {
        throw new Exception("Too many verification attempts. Please try again later.");
    }
    
    // Process new verification email
    $emailSent = processEmailVerification($conn, $user['id'], $email, $user['full_name']);
    
    if (!$emailSent) {
        throw new Exception("Failed to send verification email. Please try again later.");
    }
    
    $_SESSION['success'] = "Verification email has been sent. Please check your inbox.";
    header("Location: verify.php?email=" . urlencode($email));
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: verify.php?email=" . urlencode($email));
    exit();
}
?>