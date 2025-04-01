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
        $mail->Body = '
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                        line-height: 1.5;
                        color: #333333;
                        background-color: #f5f5f5;
                    }
                    
                    .container {
                        max-width: 550px;
                        margin: 0 auto;
                        background: #ffffff;
                        border: 1px solid #e0e0e0;
                        border-radius: 4px;
                        overflow: hidden;
                    }
                    
                    .header {
                        padding: 20px;
                        text-align: left;
                        border-bottom: 1px solid #f1f1f1;
                    }
                    
                    .logo {
                        height: 25px;
                        width: auto;
                    }
                    
                    .content {
                        padding: 30px 25px;
                        text-align: left;
                    }
                    
                    .recipient {
                        text-align: right;
                        font-size: 14px;
                        color: #666666;
                        margin-bottom: 5px;
                    }
                    
                    h1 {
                        color: #292929;
                        font-size: 24px;
                        font-weight: 600;
                        margin-bottom: 15px;
                    }
                    
                    .instructions {
                        color: #666666;
                        font-size: 14px;
                        margin-bottom: 30px;
                    }
                    
                    .verification-code {
                        color: #5c6f7c;
                        font-size: 36px;
                        font-weight: 500;
                        text-align: center;
                        margin: 30px 0;
                    }
                    
                    .privacy-box {
                        border: 1px solid #e0e0e0;
                        border-radius: 4px;
                        padding: 20px;
                        margin: 30px 0;
                        background-color: #fcfcfc;
                    }
                    
                    .privacy-title {
                        display: flex;
                        align-items: center;
                        font-size: 14px;
                        font-weight: 600;
                        margin-bottom: 10px;
                    }
                    
                    .shield-icon {
                        margin-right: 8px;
                        color: #666;
                    }
                    
                    .privacy-text {
                        font-size: 14px;
                        color: #666666;
                        line-height: 1.6;
                    }
                    
                    .privacy-text a {
                        color: #0073b1;
                        text-decoration: none;
                    }
                    
                    .button {
                        display: inline-block;
                        background-color:rgb(225, 235, 240);
                        color: white;
                        text-decoration: none;
                        padding: 12px 24px;
                        border-radius: 24px;
                        font-weight: 600;
                        font-size: 16px;
                        margin: 15px 0;
                        text-align: center;
                    }
                    
                    .footer {
                        padding: 20px 25px;
                        font-size: 12px;
                        color: #666666;
                        border-top: 1px solid #e0e0e0;
                        background-color: #fafafa;
                        text-align: left;
                    }
                    
                    .footer p {
                        margin-bottom: 8px;
                    }
                    
                    .footer a {
                        color: #0073b1;
                        text-decoration: none;
                    }
                    
                    .footer-logo {
                        display: block;
                        margin: 10px 0;
                    }
                    
                    .copyright {
                        margin-top: 10px;
                        font-size: 11px;
                        color: #777777;
                    }
                    
                    @media only screen and (max-width: 550px) {
                        .container {
                            width: 100%;
                            border-radius: 0;
                            border-left: none;
                            border-right: none;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <img src="https://brand.linkedin.com/content/dam/me/business/en-us/amp/brand-site/v2/bg/LI-Logo.svg.original.svg" alt="' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . '" class="logo">
                        <div class="recipient">' . htmlspecialchars($recipientName) . '</div>
                    </div>
                    
                    <div class="content">
                        <h1>Thank you for signing up</h1>
                        <p class="instructions">Enter this code or click the button below to confirm your email.</p>
                        
                        <div class="verification-code">' . htmlspecialchars($otpCode) . '</div>
                        
                        <div class="privacy-box">
                            <div class="privacy-title">
                                <span class="shield-icon">🛡️</span>
                                Your privacy is important
                            </div>
                            <p class="privacy-text">
                                We may send you member updates, upcoming event messages, job suggestions, reminders and promotional messages from us and our partners. You can <a href="' . htmlspecialchars(getenv('APP_URL')) . '/preferences">change your preferences</a> anytime.
                            </p>
                        </div>
                        
                        <a href="' . htmlspecialchars(getenv('APP_URL')) . '/verify?code=' . htmlspecialchars($otpCode) . '&email=' . urlencode(htmlspecialchars($recipientEmail)) . '" class="button">Confirm your email</a>
                    </div>
                    
                    <div class="footer">
                        <p>This email was intended for ' . htmlspecialchars($recipientName) . ' (' . htmlspecialchars($recipientEmail) . ')</p>
                        <p><a href="' . htmlspecialchars(getenv('APP_URL')) . '/learn-more">Learn why we included this</a></p>
                        <p>You are receiving ' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . ' notification emails.</p>
                        <p><a href="' . htmlspecialchars(getenv('APP_URL')) . '/help">Help</a></p>
                        
                        <img src="https://brand.linkedin.com/content/dam/me/business/en-us/amp/brand-site/v2/bg/LI-Logo.svg.original.svg" alt="' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . '" class="footer-logo" width="80">
                        
                        <p class="copyright">
                            © ' . date('Y') . ' ' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . ', ' . htmlspecialchars(getenv('COMPANY_ADDRESS')) . '.<br>
                            ' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . ' and the ' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . ' logo are registered trademarks of ' . htmlspecialchars(getenv('SMTP_FROM_NAME')) . '.
                        </p>
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