
<?php
session_start();
include("db_connection.php");
include("function.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Forum | Community Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

       
        /* ===== HERO SECTION ===== */
        .hero {
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.7), rgba(0, 51, 153, 0.7)), 
                        url('pictures/background.jpeg') center/cover;
            color: white;
            padding: 120px 20px;
            text-align: center;
            position: relative;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero h1 {
            font-size: 56px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .search-box {
            display: flex;
            gap: 10px;
            max-width: 500px;
            margin: 0 auto;
            margin-bottom: 30px;
        }

        .search-box button {
            padding: 14px 30px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-box button:hover {
            background: #0052a3;
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 80px;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
        }

        .feature-icon {
            font-size: 48px;
            color: #0066cc;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* ===== COMPANY SECTION ===== */
        .company-section {
            background: white;
            padding: 80px 20px;
            margin-top: 60px;
        }

        .company-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .company-text h2 {
            font-size: 42px;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .company-text h2 span {
            color: #0066cc;
        }

        .company-text p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .company-btn {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .company-btn:hover {
            background: #0052a3;
        }

        .company-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .company-images img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .company-images .large-img {
            grid-column: 1 / -1;
        }

        /* ===== TRENDING POSTS SECTION ===== */
        .trending-posts-section {
            background: #f9f9f9;
            padding: 80px 20px;
            margin-top: 60px;
        }

        .trending-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #333;
        }

        .section-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 40px;
        }

        .trending-posts-feed {
            display: flex;
            flex-direction: column;
            gap: 0;
            border-top: 2px solid #ddd;
            background: white;
        }

        .trending-post {
            background: white;
            border-bottom: 2px solid #ddd;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 0;
            transition: background 0.3s ease;
            min-height: 200px;
        }

        .trending-post:nth-child(even) {
            background: #f9f9f9;
        }

        .trending-post:hover {
            background: #f5f5f5;
        }

        .trending-post-sidebar {
            background: #f0f2f5;
            padding: 20px 15px;
            border-right: 2px solid #ddd;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .trending-post-author {
            font-weight: 700;
            color: #667eea;
            font-size: 15px;
            margin-bottom: 10px;
            word-break: break-word;
        }

        .trending-post-avatar {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .trending-post-category {
            font-size: 12px;
            background: #667eea;
            color: white;
            padding: 5px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
        }

        .trending-post-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .trending-post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 13px;
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px;
        }

        .trending-post-time {
            color: #999;
            font-size: 13px;
        }

        .trending-post-body {
            color: #333;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .trending-badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .trending-post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .trending-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .trending-action-btn {
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

        .trending-action-btn:hover {
            color: #764ba2;
        }

        .view-all-btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 14px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 30px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .view-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.6);
        }

        @media (max-width: 768px) {
            .trending-post {
                grid-template-columns: 120px 1fr;
                min-height: auto;
            }

            .trending-post-sidebar {
                padding: 15px 10px;
            }

            .trending-post-author {
                font-size: 13px;
            }

            .trending-post-avatar {
                font-size: 32px;
            }

            .trending-post-body {
                font-size: 14px;
            }

            .section-title {
                font-size: 24px;
            }
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .search-box {
                flex-direction: column;
            }

            .company-content {
                grid-template-columns: 1fr;
            }

            .nav-links {
                gap: 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<?php include("header.php"); ?>
    <!-- HERO SECTION -->
    <section class="hero">
        <?php if(isLogedIn()):?>
        <h1>Welcome to our <br> Community Forums</h1>
        <p>Connect, discuss, and share knowledge with our community</p>
        <?php else: ?>
        <h1>Responsive Design<br>for Community Forums</h1>
        <p>Connect, discuss, and share knowledge with our community</p>
        <div class="search-box">
            <a href="register.php" class="btn-join">Join Discussion</a>
        </div>
        <?php endif; ?>

    </section>
    
    

    <!-- FEATURES SECTION -->
    <section class="features">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-comments"></i></div>
                <h3>Discussion Boards</h3>
                <p>Create and participate in topic-based discussions with thousands of community members. <?php if(!isLogedIn()): ?><strong style="color: #667eea;">Login to create posts.</strong><?php endif; ?></p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Ask & Answer</h3>
                <p>Get expert help by asking questions and sharing valuable solutions with the community.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-globe"></i></div>
                <h3>Global Network</h3>
                <p>Connect with users from around the world and expand your professional network.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-circle"></i></div>
                <h3>User Profiles</h3>
                <p>Build your reputation and track your contributions across the platform.</p>
            </div>
        </div>
    </section>


    <!-- TRENDING POSTS SECTION -->
    <section class="trending-posts-section">
        <div class="trending-container">
            <h2 class="section-title"> Trending Discussions</h2>
            <p class="section-subtitle">Most active conversations in our community</p>

            <div class="trending-posts-feed">
            <?php
            // Fetch 5 most replied posts
            $trending_sql = "
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
                ORDER BY total_replies DESC
                LIMIT 5
            ";

            $trending_result = mysqli_query($conn, $trending_sql);

            // Function to show time like Instagram
            function timeAgoTrending($datetime) {
                $time = strtotime($datetime);
                $diff = time() - $time;
                if ($diff < 60) return "Just now";
                if ($diff < 3600) return floor($diff / 60) . " min ago";
                if ($diff < 86400) return floor($diff / 3600) . " hrs ago";
                return floor($diff / 86400) . " days ago";
            }

            // Loop through trending posts
            if ($trending_result && mysqli_num_rows($trending_result) > 0) {
                while ($post = mysqli_fetch_assoc($trending_result)) {
            ?>
                <div class="trending-post">
                    <!-- POST SIDEBAR -->
                    <div class="trending-post-sidebar">
                        <div class="trending-post-avatar">👤</div>
                        <div class="trending-post-author"><?php echo htmlspecialchars($post['username']); ?></div>
                        <span class="trending-post-category"><?php echo htmlspecialchars($post['category_name']); ?></span>
                    </div>

                    <!-- POST CONTENT -->
                    <div class="trending-post-content">
                        <span class="trending-badge"> <?php echo $post['total_replies']; ?> Replies</span>
                        
                        <div class="trending-post-header">
                            <span class="trending-post-time"><?php echo timeAgoTrending($post['created_at']); ?></span>
                            <span>#<?php echo $post['post_id']; ?></span>
                        </div>
                        
                        <div class="trending-post-body">
                            <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))) . (strlen($post['content']) > 300 ? '...' : ''); ?>
                        </div>

                        <div class="trending-post-footer">
                            <span></span>
                            <div class="trending-actions">
                                <a class="trending-action-btn" href="replay.php?post_id=<?php echo $post['post_id']; ?>">
                                    💬 View Discussion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
            ?>
                <div style="text-align: center; padding: 60px 20px; color: #999;">
                    <p>No discussions yet. Be the first one to start!</p>
                </div>
            <?php } ?>
            </div>

            <a href="login.php" class="view-all-btn">Create a post</a>
        </div>
    </section>

    <!-- COMPANY SECTION -->
    <section class="company-section">
        <div class="company-content">
            <div class="company-text">
                <h2>We've been building <span>community</span><br>for <span>many years.</span></h2>
                <p>Our platform has been trusted by thousands of users to find answers, share expertise, and build meaningful connections. We're committed to providing a safe, welcoming space for everyone.</p>
                <p>Join our growing community and discover why millions of people choose to engage with us every day. Your voice matters and your contributions help others.</p>
                <a href="#" class="company-btn">Create a post</a>
            </div>
            <div class="company-images">
                <img src="pictures/background.jpeg" alt="Community Discussion">
                <img src="pictures/background2.jpeg" alt="Team Collaboration">
                <img src="pictures/background.jpeg" alt="Professional Network" class="large-img">
            </div>
        </div>
    </section>
    <?php 
    include("footer.php");
    ?>
</body>
</html>
