<?php
session_start();
include("db_connection.php");
include("function.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts - wazoForum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-title {
            margin-bottom: 30px;
            color: #667eea;
            font-size: 24px;
        }

        /* ===== POSTS - JAMII FORUM STYLE ===== */
        .feed {
            display: flex;
            flex-direction: column;
            gap: 0;
            border-top: 2px solid #ddd;
        }

        .post {
            background: white;
            border-bottom: 2px solid #ddd;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 0;
            transition: background 0.3s ease;
            min-height: 200px;
        }

        .post:nth-child(even) {
            background: #f9f9f9;
        }

        .post:hover {
            background: #f5f5f5;
        }

        .post-sidebar {
            background: #f0f2f5;
            padding: 20px 15px;
            border-right: 2px solid #ddd;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .post-author {
            font-weight: 700;
            color: #667eea;
            font-size: 15px;
            margin-bottom: 10px;
            word-break: break-word;
        }

        .post-avatar {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .post-category {
            font-size: 12px;
            background: #667eea;
            color: white;
            padding: 5px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
        }

        .post-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 13px;
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px;
        }

        .post-time {
            color: #999;
            font-size: 13px;
        }

        .post-body {
            color: #333;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .post-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            font-size: 13px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .post-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            padding: 8px 12px;
        }

        .action-btn:hover {
            color: #764ba2;
        }

        .reply-btn-disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .reply-btn-disabled:hover {
            color: #ccc;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            background: white;
            border-radius: 8px;
        }

        .empty-state h2 {
            font-size: 22px;
            color: #667eea;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .post {
                grid-template-columns: 120px 1fr;
            }

            .post-sidebar {
                padding: 15px 10px;
            }

            .post-author {
                font-size: 13px;
            }

            .post-avatar {
                font-size: 32px;
            }

            .post-body {
                font-size: 14px;
            }

            .container {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 class="page-title">📰 All Forum Posts</h1>
        <?php if(isLogedIn()): ?>
            <a href="create_post.php" class="action-btn" style="background: #667eea; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-size: 16px;">
                Create New Post
            </a>
        <?php endif; ?>
    </div>

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
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) { 
?>
    <div class="post">
        <!-- POST SIDEBAR -->
        <div class="post-sidebar">
            <div class="post-avatar"></div>
            <div class="post-author"><?php echo htmlspecialchars($row['username']); ?></div>
            <span class="post-category"><?php echo htmlspecialchars($row['category_name']); ?></span>
        </div>

        <!-- POST CONTENT -->
        <div class="post-content">
            <div class="post-header">
                <span class="post-time"><?php echo timeAgo($row['created_at']); ?></span>
                <span>#<?php echo $row['post_id']; ?></span>
            </div>
            
            <div class="post-body">
                <?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 300))) . (strlen($row['content']) > 300 ? '...' : ''); ?>
            </div>

            <div class="post-footer">
                <div class="post-actions">
                    <!-- View Post Button -->
                    <a class="action-btn" href="replay.php?post_id=<?php echo $row['post_id']; ?>">
                        💬 <?php echo $row['total_replies']; ?> Replies
                    </a>
                    
                    <!-- Reply Button (Only for logged-in users) -->
                    <?php if(isLogedIn()): ?>
                        <a class="action-btn" href="replay.php?post_id=<?php echo $row['post_id']; ?>">
                            ↩️ Reply
                        </a>
                    <?php else: ?>
                        <a class="action-btn reply-btn-disabled" title="Login required to reply - You must be logged in to participate in discussions" href="login.php?message=Login to reply to posts">
                            ↩️ Reply (Login Required)
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php 
    }
} else {
?>
    <div class="empty-state">
        <h2>No Posts Yet</h2>
        <p>Be the first one to start a discussion!</p>
        <?php if(isLogedIn()): ?>
            <a href="create_post.php" class="action-btn" style="margin-top: 15px; justify-content: center;">
                Create New Post
            </a>
        <?php else: ?>
            <a href="login.php" class="action-btn" style="margin-top: 15px; justify-content: center;">
                Login to Create Post
            </a>
        <?php endif; ?>
    </div>
<?php } ?>

    </div>
</div>

</body>
</html>