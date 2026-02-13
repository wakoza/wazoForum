<?php

$databaseUrl = getenv("MYSQL_URL");

$dbParts = parse_url($databaseUrl);

$host = $dbParts['host'];
$user = $dbParts['user'];
$pass = $dbParts['pass'];
$db   = ltrim($dbParts['path'], '/');
$port = $dbParts['port'];

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>