<?php
require_once 'config/db_connect.php';
require_once 'env_loader.php';
// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Generate a random 6-digit OTP code
 * @return string
 */
function generateOTP() {
    return sprintf("%06d", mt_rand(100000, 999999));
}

/**
 * Save verification OTP in database
 * @param PDO 
 * @param int 
 * @param string 
 * @return bool 
 */
function saveVerificationOTP($conn, $userId, $otpCode) {
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $stmt = $conn->prepare("INSERT INTO email_verifications (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
    return $stmt->execute([$userId, $otpCode, $expiresAt]);
}

/**
 * Send verification email using PHPMailer
 * @param string 
 * @param string 
 * @param string 
 * @return bool 
 */
function sendVerificationEmail($recipientEmail, $recipientName, $otpCode) {
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
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body    = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .code { font-size: 24px; font-weight: bold; background-color: #f1f1f1; padding: 10px; text-align: center; letter-spacing: 5px; }
                    .footer { font-size: 12px; text-align: center; margin-top: 30px; color: #6c757d; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Verify Your Email Address</h2>
                    </div>
                    <div class="content">
                        <p>Hello ' . htmlspecialchars($recipientName) . ',</p>
                        <p>Thank you for registering. Please use the following verification code to verify your email address:</p>
                        <div class="code">' . $otpCode . '</div>
                        <p>This code will expire in 24 hours.</p>
                        <p>If you did not request this verification, please ignore this email.</p>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' ' . getenv('SMTP_FROM_NAME') . '. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ';
        $mail->AltBody = "Hello " . $recipientName . ",\n\nYour email verification code is: " . $otpCode . "\n\nThis code will expire in 24 hours.\n\nIf you did not request this verification, please ignore this email.";
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}


/**
 * Main function to handle the email verification process
 * @param PDO 
 * @param int 
 * @param string 
 * @param string 
 * @return bool 
 */
function processEmailVerification($conn, $userId, $userEmail, $userName) {
    $otpCode = generateOTP();
    

    $saved = saveVerificationOTP($conn, $userId, $otpCode);
    
    if (!$saved) {
        return false;
    }
    
    return sendVerificationEmail($userEmail, $userName, $otpCode);
}
?>