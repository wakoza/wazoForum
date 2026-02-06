<?php
include("db_connection.php");
include("function.php"); // contains isLogedIn() and getCurrentUser()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="post.css">
    <title>wazoForum - Posts</title>
    <style>
        /* ===== HEADER STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        /* ===== POST FEED STYLES ===== */
        body {
            font-family: Arial, sans-serif;
            background: #fafafa;
        }

        .feed {
            max-width: 600px;
            margin: 30px auto;
        }

        .post {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .post-header {
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .category {
            font-size: 12px;
            color: #0095f6;
        }

        .post-body {
            padding: 12px 15px;
        }

        .post-body h4 {
            margin-bottom: 5px;
        }

        .post-footer {
            padding: 10px 15px;
            border-top: 1px solid #efefef;
            font-size: 14px;
            color: #555;
            display: flex;
            justify-content: space-between;
        }

        .reply-btn {
            color: #0095f6;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php include("header.php"); ?>

<div class="feed">

<?php
// Fetch posts with user, category, and number of replies
$sql = "
    SELECT 
        p.post_id,
        p.content,
        p.created_at,
        u.username,
        c.category_name,
        COUNT(r.reply_id) AS total_replies
    FROM posts p
    JOIN users u ON p.user_id = u.user_id
    JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN replies r ON p.post_id = r.post_id
    GROUP BY p.post_id
    ORDER BY p.created_at DESC
";

$result = mysqli_query($conn, $sql);

// Function to show time like Instagram
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . " min ago";
    if ($diff < 86400) return floor($diff / 3600) . " hrs ago";
    return floor($diff / 86400) . " days ago";
}

// Loop through posts
while ($row = mysqli_fetch_assoc($result)) { 
?>
    <div class="post">
        <!-- Post Header -->
        <div class="post-header">
            <span>@<?php echo htmlspecialchars($row['username']); ?></span>
            <span class="category"><?php echo htmlspecialchars($row['category_name']); ?></span>
        </div>

        <!-- Post Body -->
        <div class="post-body">
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
        </div>

        <!-- Post Footer -->
        <div class="post-footer">
            <span><?php echo timeAgo($row['created_at']); ?></span>
            <span>
                <?php echo $row['total_replies']; ?> replies · 
                <a class="reply-btn" href="replay.php?post_id=<?php echo $row['post_id']; ?>">Reply</a>
            </span>
        </div>
    </div>
<?php } ?>

</div>

</body>
</html>