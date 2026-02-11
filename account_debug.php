<?php
session_start();
include("db_connection.php");
include("function.php");

// Get current user
$user = getCurrentUser();
$is_admin = isLogedIn() && $user && $user['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Debug - E-Forum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f5f7fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .section h2 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 600;
            color: #333;
        }

        .value {
            color: #666;
            word-break: break-all;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }

        .status-admin {
            background: #fff3cd;
            color: #856404;
        }

        .status-member {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-offline {
            background: #f8d7da;
            color: #721c24;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-color: #4caf50;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f0f2f5;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e4e6eb;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .code-block {
            background: #222;
            color: #0f0;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            margin-top: 10px;
        }

        .checkmark {
            color: #4caf50;
            font-weight: bold;
            font-size: 18px;
        }

        .cross {
            color: #f44336;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container">
        <h1>Account Status Check</h1>

        <?php if (isLogedIn()): ?>
            <?php if ($is_admin): ?>
                <div class="alert alert-success">
                    <strong>Admin Verified!</strong> You have admin privileges. You should see the purple "Admin Panel" button in your header.
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Not an Admin</strong> Your account is registered as a regular member. Only admins can access the admin panel.
                </div>
            <?php endif; ?>

            <div class="section">
                <h2>Your Account Details</h2>
                <div class="info-item">
                    <span class="label">User ID:</span>
                    <span class="value"><?php echo htmlspecialchars($user['user_id'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Username:</span>
                    <span class="value"><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Role:</span>
                    <span class="value">
                        <span class="status <?php echo $user['role'] === 'admin' ? 'status-admin' : 'status-member'; ?>">
                            <?php echo ucfirst(htmlspecialchars($user['role'] ?? 'unknown')); ?>
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Login Status:</span>
                    <span class="value">
                        <span class="status status-admin">LOGGED IN</span>
                    </span>
                </div>
            </div>

            <div class="section">
                <h2>System Status</h2>
                <div class="info-item">
                    <span class="label">IsLogedIn() Function:</span>
                    <span class="value">
                        <span class="checkmark"></span> Working
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">GetCurrentUser() Function:</span>
                    <span class="value">
                        <span class="checkmark"></span> Working
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Session Role Stored:</span>
                    <span class="value">
                        <?php if (isset($_SESSION['role'])): ?>
                            <span class="checkmark"></span> Yes (<?php echo htmlspecialchars($_SESSION['role']); ?>)
                        <?php else: ?>
                            <span class="cross"></span> No
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Admin Check:</span>
                    <span class="value">
                        <?php if ($is_admin): ?>
                            <span class="checkmark"></span> You are admin
                        <?php else: ?>
                            <span class="cross"></span> Not admin
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <div class="section">
                <h2>Next Steps</h2>
                <?php if ($is_admin): ?>
                    <p style="margin-bottom: 15px;">Welcome, Administrator! You now have access to:</p>
                    <ul style="margin-left: 20px; margin-bottom: 15px;">
                        <li>Admin Dashboard</li>
                        <li>Member Management (search, view, delete)</li>
                        <li>Forum Statistics</li>
                        <li>Activity Monitoring</li>
                    </ul>
                    <div class="btn-group">
                        <a href="admin/dashboad.php" class="btn btn-primary">Go to Admin Dashboard</a>
                        <a href="admin/manageuser.php" class="btn btn-primary">Manage Members</a>
                        <a href="index.php" class="btn btn-secondary">← Back to Forum</a>
                    </div>
                <?php else: ?>
                    <p>Your account is set up as a regular member. If you should be an admin, contact us to upgrade your account.</p>
                    <div class="btn-group">
                        <a href="categories.php" class="btn btn-primary">Browse Forum</a>
                        <a href="index.php" class="btn btn-secondary">Back Home</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section" style="background: #f0f2f5; border-left-color: #999;">
                <h2>Session Debug Info</h2>
                <div class="code-block">
$_SESSION contents:<br>
<?php 
    foreach($_SESSION as $key => $value) {
        echo htmlspecialchars("[$key] = " . (is_array($value) ? 'Array' : $value)) . "\n";
    }
?>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <strong>Not Logged In</strong> Please log in to access your account details.
            </div>

            <div class="section">
                <h2>Please Log In</h2>
                <p style="margin-bottom: 20px;">You need to be logged in to view this page.</p>
                <div class="btn-group">
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                    <a href="index.php" class="btn btn-secondary">Back Home</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
