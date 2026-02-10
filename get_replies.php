<?php
session_start();
include("db_connection.php");
include("function.php");

// Get post_id from query parameter
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'replies' => []]);
    exit;
}

// Function to show time like Instagram
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . " min ago";
    if ($diff < 86400) return floor($diff / 3600) . " hrs ago";
    return floor($diff / 86400) . " days ago";
}

// Fetch replies for this post
$replies_sql = "
    SELECT 
        r.reply_id,
        r.content,
        r.created_at,
        u.username
    FROM replies r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.post_id = $post_id
    ORDER BY r.created_at DESC
";

$replies_result = mysqli_query($conn, $replies_sql);

if (!$replies_result) {
    echo json_encode(['success' => false, 'replies' => []]);
    exit;
}

$replies = [];
while ($reply = mysqli_fetch_assoc($replies_result)) {
    $replies[] = [
        'reply_id' => $reply['reply_id'],
        'username' => htmlspecialchars($reply['username']),
        'content' => nl2br(htmlspecialchars($reply['content'])),
        'time_ago' => timeAgo($reply['created_at'])
    ];
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'replies' => $replies,
    'count' => count($replies)
]);
?>
