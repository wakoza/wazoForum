<?php
session_start();
include("db_connection.php");
include("function.php");

// Get & validate category ID from URL
$selected_category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$selected_category = null;
$posts = null;

// Fetch the selected category if ID is provided
if ($selected_category_id > 0) {
    $cat_sql = "SELECT category_id, category_name, description 
                FROM categories 
                WHERE category_id = $selected_category_id";
    
    $cat_result = mysqli_query($conn, $cat_sql);
    
    if ($cat_result && mysqli_num_rows($cat_result) > 0) {
        $selected_category = mysqli_fetch_assoc($cat_result);
        
        // Fetch posts in this category
        $posts_sql = "
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
            WHERE p.category_id = $selected_category_id
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ";
        
        $posts = mysqli_query($conn, $posts_sql);
    }
}

// Fetch all categories for the sidebar
$all_categories_sql = "SELECT c.category_id, c.category_name, c.description, COUNT(p.post_id) AS post_count
                       FROM categories c
                       LEFT JOIN posts p ON c.category_id = p.category_id
                       GROUP BY c.category_id
                       ORDER BY c.created_at DESC";
$all_categories = mysqli_query($conn, $all_categories_sql);

// Function to show time like Instagram
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . " min ago";
    if ($diff < 86400) return floor($diff / 3600) . " hrs ago";
    return floor($diff / 86400) . " days ago";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Categories - wazoForum</title>
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

        /* ===== MAIN LAYOUT ===== */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
            display: grid;
            grid-template-columns: 1fr 2.5fr;
            gap: 30px;
        }

        /* ===== CATEGORIES SIDEBAR ===== */
        .categories-sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .categories-sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #667eea;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .category-item {
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9f9f9;
        }

        .category-item:hover {
            background: #667eea;
            color: white;
            transform: translateX(5px);
        }

        .category-item.active {
            background: #667eea;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .category-badge {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .category-item.active .category-badge {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* ===== CATEGORY VIEW (No selection) ===== */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .category-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .category-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .category-card p {
            font-size: 14px;
            opacity: 0.95;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .category-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        /* ===== CATEGORY SELECTED VIEW ===== */
        .category-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
        }

        .category-header h1 {
            font-size: 28px;
            color: #667eea;
            margin-bottom: 8px;
        }

        .category-header p {
            color: #666;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .category-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
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

        /* ===== POSTS - JAMII FORUM STYLE ===== */
        .posts-feed {
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
            min-height: 180px;
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
            font-size: 12px;
            padding: 6px 10px;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            color: #764ba2;
        }

        .view-replies-btn {
            color: #337ab7;
        }

        .view-replies-btn:hover {
            color: #0078b4;
        }

        .reply-btn-disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .reply-btn-disabled:hover {
            background: transparent;
            color: #ccc;
        }

        .replies-section {
            grid-column: 1 / -1;
            background: #f5f5f5;
            padding: 0;
            border-top: 1px solid #ddd;
            display: none;
            border-bottom: 1px solid #ddd;
        }

        .replies-section.show {
            display: block;
        }

        .replies-container {
            display: flex;
            flex-direction: column;
        }

        .reply-item {
            background: white;
            padding: 0;
            border-left: 150px solid #f0f2f5;
            min-height: 80px;
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 0;
            border-bottom: 1px solid #ddd;
        }

        .reply-item:nth-child(even) {
            background: #f9f9f9;
        }

        .reply-sidebar {
            background: #f0f2f5;
            padding: 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 12px;
        }

        .reply-author {
            font-weight: 700;
            color: #667eea;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .reply-time {
            color: #999;
            font-size: 11px;
        }

        .reply-content {
            padding: 12px;
            color: #333;
            line-height: 1.6;
            font-size: 13px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state h2 {
            font-size: 22px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .categories-sidebar {
                position: static;
            }

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                gap: 15px;
            }

            .header-container {
                flex-wrap: wrap;
                gap: 10px;
            }

            .post,
            .reply-item {
                grid-template-columns: 120px 1fr;
            }

            .post-sidebar,
            .reply-sidebar {
                padding: 15px 10px;
            }

            .post-author,
            .reply-author {
                font-size: 13px;
            }

            .post-avatar {
                font-size: 32px;
            }

            .post-body,
            .reply-content {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <!-- MAIN CONTAINER -->
    <div class="container">

        <!-- SIDEBAR - CATEGORIES LIST -->
        <div class="categories-sidebar">
            <h2>📂 Categories</h2>
            <div>
                <a href="categories.php" class="category-item <?php echo !$selected_category ? 'active' : ''; ?>">
                    <span>All Categories</span>
                    <span class="category-badge"><?php echo mysqli_num_rows($all_categories); ?></span>
                </a>
            </div>
            <?php 
            // Reset the result pointer for the second loop
            mysqli_data_seek($all_categories, 0);
            while ($cat = mysqli_fetch_assoc($all_categories)): 
            ?>
                <a href="categories.php?id=<?php echo $cat['category_id']; ?>" 
                   class="category-item <?php echo ($selected_category && $selected_category['category_id'] == $cat['category_id']) ? 'active' : ''; ?>">
                    <span><?php echo htmlspecialchars($cat['category_name']); ?></span>
                    <span class="category-badge"><?php echo $cat['post_count']; ?></span>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">

            <!-- IF NO CATEGORY SELECTED - SHOW ALL CATEGORIES -->
            <?php if (!$selected_category): ?>

                <h1 style="margin-bottom: 10px; color: #667eea;">Explore Forum Categories</h1>
                <p style="color: #666; margin-bottom: 30px;">Select a category to view discussions and create new posts</p>

                <div class="categories-grid">
                    <?php 
                    mysqli_data_seek($all_categories, 0);
                    while ($cat = mysqli_fetch_assoc($all_categories)): 
                    ?>
                        <a href="categories.php?id=<?php echo $cat['category_id']; ?>" class="category-card">
                            <h3><?php echo htmlspecialchars($cat['category_name']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($cat['description'], 0, 100)) . (strlen($cat['description']) > 100 ? '...' : ''); ?></p>
                            <div class="category-card-footer">
                                <span>📊 <?php echo $cat['post_count']; ?> posts</span>
                                <span>→</span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>

            <!-- IF CATEGORY SELECTED - SHOW CATEGORY POSTS -->
            <?php else: ?>

                <div class="category-header">
                    <h1><?php echo htmlspecialchars($selected_category['category_name']); ?></h1>
                    <p><?php echo htmlspecialchars($selected_category['description']); ?></p>
                    
                    <div class="category-actions">
                        <?php if(isLogedIn()): ?>
                            <a href="posts.php" class="btn btn-primary">
                                ✏️ Create New Post
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">
                                ✏️ Login to Post
                            </a>
                        <?php endif; ?>
                        <a href="categories.php" class="btn btn-secondary">
                            ← Back to Categories
                        </a>
                    </div>
                </div>

                <!-- POSTS -->
                <?php if ($posts && mysqli_num_rows($posts) > 0): ?>

                    <div class="posts-feed">
                        <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                            <div class="post">
                                <!-- POST SIDEBAR -->
                                <div class="post-sidebar">
                                    <div class="post-avatar">👤</div>
                                    <div class="post-author"><?php echo htmlspecialchars($post['username']); ?></div>
                                    <span class="post-category"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                </div>

                                <!-- POST CONTENT -->
                                <div class="post-content">
                                    <div class="post-header">
                                        <span class="post-time"><?php echo timeAgo($post['created_at']); ?></span>
                                        <span>#<?php echo $post['post_id']; ?></span>
                                    </div>
                                    
                                    <div class="post-body">
                                        <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))) . (strlen($post['content']) > 300 ? '...' : ''); ?>
                                    </div>

                                    <div class="post-footer">
                                        <div class="post-actions">
                                            <!-- View Replies Button -->
                                            <button class="action-btn view-replies-btn" onclick="toggleReplies(this, <?php echo $post['post_id']; ?>)">
                                                💬 <?php echo $post['total_replies']; ?>
                                            </button>
                                            
                                            <!-- Reply Button (Only for logged-in users) -->
                                            <?php if(isLogedIn()): ?>
                                                <a class="action-btn" href="replay.php?post_id=<?php echo $post['post_id']; ?>">
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

                                <!-- REPLIES SECTION -->
                                <div class="replies-section" id="replies-<?php echo $post['post_id']; ?>">
                                    <div class="replies-container" id="replies-container-<?php echo $post['post_id']; ?>"></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                <?php else: ?>

                    <div class="empty-state">
                        <h2>📭 No Discussions Yet</h2>
                        <p>Be the first one to start a discussion in this category.</p>
                        <?php if(isLogedIn()): ?>
                            <a href="create_post.php" class="btn btn-primary">
                                Start Discussion Now
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">
                                Login to Create Post
                            </a>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>

    <!-- SCRIPT FOR TOGGLING REPLIES -->
    <script>
    function toggleReplies(btn, postId) {
        const repliesSection = document.getElementById('replies-' + postId);
        const repliesContainer = document.getElementById('replies-container-' + postId);

        // Toggle display
        if (repliesSection.classList.contains('show')) {
            repliesSection.classList.remove('show');
            btn.textContent = '💬 ' + btn.textContent.match(/\d+/)[0];
        } else {
            repliesSection.classList.add('show');
            btn.textContent = '💬 Hide';

            // Load replies if not already loaded
            if (repliesContainer.innerHTML === '') {
                loadReplies(postId, repliesContainer);
            }
        }
    }

    function loadReplies(postId, container) {
        // Use fetch to load replies from a separate PHP file
        fetch('get_replies.php?post_id=' + postId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.replies.length > 0) {
                    let html = '';
                    data.replies.forEach((reply, index) => {
                        html += `
                            <div class="reply-item">
                                <div class="reply-sidebar">
                                    <div style="font-size: 20px; margin-bottom: 4px;">👤</div>
                                    <div class="reply-author">${reply.username}</div>
                                </div>
                                <div class="reply-content">
                                    <div style="font-size: 11px; color: #999; margin-bottom: 8px;">${reply.time_ago}</div>
                                    <div>${reply.content}</div>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div style="padding: 15px; text-align: center; color: #999; grid-column: 1/-1;">No replies yet.</div>';
                }
            })
            .catch(error => {
                container.innerHTML = '<p style="color: #f00;">Error loading replies</p>';
            });
    }
    </script>

</body>
</html>
