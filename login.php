<?php
include("db_connection.php"); 
include("function.php");
session_start();

$redmassage = "";
$greenmassage = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //validate input
    if(empty($email) || empty($password)){
        $redmassage = "email or password is required";
    }

    //fetch informatiom from the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $results = mysqli_query($conn, $sql);

    //change info into an array 
    if(mysqli_num_rows($results) == 1){
        $row = mysqli_fetch_assoc($results);
        
    //verify the password
        if(password_verify($password,$row['password'])){
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            //redirecting to welcome page
            header("location: index.php");
            exit();
        }else{
            $redmassage = "Incorrect password" .mysqli_connect_error();
        }
    }else{
        $redmassage = "Acount not exist";
    }
    
   
}   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - wazoForum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        /* Header styling */
        header {
            flex-shrink: 0;
        }

        /* Main content wrapper */
        .main-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .container {
            background: white;
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.6s ease;
        }
        
        h1 {
            color: #333;
            margin-bottom: 8px;
            text-align: center;
            font-size: 32px;
            font-weight: 700;
        }
        
        .subtitle {
            text-align: center;
            color: #999;
            margin-bottom: 30px;
            font-size: 15px;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 15px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(-1px);
        }
        
        .alert {
            padding: 15px 16px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 15px;
            border-left: 4px solid;
            animation: fadeIn 0.4s ease;
        }
        
        .alert-error {
            background-color: #fff5f5;
            color: #c53030;
            border-left-color: #fc8181;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            color: #15803d;
            border-left-color: #86efac;
        }

        .alert-success a {
            color: #15803d;
            font-weight: 700;
            text-decoration: none;
        }

        .alert-success a:hover {
            text-decoration: underline;
        }
        
        .login-link {
            text-align: center;
            margin-top: 30px;
            font-size: 15px;
            color: #666;
            border-top: 1px solid #f0f0f0;
            padding-top: 25px;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Footer styling */
        footer {
            flex-shrink: 0;
            background: #222;
            color: #fff;
            text-align: center;
            padding: 35px 20px;
            margin-top: auto;
            border-top: 1px solid #333;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Responsive design */
        @media (max-width: 500px) {
            .container {
                padding: 40px 25px;
                margin: 20px;
            }
            
            h1 {
                font-size: 26px;
            }

            .main-wrapper {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="main-wrapper">
        <div class="container">
            <h1>Login</h1>
            <p class="subtitle">Sign in to your account</p>
            
            <?php if ($redmassage): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($redmassage); ?></div>
            <?php endif; ?>
            
            <?php if ($greenmassage): ?>
                <div class="alert alert-success"><?php echo $greenmassage; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <div class="login-link">
                Don not have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>