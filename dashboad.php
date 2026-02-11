<?php
session_start();
include("db_connection.php");
include("function.php");

// Check if user is logged in and is admin
$user = getCurrentUser();
if (!isLogedIn() || !$user || $user['role'] !== 'admin') {
    header("Location: login.php?message=Admin access required");
    exit;
}

// Get statistics
// Total members
$members_sql = "SELECT COUNT(*) as total FROM users";
$members_result = mysqli_query($conn, $members_sql);
$members_row = mysqli_fetch_assoc($members_result);
$total_members = $members_row['total'];

// Total posts
$posts_sql = "SELECT COUNT(*) as total FROM posts";
$posts_result = mysqli_query($conn, $posts_sql);
$posts_row = mysqli_fetch_assoc($posts_result);
$total_posts = $posts_row['total'];

// Total replies
$replies_sql = "SELECT COUNT(*) as total FROM replies";
$replies_result = mysqli_query($conn, $replies_sql);
$replies_row = mysqli_fetch_assoc($replies_result);
$total_replies = $replies_row['total'];

// Total categories
$categories_sql = "SELECT COUNT(*) as total FROM categories";
$categories_result = mysqli_query($conn, $categories_sql);
$categories_row = mysqli_fetch_assoc($categories_result);
$total_categories = $categories_row['total'];

// Recent members (last 5)
$recent_members_sql = "SELECT user_id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$recent_members = mysqli_query($conn, $recent_members_sql);

// Recent posts (last 5)
$recent_posts_sql = "
    SELECT p.post_id, p.content, u.username, p.created_at 
    FROM posts p 
    JOIN users u ON p.user_id = u.user_id 
    ORDER BY p.created_at DESC LIMIT 5
";
$recent_posts = mysqli_query($conn, $recent_posts_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Admin Dashboard - E-Forum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .header-section h1 {
            font-size: 32px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-section p {
            opacity: 0.95;
            font-size: 16px;
        }

        .admin-nav {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            font-size: 32px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .stat-icon-members {
            background: #dfe9ff;
            color: #667eea;
        }

        .stat-icon-posts {
            background: #fff3cd;
            color: #ffc107;
        }

        .stat-icon-replies {
            background: #e8f5e9;
            color: #4caf50;
        }

        .stat-icon-categories {
            background: #f3e5f5;
            color: #9c27b0;
        }

        .stat-content h3 {
            font-size: 14px;
            color: #999;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h2 {
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .list-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item-title {
            font-weight: 600;
            color: #333;
        }

        .list-item-meta {
            font-size: 13px;
            color: #999;
        }

        .list-item-time {
            font-size: 12px;
            color: #bbb;
        }

        .view-all-link {
            display: inline-block;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .view-all-link:hover {
            color: #764ba2;
            gap: 8px;
        }

        .view-all-link::after {
            content: ' →';
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .back-link:hover {
            transform: translateX(-4px);
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .admin-nav {
                gap: 8px;
            }

            .nav-btn {
                padding: 8px 12px;
                font-size: 13px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container">
        <a href="index.php" class="back-link">
            Back to Forum
        </a>

        <div class="header-section">
            <h1>
                Admin Dashboard
            </h1>
            <p>Welcome back, <?php echo htmlspecialchars($user['username']); ?>! Here's an overview of your forum.</p>
            
            <div class="admin-nav">
                <a href="admin/manageuser.php" class="nav-btn">
                    Manage Members
                </a>
                <a href="admin/deleted_members.php" class="nav-btn">
                    Deleted Members
                </a>
                <a href="categories.php" class="nav-btn">
                    View Categories
                </a>
                <a href="posts.php" class="nav-btn">
                    All Posts
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-members">
                </div>
                <div class="stat-content">
                    <h3>Total Members</h3>
                    <div class="stat-number"><?php echo $total_members; ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-posts">
                </div>
                <div class="stat-content">
                    <h3>Total Posts</h3>
                    <div class="stat-number"><?php echo $total_posts; ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-replies">
                </div>
                <div class="stat-content">
                    <h3>Total Replies</h3>
                    <div class="stat-number"><?php echo $total_replies; ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-categories">
                </div>
                <div class="stat-content">
                    <h3>Categories</h3>
                    <div class="stat-number"><?php echo $total_categories; ?></div>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Recent Members -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        Recent Members
                    </h2>
                </div>
                <div class="card-body">
                    <?php 
                    $has_members = false;
                    while ($member = mysqli_fetch_assoc($recent_members)): 
                        $has_members = true;
                    ?>
                        <div class="list-item">
                            <div>
                                <div class="list-item-title"><?php echo htmlspecialchars($member['username']); ?></div>
                                <div class="list-item-meta"><?php echo htmlspecialchars($member['email']); ?></div>
                            </div>
                            <div class="list-item-time">
                                <?php echo date('M d, Y', strtotime($member['created_at'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if (!$has_members): ?>
                        <div class="empty-state">
                            <p>No members yet</p>
                        </div>
                    <?php endif; ?>

                    <a href="manageuser.php" class="view-all-link">View All Members</a>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        Recent Posts
                    </h2>
                </div>
                <div class="card-body">
                    <?php 
                    $has_posts = false;
                    while ($post = mysqli_fetch_assoc($recent_posts)): 
                        $has_posts = true;
                        $content_preview = substr($post['content'], 0, 50) . (strlen($post['content']) > 50 ? '...' : '');
                    ?>
                        <div class="list-item">
                            <div>
                                <div class="list-item-title"><?php echo htmlspecialchars($content_preview); ?></div>
                                <div class="list-item-meta">by <?php echo htmlspecialchars($post['username']); ?></div>
                            </div>
                            <div class="list-item-time">
                                <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if (!$has_posts): ?>
                        <div class="empty-state">
                            <p>No posts yet</p>
                        </div>
                    <?php endif; ?>

                    <a href="posts.php" class="view-all-link">View All Posts</a>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        Forum Health
                    </h2>
                </div>
                <div class="card-body">
                    <div class="list-item">
                        <div>
                            <div class="list-item-title">Avg. Posts per Member</div>
                        </div>
                        <div class="list-item-time" style="font-size: 16px; font-weight: 600;">
                            <?php echo $total_members > 0 ? round($total_posts / $total_members, 2) : 0; ?>
                        </div>
                    </div>
                    <div class="list-item">
                        <div>
                            <div class="list-item-title">Avg. Replies per Post</div>
                        </div>
                        <div class="list-item-time" style="font-size: 16px; font-weight: 600;">
                            <?php echo $total_posts > 0 ? round($total_replies / $total_posts, 2) : 0; ?>
                        </div>
                    </div>
                    <div class="list-item">
                        <div>
                            <div class="list-item-title">Total Discussions</div>
                        </div>
                        <div class="list-item-time" style="font-size: 16px; font-weight: 600;">
                            <?php echo $total_posts + $total_replies; ?>
                        </div>
                    </div>
                    <div class="list-item">
                        <div>
                            <div class="list-item-title">Forum Health</div>
                        </div>
                        <div class="list-item-time" style="font-size: 16px; font-weight: 600; color: #4caf50;">
                            <?php 
                            $engagement = $total_members > 0 ? ($total_posts / $total_members) : 0;
                            if ($engagement > 2) {
                                echo 'Excellent';
                            } elseif ($engagement > 1) {
                                echo 'Good';
                            } elseif ($engagement > 0.5) {
                                echo 'Fair';
                            } else {
                                echo 'Low';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
