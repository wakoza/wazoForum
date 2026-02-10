<?php
session_start();
include("db_connection.php");
include("function.php");

// Check if user is logged in - redirect to login if not
if (!isLogedIn()) {
    header("Location: login.php");
    exit;
}

// Get all categories
$categories_sql = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
$categories = mysqli_query($conn, $categories_sql);

$user = getCurrentUser();
$success_msg = '';
$error_msg = '';

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $post_content = isset($_POST['post_content']) ? trim($_POST['post_content']) : '';
    
    // Validation
    if ($category_id <= 0) {
        $error_msg = "Please select a category";
    } elseif (empty($post_content)) {
        $error_msg = "Post content cannot be empty";
    } elseif (strlen($post_content) > 5000) {
        $error_msg = "Post content cannot exceed 5000 characters";
    } else {
        // Insert post into database
        $insert_sql = "INSERT INTO posts (user_id, category_id, content, created_at) 
                       VALUES ({$user['user_id']}, $category_id, '" . mysqli_real_escape_string($conn, $post_content) . "', NOW())";
        
        if (mysqli_query($conn, $insert_sql)) {
            $new_post_id = mysqli_insert_id($conn);
            $success_msg = "Post created successfully!";
            // Redirect to the post's discussion page after 2 seconds
            header("Refresh: 2; url=replay.php?post_id=$new_post_id");
        } else {
            $error_msg = "Error creating post: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post | E-Forum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin: 0;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 10px;
        }

        .form-container {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 300px;
            line-height: 1.6;
        }

        .char-counter {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        .char-counter.warning {
            color: #ff9800;
        }

        .char-counter.danger {
            color: #f44336;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #f0f2f5;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e4e6eb;
        }

        .btn-secondary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        .alert-icon {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-group textarea {
                min-height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container">
        <div class="header">
            <h1>✏️ Create New Post</h1>
            <p>Share your thoughts with the community</p>
        </div>

        <div class="form-container">
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <span><?php echo htmlspecialchars($success_msg); ?> Redirecting...</span>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">✕</span>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$success_msg): ?>
                <form method="POST" id="createPostForm">
                    <div class="form-group">
                        <label for="category_id">📁 Category *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">-- Select a Category --</option>
                            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="post_content">💬 Post Content *</label>
                        <textarea 
                            id="post_content" 
                            name="post_content" 
                            placeholder="Share your thoughts, ask a question, or start a discussion..."
                            maxlength="5000"
                            required
                        ></textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span> / 5000 characters
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">
                            ← Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            📤 Create Post
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <script>
        const textarea = document.getElementById('post_content');
        const charCount = document.getElementById('charCount');

        if (textarea) {
            textarea.addEventListener('keyup', function() {
                charCount.textContent = this.value.length;
                
                const counter = charCount.parentElement;
                if (this.value.length > 4500) {
                    counter.classList.add('danger');
                    counter.classList.remove('warning');
                } else if (this.value.length > 3500) {
                    counter.classList.add('warning');
                    counter.classList.remove('danger');
                } else {
                    counter.classList.remove('warning', 'danger');
                }
            });
        }

        // Prevent accidental navigation
        document.getElementById('createPostForm').addEventListener('change', function() {
            window.onbeforeunload = function() {
                return 'You have unsaved changes. Are you sure you want to leave?';
            };
        });

        document.getElementById('createPostForm').addEventListener('submit', function() {
            window.onbeforeunload = null;
        });
    </script>
</body>
</html>
