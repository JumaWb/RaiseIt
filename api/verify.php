<?php

session_start();
require_once 'includes/db_connect.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';

// Clear session messages
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $otp = trim($_POST["otp"]);
    
    if (empty($email) || empty($otp)) {
        $_SESSION['error'] = "Email and verification code are required.";
        header("Location: verify.php?email=" . urlencode($email));
        exit();
    }
    
    try {
        // Get user by email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception("User not found.");
        }
        
        $userId = $user['id'];
        
        $stmt = $conn->prepare("
            SELECT id 
            FROM email_verifications 
            WHERE user_id = ? 
            AND otp_code = ? 
            AND expires_at > NOW() 
            AND used = FALSE
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$userId, $otp]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$verification) {
            throw new Exception("Invalid or expired verification code.");
        }
        
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("UPDATE email_verifications SET used = TRUE WHERE id = ?");
        $stmt->execute([$verification['id']]);
        
        $stmt = $conn->prepare("UPDATE users SET email_verified = TRUE WHERE id = ?");
        $stmt->execute([$userId]);
        
        $conn->commit();
        
        $_SESSION['success'] = "Email verified successfully! You can now log in.";
        header("Location: login.php");
        exit();
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: verify.php?email=" . urlencode($email));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <p>Please enter the verification code sent to your email.</p>
        
        <form action="verify.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="otp">Verification Code:</label>
                <input type="text" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}" required>
            </div>
            
            <button type="submit" class="btn">Verify Email</button>
        </form>
        
        <div class="links">
            <a href="resend_verification.php?email=<?php echo urlencode($email); ?>">Resend Verification Code</a>
            <span>|</span>
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>