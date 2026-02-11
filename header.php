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
            align-items: center;
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

        .btn-admin-panel {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            border: 2px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }

        .btn-admin-panel:hover {
            background: linear-gradient(135deg, #ff5252 0%, #dd4e42 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        }

        /* ===== USER PROFILE SECTION ===== */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
        }

        .user-name {
            color: white;
            font-weight: 500;
            font-size: 14px;
        }

        .btn-logout {
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: white;
            color: #667eea;
            border: none;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-logout:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-logout:active {
            transform: translateY(0);
        }

        /* ===== FOOTER ===== */
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            margin-top: 80px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .header-container {
                flex-wrap: wrap;
                gap: 10px;
            }

            .logo {
                font-size: 1.5em;
            }

            .nav-menu {
                gap: 15px;
                font-size: 14px;
                order: 3;
                width: 100%;
            }

            .auth-buttons {
                gap: 10px;
                order: 2;
            }

            .user-profile {
                gap: 8px;
                padding: 6px 12px;
            }

            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .user-name {
                font-size: 13px;
            }

            .btn-logout,
            .btn-login,
            .btn-register {
                padding: 8px 16px;
                font-size: 12px;
            }

            .btn-admin-panel {
                padding: 8px 16px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>

        <div class="header-container">
            <a href="index.php" class="logo">wazoForum</a>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="posts.php">Posts</a></li>
            </ul>

            <div class="auth-buttons">
                <?php if(isLogedIn()): 
                    $user = getCurrentUser();
                    $firstLetter = strtoupper(substr($user['username'], 0, 1));
                ?>
                    <?php if($user['role'] === 'admin'): ?>
                        <a href="dashboad.php" class="btn-admin-panel">
                            Admin Panel
                        </a>
                    <?php endif; ?>
                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php echo $firstLetter; ?>
                        </div>
                        <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Salutation for logged-in users -->
    <?php if(isLogedIn()): 
        $user = getCurrentUser();
    ?>
    <div data-username="<?php echo htmlspecialchars($user['username']); ?>"></div>
    <script src="js/salutation.js"></script>
    <?php endif; ?>

