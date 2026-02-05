<?php
session_start();
include "db_connection.php";


   //Get & validate category ID

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id <= 0) {
    die("Invalid category");
}


   //Fetch the selected category

$cat_sql = "SELECT category_id, category_name, description 
            FROM categories 
            WHERE category_id = $category_id";

$cat_result = mysqli_query($conn, $cat_sql);

if (!$cat_result || mysqli_num_rows($cat_result) === 0) {
    die("Category not found");
}

$category = mysqli_fetch_assoc($cat_result);

/* =========================
   3. Fetch topics in this category
========================= */
$topic_sql = "
    SELECT 
        p.post_id,
        p.title,
        p.created_at,
        u.username,
        COUNT(r.reply_id) AS reply_count
    FROM posts p
    JOIN users u ON p.user_id = u.user_id
    LEFT JOIN replies r ON p.post_id = r.post_id
    WHERE p.category_id = $category_id
    GROUP BY p.post_id
    ORDER BY p.created_at DESC
";

$topics = mysqli_query($conn, $topic_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($category['category_name']); ?> | E-Forum</title>

<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:"Segoe UI",sans-serif;}
body{background:#f1f4f9;padding:30px;}

.header{
    background:#fff;
    padding:25px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}
.header h1{color:#0d6efd;margin-bottom:10px;}
.header p{color:#666;}

.btn{
    display:inline-block;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
}
.btn-primary{background:#0d6efd;color:#fff;}
.btn-success{background:#198754;color:#fff;}

.topic{
    background:#fff;
    padding:18px;
    border-radius:10px;
    margin-bottom:15px;
    box-shadow:0 8px 20px rgba(0,0,0,.07);
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.topic h3{margin-bottom:6px;}
.topic small{color:#888;}

.badge{
    background:#0d6efd;
    color:#fff;
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
}

.empty{
    background:#fff;
    padding:40px;
    text-align:center;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}
</style>
</head>

<body>

<!-- CATEGORY HEADER -->
<div class="header">
    <h1><?php echo htmlspecialchars($category['category_name']); ?></h1>
    <p><?php echo htmlspecialchars($category['description']); ?></p>
    <br>
    <a href="create_topic.php?cat_id=<?php echo $category_id; ?>" class="btn btn-success">
        + Create New Topic
    </a>
</div>

<!-- TOPICS LIST -->
<?php if ($topics && mysqli_num_rows($topics) > 0): ?>

    <?php while ($topic = mysqli_fetch_assoc($topics)): ?>
        <div class="topic">
            <div>
                <h3>
                    <a href="topic.php?id=<?php echo $topic['post_id']; ?>" style="text-decoration:none;color:#333;">
                        <?php echo htmlspecialchars($topic['title']); ?>
                    </a>
                </h3>
                <small>
                    Posted by <?php echo htmlspecialchars($topic['username']); ?> ·
                    <?php echo date("d M Y", strtotime($topic['created_at'])); ?>
                </small>
            </div>

            <div class="badge">
                <?php echo $topic['reply_count']; ?> Replies
            </div>
        </div>
    <?php endwhile; ?>

<?php else: ?>

    <!-- EMPTY STATE -->
    <div class="empty">
        <h2>No discussions yet</h2>
        <p>Be the first one to start a discussion in this category.</p>
        <a href="create_topic.php?cat_id=<?php echo $category_id; ?>" class="btn btn-primary">
            Start New Topic
        </a>
    </div>

<?php endif; ?>

</body>
</html>
