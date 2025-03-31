<?php
session_start();
require_once 'includes/db_connect.php';

// Verify token is provided
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    $_SESSION['error'] = "Invalid password reset link.";
    header("Location: login.php");
    exit();
}

// Validate token before showing form
try {
    // Check if token exists and is not expired
    $stmt = $conn->prepare("
        SELECT pr.id, pr.user_id, u.email
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.reset_token = ?
        AND pr.expires_at > NOW()
    ");
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resetRequest) {
        $_SESSION['error'] = "Invalid or expired password reset link.";
        header("Location: login.php");
        exit();
    }
    
    $_SESSION['reset_email'] = $resetRequest['email'];
    
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred. Please try again.";
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $token = $_POST["token"];
    
    // Validate input
    if (empty($password) || empty($confirm_password) || empty($token)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }
    
    try {
        // Verify token again
        $stmt = $conn->prepare("
            SELECT pr.id, pr.user_id, u.email
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.reset_token = ?
            AND pr.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resetRequest) {
            $_SESSION['error'] = "Invalid or expired password reset link.";
            header("Location: login.php");
            exit();
        }
        
        // Verify email hasn't changed since token verification
        if (!isset($_SESSION['reset_email']) || $_SESSION['reset_email'] !== $resetRequest['email']) {
            $_SESSION['error'] = "Session validation failed. Please try again.";
            header("Location: login.php");
            exit();
        }
        
        // Hash the new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Begin transaction
        $conn->beginTransaction();
        
        // Update user's password
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $passwordUpdated = $stmt->execute([$password_hash, $resetRequest['user_id']]);
        
        if (!$passwordUpdated) {
            throw new Exception("Failed to update password.");
        }
        
        // Invalidate all reset tokens for this user
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$resetRequest['user_id']]);
        
        // Commit transaction
        $conn->commit();
        
        // Clear reset email from session
        unset($_SESSION['reset_email']);
        
        $_SESSION['success'] = "Your password has been successfully reset. You can now log in with your new password.";
        header("Location: login.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }
}

// If not submitting form, display the reset form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorMsg = document.getElementById('password_error');
            
            // Password strength validation
            if (password.length < 8) {
                errorMsg.textContent = "Password must be at least 8 characters long.";
                return false;
            }
            
            // Password match validation
            if (password !== confirmPassword) {
                errorMsg.textContent = "Passwords do not match.";
                return false;
            }
            
            errorMsg.textContent = "";
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <p>Please enter your new password below.</p>
        
        <form action="reset_password.php" method="post" onsubmit="return validateForm()">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                <div id="password_error" class="error-text"></div>
            </div>
            
            <button type="submit" class="btn">Reset Password</button>
        </form>
        
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>