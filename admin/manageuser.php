<?php
session_start();
include("../db_connection.php");
// if(isset($_SESSION['user_id'])){
//     header("location: ../login.php");
// }
//retrive data from database
$sql ="SELECT * FROM users";
$results = mysqli_query($conn, $sql);
$query = mysqli_fetch_all($results, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title></title>
</head>
<body>
    <?php include("../header.php");?>
    <div class="container">
        <table border="2">
            <tr>
                <th>Username</th>
                <th>role</th>
                <th>email</th>
                <th>created_at</th>
            </tr>
            <?php foreach($results as $result){ ?>
            <tr>
                <td><?php echo htmlspecialchars($result['username'])?></td>
                <td><?php echo htmlspecialchars($result['email'])?></td>
                <td><?php echo htmlspecialchars($result['role'])?></td>
                <td><?php echo htmlspecialchars($result['created_at'])?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>