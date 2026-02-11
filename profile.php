<?php
session_start();
include("db_connection.php");
include("function.php");

// Check if user is logged in
if (!isLogedIn()) {
    header("Location: login.php?message=Please login first");
    exit;
}

$user = getCurrentUser();
$user_id = $user['user_id'];
$success_msg = '';
$error_msg = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Update Email
    if ($action == 'update_email') {
        $new_email = trim($_POST['email'] ?? '');
        
        if (empty($new_email)) {
            $error_msg = "Email cannot be empty!";
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Invalid email format!";
        } else {
            // Check if email already exists
            $email_check = "SELECT user_id FROM users WHERE email = '$new_email' AND user_id != $user_id";
            $result = mysqli_query($conn, $email_check);
            
            if (mysqli_num_rows($result) > 0) {
                $error_msg = "This email is already in use!";
            } else {
                $new_email_escaped = mysqli_real_escape_string($conn, $new_email);
                $update_sql = "UPDATE users SET email = '$new_email_escaped' WHERE user_id = $user_id";
                
                if (mysqli_query($conn, $update_sql)) {
                    $_SESSION['email'] = $new_email;
                    $success_msg = "Email updated successfully!";
                } else {
                    $error_msg = "Error updating email: " . mysqli_error($conn);
                }
            }
        }
    }
    
    // Update Password
    if ($action == 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Get current password from database
        $pass_check = "SELECT password FROM users WHERE user_id = $user_id";
        $pass_result = mysqli_query($conn, $pass_check);
        $pass_row = mysqli_fetch_assoc($pass_result);
        
        if (!password_verify($current_password, $pass_row['password'])) {
            $error_msg = "Current password is incorrect!";
        } elseif (empty($new_password)) {
            $error_msg = "New password cannot be empty!";
        } elseif (strlen($new_password) < 6) {
            $error_msg = "Password must be at least 6 characters long!";
        } elseif ($new_password !== $confirm_password) {
            $error_msg = "New passwords do not match!";
        } elseif ($new_password === $current_password) {
            $error_msg = "New password must be different from current password!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $hashed_escaped = mysqli_real_escape_string($conn, $hashed_password);
            $update_sql = "UPDATE users SET password = '$hashed_escaped' WHERE user_id = $user_id";
            
            if (mysqli_query($conn, $update_sql)) {
                $success_msg = "Password updated successfully!";
            } else {
                $error_msg = "Error updating password: " . mysqli_error($conn);
            }
        }
    }
}

// Get current user info
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_sql);
$current_user = mysqli_fetch_assoc($user_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - wazoForum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            margin: 0 auto 15px;
            border: 3px solid white;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-role {
            font-size: 14px;
            opacity: 0.9;
            text-transform: capitalize;
            font-weight: 500;
        }

        .profile-content {
            padding: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            display: block;
        }

        .info-value {
            font-size: 15px;
            color: #333;
            padding: 8px 12px;
            background: #f5f5f5;
            border-radius: 4px;
            word-break: break-all;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        button {
            padding: 12px 24px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-cancel {
            background: #eee;
            color: #333;
        }

        .btn-cancel:hover {
            background: #ddd;
        }

        .btn-back {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-back:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }

        .form-divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
            color: #999;
        }

        .form-divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }

        .form-divider span {
            position: relative;
            background: white;
            padding: 0 10px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px auto;
            }

            .profile-header {
                padding: 30px 15px;
            }

            .profile-content {
                padding: 20px;
            }

            .profile-name {
                font-size: 20px;
            }

            .btn-group {
                flex-direction: column;
            }

            button,
            .btn-back {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($current_user['username'], 0, 1)); ?>
            </div>
            <div class="profile-name"><?php echo htmlspecialchars($current_user['username']); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($current_user['role']); ?> Account</div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Messages -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <span><?php echo htmlspecialchars($success_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <!-- Account Information Section -->
            <div class="form-section">
                <h3>Account Information</h3>
                <div class="info-item">
                    <span class="info-label">Username</span>
                    <div class="info-value"><?php echo htmlspecialchars($current_user['username']); ?></div>
                </div>
                <div class="info-item">
                    <span class="info-label">Member Since</span>
                    <div class="info-value"><?php echo date('F j, Y', strtotime($current_user['created_at'])); ?></div>
                </div>
            </div>

            <!-- Update Email Section -->
            <div class="form-section">
                <h3>Update Email</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update_email">
                    <div class="form-group">
                        <label for="current_email">Current Email</label>
                        <div class="info-value" style="background: #f5f5f5; padding: 12px 15px;">
                            <?php echo htmlspecialchars($current_user['email']); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">New Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your new email" required>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn-update">Update Email</button>
                    </div>
                </form>
            </div>

            <!-- Update Password Section -->
            <div class="form-section">
                <h3>Change Password</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" placeholder="Enter your current password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn-update">Change Password</button>
                    </div>
                </form>
            </div>

            <!-- Back Button -->
            <a href="index.php" class="btn-back">Back to Forum</a>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>
