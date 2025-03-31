<?php
session_start();
require_once 'includes/db_connect.php';

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/**
 * Generate a random reset token
 * @return string
 */
function generateResetToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Save reset token in database
 * @param PDO 
 * @param int 
 * @param string 
 * @return bool 
 */
function saveResetToken($conn, $userId, $token) {
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Insert token into database
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, reset_token, expires_at) VALUES (?, ?, ?)");
    return $stmt->execute([$userId, $token, $expiresAt]);
}

/**
 * Send password reset email using PHPMailer
 * @param string 
 * @param string
 * @param string 
 * @return bool 
 */
function sendResetEmail($recipientEmail, $recipientName, $resetToken) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST'); 
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER'); 
        $mail->Password   = getenv('SMTP_PASS'); 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = getenv('SMTP_PORT');
        
        // Recipients
        $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));
        $mail->addAddress($recipientEmail, $recipientName);
        
        // Build reset URL
        $resetUrl = 'https://raiseit.com/reset_password.php?token=' . urlencode($resetToken);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .button { display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; 
                              text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .note { font-size: 12px; color: #6c757d; margin-top: 20px; }
                    .footer { font-size: 12px; text-align: center; margin-top: 30px; color: #6c757d; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Password Reset Request</h2>
                    </div>
                    <div class="content">
                        <p>Hello ' . htmlspecialchars($recipientName) . ',</p>
                        <p>We received a request to reset your password. If you didn\'t make this request, you can ignore this email.</p>
                        <p>To reset your password, click on the button below:</p>
                        <p><a href="' . $resetUrl . '" class="button">Reset Password</a></p>
                        <p class="note">This link will expire in 1 hour for security reasons.</p>
                        <p class="note">If the button doesn\'t work, copy and paste this URL into your browser:</p>
                        <p class="note">' . $resetUrl . '</p>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' ' . getenv('SMTP_FROM_NAME') . '. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ';
        $mail->AltBody = "Hello " . $recipientName . ",\n\n" .
                         "We received a request to reset your password. If you didn't make this request, you can ignore this email.\n\n" .
                         "To reset your password, visit this link: " . $resetUrl . "\n\n" .
                         "This link will expire in 1 hour for security reasons.\n\n" .
                         getenv('SMTP_FROM_NAME');

        return $mail->send();
    } catch (Exception $e) {
        error_log("Reset email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}


// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    
    if (empty($email)) {
        $_SESSION['error'] = "Email is required.";
        header("Location: forgot_password.html");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: forgot_password.html");
        exit();
    }
    
    try {
        $stmt = $conn->prepare("SELECT id, full_name, email_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        
        if (!$user) {
            $_SESSION['success'] = "If your email is registered, you will receive password reset instructions shortly.";
            header("Location: forgot_password.html");
            exit();
        }
        
        // Check if email is verified
        if (!$user['email_verified']) {
            $_SESSION['error'] = "Please verify your email address before resetting your password.";
            header("Location: verify.php?email=" . urlencode($email));
            exit();
        }
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM password_resets 
            WHERE user_id = ? 
            AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute([$user['id']]);
        $requestCount = $stmt->fetchColumn();
        
        if ($requestCount >= 3) {
            $_SESSION['error'] = "Too many password reset attempts. Please try again later.";
            header("Location: forgot_password.html");
            exit();
        }
        
        // Generate and save reset token
        $resetToken = generateResetToken();
        $tokenSaved = saveResetToken($conn, $user['id'], $resetToken);
        
        if (!$tokenSaved) {
            throw new Exception("Failed to process reset request.");
        }
        
        // Send reset email
        $emailSent = sendResetEmail($user['email'], $user['full_name'], $resetToken);
        
        if (!$emailSent) {
            throw new Exception("Failed to send reset email.");
        }
        
        $_SESSION['success'] = "Password reset instructions have been sent to your email.";
        header("Location: login.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: forgot_password.html");
        exit();
    }
}
?>