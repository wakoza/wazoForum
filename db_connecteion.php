<?php

// Create connection
$conn = new mysqli("localhost", "root", "","wazoforum");

// Check connection
if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

// Connection successful - no output
?>