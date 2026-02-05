<?php

include("db_connection.php");
include("function.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wazoForum - Join the Community Discussion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ===== HEADER ===== */
        header {
            background: #667eea;
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8em;
            font-weight: bold;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
            list-style: none;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-login, .btn-register {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid white;
        }

        .btn-login {
            background: transparent;
            color: white;
        }

        .btn-login:hover {
            background: white;
            color: #667eea;
        }

        .btn-register {
            background: white;
            color: #667eea;
        }

        .btn-register:hover {
            background: #f0f0f0;
            transform: scale(1.05);
        }

        /* ===== FOOTER ===== */
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            margin-top: 80px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>

        <div class="header-container">
            <a href="index.php" class="logo">wazaForum</a>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="post.php">Posts</a></li>
            </ul>
             <?php if (isLogedIn()): 
                $current_user = getCurrentUser();
                ?>

            <span style="color:white;">
                <i class="fas fa-user-circle"></i>
                 <?php echo $_SESSION['username']; ?>
            </span>
            <a href="logout.php">Logout</a>
             <?php else: ?>

            <div class="auth-buttons">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
            <?php endif; ?>
        </div>
    </header>