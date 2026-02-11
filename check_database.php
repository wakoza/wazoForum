<?php
include("db_connection.php");

echo "<h1>Database Diagnostic Report</h1>";

// Check current database connection
echo "<h2>1. Connection Info:</h2>";
echo "Connected to database: <strong>" . $conn->server_info . "</strong><br>";
echo "Current database: <strong>" . $conn->select_db("e_forum") ? "e_forum" : "ERROR" . "</strong><br><br>";

// List all tables
echo "<h2>2. Tables in Database:</h2>";
$tables_result = $conn->query("SHOW TABLES");
echo "<ul>";
while($table = $tables_result->fetch_assoc()) {
    $table_name = array_values($table)[0];
    echo "<li>" . $table_name . "</li>";
}
echo "</ul><br>";

// Check users table structure
echo "<h2>3. Users Table Structure:</h2>";
$structure = $conn->query("DESCRIBE users");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while($row = $structure->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table><br>";

// Count users
echo "<h2>4. Total Users in Database:</h2>";
$count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc();
echo "<strong>" . $count['total'] . " users found in database</strong><br><br>";

// Show all users
echo "<h2>5. All Users in Database:</h2>";
$users = $conn->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY created_at DESC");
if($users->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='width:100%'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th></tr>";
    while($user = $users->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $user['user_id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>No users found!</strong></p>";
}

$conn->close();
?>
