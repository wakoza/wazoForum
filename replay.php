<?php
session_start();
include("db_connection.php");
include("function.php");

// Check if user is logged in - STRICT enforcement
if (!isLogedIn()) {
    header("Location: login.php?message=You must be logged in to view and reply to posts");
    exit;
}

// Handle post deletion
$delete_success = '';
$delete_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_post') {
    $user = getCurrentUser();
    $post_id = (int)$_POST['post_id'];
    
    // Verify the user owns this post
    $verify_sql = "SELECT user_id FROM posts WHERE post_id = $post_id";
    $verify_result = mysqli_query($conn, $verify_sql);
    
    if (!$verify_result || mysqli_num_rows($verify_result) == 0) {
        $delete_error = "Post not found";
    } else {
        $post_owner = mysqli_fetch_assoc($verify_result);
        if ($post_owner['user_id'] != $user['user_id']) {
            $delete_error = "You can only delete your own posts";
        } else {
            // Delete replies first (foreign key constraint)
            $del_replies = "DELETE FROM replies WHERE post_id = $post_id";
            mysqli_query($conn, $del_replies);
            
            // Then delete the post
            $del_post = "DELETE FROM posts WHERE post_id = $post_id";
            if (mysqli_query($conn, $del_post)) {
                // Redirect to posts page after successful deletion
                header("Location: posts.php?message=Post deleted successfully");
                exit;
            } else {
                $delete_error = "Error deleting post: " . mysqli_error($conn);
            }
        }
    }
}

// Get post_id from URL
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id <= 0) {
    die("Invalid post ID");
}

// Fetch the post details
$post_sql = "
    SELECT 
        p.post_id,
        p.content,
        p.created_at,
        p.user_id,
        u.username,
        c.category_name
    FROM posts p
    JOIN users u ON p.user_id = u.user_id
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.post_id = $post_id
";

$post_result = mysqli_query($conn, $post_sql);

if (!$post_result || mysqli_num_rows($post_result) == 0) {
    die("Post not found");
}

$post = mysqli_fetch_assoc($post_result);
$user = getCurrentUser();

// Handle reply submission
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply_content = isset($_POST['reply_content']) ? trim($_POST['reply_content']) : '';
    
    if (empty($reply_content)) {
        $error_msg = "Reply cannot be empty";
    } else if (strlen($reply_content) > 5000) {
        $error_msg = "Reply is too long (max 5000 characters)";
    } else {
        // Insert reply into database
        $reply_content_escaped = mysqli_real_escape_string($conn, $reply_content);
        $user_id = $user['user_id'];
        
        $insert_sql = "
            INSERT INTO replies (post_id, user_id, content, created_at)
            VALUES ($post_id, $user_id, '$reply_content_escaped', NOW())
        ";
        
        if (mysqli_query($conn, $insert_sql)) {
            $success_msg = "Reply posted successfully!";
            $_POST['reply_content'] = ''; // Clear the form
        } else {
            $error_msg = "Error posting reply: " . mysqli_error($conn);
        }
    }
}

// Fetch all replies for this post
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . " min ago";
    if ($diff < 86400) return floor($diff / 3600) . " hrs ago";
    return floor($diff / 86400) . " days ago";
}

$replies_sql = "
    SELECT 
        r.reply_id,
        r.content,
        r.created_at,
        u.username,
        u.user_id
    FROM replies r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.post_id = $post_id
    ORDER BY r.created_at ASC
";

$replies_result = mysqli_query($conn, $replies_sql);
$replies = [];
if ($replies_result) {
    while ($reply = mysqli_fetch_assoc($replies_result)) {
        $replies[] = $reply;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Post - wazoForum</title>
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

        /* BACK BUTTON */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #764ba2;
            transform: translateX(-3px);
        }

        /* ORIGINAL POST */
        .original-post {
            background: white;
            border: 1px solid #ddd;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 0;
        }

        .original-post-sidebar {
            background: #f0f2f5;
            padding: 20px 15px;
            border-right: 2px solid #ddd;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .post-user-avatar {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .post-user-name {
            font-weight: 700;
            color: #667eea;
            font-size: 15px;
            word-break: break-word;
            margin-bottom: 10px;
        }

        .post-category-badge {
            font-size: 10px;
            background: #667eea;
            color: white;
            padding: 4px 6px;
            border-radius: 3px;
        }

        .original-post-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .original-post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .post-time {
            color: #999;
        }

        .original-post-body {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
            flex-grow: 1;
        }

        /* REPLIES SECTION */
        .replies-section {
            background: white;
            padding: 0;
            border: 1px solid #ddd;
            border-top: 0;
            margin-bottom: 0;
        }

        .replies-section h2 {
            color: #333;
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            padding: 15px;
            background: #f0f2f5;
            border-bottom: 1px solid #ddd;
        }

        .no-replies {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 14px;
        }

        .reply-item {
            background: white;
            padding: 0;
            border-bottom: 1px solid #ddd;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 0;
        }

        .reply-item:nth-child(even) {
            background: #f9f9f9;
        }

        .reply-sidebar {
            background: #f0f2f5;
            padding: 15px;
            text-align: center;
            border-right: 2px solid #ddd;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .reply-avatar {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .reply-author {
            font-weight: 700;
            color: #667eea;
            font-size: 13px;
            margin-bottom: 6px;
            word-break: break-word;
        }

        .reply-time {
            color: #999;
            font-size: 11px;
        }

        .reply-content {
            padding: 15px;
            font-size: 14px;
            line-height: 1.7;
            color: #333;
        }

        .reply-header {
            display: none;
        }

        /* REPLY FORM */
        .reply-form-section {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: 3px solid #667eea;
            margin-top: 20px;
        }

        .reply-form-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            resize: vertical;
            min-height: 120px;
            transition: all 0.3s ease;
        }

        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .char-count {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #f0f2f5;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
        }

        @media (max-width: 600px) {
            .original-post,
            .reply-item {
                grid-template-columns: 1fr;
            }

            .original-post-sidebar,
            .reply-sidebar {
                border-right: none;
                border-bottom: 1px solid #ddd;
                padding: 15px;
            }

            .post-user-avatar,
            .reply-avatar {
                font-size: 32px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
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

        <a href="categories.php?id=<?php echo htmlspecialchars($_GET['category_id'] ?? ''); ?>" class="back-btn">
            Back to Category
        </a>

        <?php if ($delete_error): ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($delete_error); ?>
            </div>
        <?php endif; ?>

        <!-- ORIGINAL POST -->
        <div class="original-post">
            <div class="original-post-sidebar">
                <div class="post-user-avatar"></div>
                <div class="post-user-name"><?php echo htmlspecialchars($post['username']); ?></div>
                <span class="post-category-badge"><?php echo htmlspecialchars($post['category_name']); ?></span>
            </div>
            <div class="original-post-content">
                <div class="original-post-header">
                    <span class="post-time"><?php echo timeAgo($post['created_at']); ?></span>
                    <span>#1</span>
                </div>
                <div class="original-post-body">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
                
                <!-- Delete button for post owner -->
                <?php if($user['user_id'] == $post['user_id']): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; text-align: right;">
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post? All replies will also be deleted.');">
                        <input type="hidden" name="action" value="delete_post">
                        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                        <button type="submit" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s ease;">
                            Delete Post
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- REPLIES LIST -->
        <?php if (count($replies) > 0): ?>
        <div class="replies-section">
            <h2><?php echo count($replies); ?> Replies</h2>
            <?php foreach ($replies as $index => $reply): ?>
                <div class="reply-item">
                    <div class="reply-sidebar">
                        <div class="reply-avatar"></div>
                        <div class="reply-author"><?php echo htmlspecialchars($reply['username']); ?></div>
                    </div>
                    <div class="reply-content">
                        <div style="font-size: 11px; color: #999; margin-bottom: 8px;">
                            <?php echo timeAgo($reply['created_at']); ?> · #<?php echo $index + 2; ?>
                        </div>
                        <div><?php echo nl2br(htmlspecialchars($reply['content'])); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="replies-section">
            <h2>Replies</h2>
            <div class="no-replies">
                <p>No replies yet. Be the first to reply!</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- REPLY FORM -->
        <div class="reply-form-section">
            <h2>✍️ Add Your Reply</h2>

            <?php if ($success_msg): ?>
                <div class="success-msg"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="reply_content">Your Reply *</label>
                    <textarea 
                        id="reply_content" 
                        name="reply_content" 
                        placeholder="Share your thoughts..." 
                        maxlength="5000"
                        onkeyup="updateCharCount()"
                        required
                    ><?php echo isset($_POST['reply_content']) ? htmlspecialchars($_POST['reply_content']) : ''; ?></textarea>
                    <div class="char-count">
                        <span id="char-count">0</span> / 5000 characters
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✉️ Post Reply
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        Clear
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
    function updateCharCount() {
        const textarea = document.getElementById('reply_content');
        const count = document.getElementById('char-count');
        count.textContent = textarea.value.length;
    }

    // Initial count
    updateCharCount();
    </script>

</body>
</html>
