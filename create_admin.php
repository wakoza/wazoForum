<?php
include("db_connection.php"); 

$username = "ANICETH";
$email = "leonceaniceth@gmail.com";
$password = "123456789";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the admin user
$sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', 'admin')";

if (mysqli_query($conn, $sql)) {
    echo "Admin user created successfully!<br>";
    echo "Username: " . $username . "<br>";
    echo "Email: " . $email . "<br>";
    echo "Role: admin<br>";
} else {
    echo "Error creating admin user: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
