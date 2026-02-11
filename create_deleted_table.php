<?php
include("db_connection.php");

// Create deleted_members table
$sql = "CREATE TABLE IF NOT EXISTS `deleted_members` (
  `deleted_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` enum('admin','member') NOT NULL,
  `created_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`deleted_id`),
  KEY `user_id` (`user_id`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (mysqli_query($conn, $sql)) {
    echo "deleted_members table created successfully!<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
?>
