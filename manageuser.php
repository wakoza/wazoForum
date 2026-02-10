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

// Store user info early to prevent losing it during deletion
$admin_user_id = $user['user_id'];

// Handle member deletion
$deleted_msg = '';
$delete_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_id'])) {
    $delete_id = (int)$_POST['delete_user_id'];
    
    // Prevent admin from deleting themselves
    if ($delete_id === (int)$admin_user_id) {
        $delete_error = "You cannot delete your own account!";
    } else {
        // Delete user and their posts/replies
        $conn->begin_transaction();
        try {
            // Delete replies from this user
            $del_replies = "DELETE FROM replies WHERE user_id = $delete_id";
            mysqli_query($conn, $del_replies);
            
            // Delete posts from this user
            $del_posts = "DELETE FROM posts WHERE user_id = $delete_id";
            mysqli_query($conn, $del_posts);
            
            // Delete the user
            $del_user = "DELETE FROM users WHERE user_id = $delete_id";
            if (mysqli_query($conn, $del_user)) {
                $conn->commit();
                $deleted_msg = "✓ Member deleted successfully!";
            }
        } catch (Exception $e) {
            $conn->rollback();
            $delete_error = "Error deleting member: " . $e->getMessage();
        }
    }
}

// Get search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_clause = "WHERE username LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%'";
}

// Fetch users
$sql = "SELECT * FROM users $where_clause ORDER BY created_at DESC";
$results = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($results, MYSQLI_ASSOC);

// Get total member count
$count_sql = "SELECT COUNT(*) as total FROM users";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$total_members = $count_row['total'];

// Get admin count
$admin_sql = "SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin'";
$admin_result = mysqli_query($conn, $admin_sql);
$admin_row = mysqli_fetch_assoc($admin_result);
$admin_count = $admin_row['admin_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manage Members - Admin Panel</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-section {
            background: #667eea;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .stat-card-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-card-number {
            font-size: 28px;
            font-weight: 700;
        }

        .controls-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .search-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            display: flex;
            gap: 8px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-search {
            background: #667eea;
            color: white;
        }

        .btn-search:hover {
            background: #5568d3;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-reset {
            background: #f0f2f5;
            color: #333;
        }

        .btn-reset:hover {
            background: #e4e6eb;
        }

        .alerts {
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-color: #4caf50;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-color: #f44336;
        }

        .table-section {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .table-header {
            background: #f5f7fa;
            padding: 20px;
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
            color: #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: #f5f7fa;
        }

        table th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #667eea;
            border-bottom: 2px solid #e0e0e0;
        }

        table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f2f5;
        }

        table tbody tr {
            transition: background 0.2s ease;
        }

        table tbody tr:hover {
            background: #f5f7fa;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .username {
            font-weight: 600;
            color: #333;
        }

        .email {
            font-size: 13px;
            color: #999;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            width: fit-content;
        }

        .role-admin {
            background: #fff3cd;
            color: #856404;
        }

        .role-member {
            background: #d1ecf1;
            color: #0c5460;
        }

        .date-joined {
            color: #999;
            font-size: 13px;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-delete {
            background: #ffebee;
            color: #c62828;
        }

        .btn-delete:hover {
            background: #ef5350;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #666;
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .modal-body {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-confirm {
            background: #f44336;
            color: white;
            padding: 10px 20px;
        }

        .btn-confirm:hover {
            background: #da190b;
        }

        .btn-cancel {
            background: #f0f2f5;
            color: #333;
            padding: 10px 20px;
        }

        .btn-cancel:hover {
            background: #e4e6eb;
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .search-container {
                flex-direction: column;
            }

            .search-box {
                width: 100%;
            }

            table {
                font-size: 13px;
            }

            table th, table td {
                padding: 10px 12px;
            }

            .action-btn {
                padding: 6px 12px;
                font-size: 11px;
            }

            .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Forum
        </a>

        <div class="header-section">
            <h1>
                <i class=""></i> Member Management
            </h1>
            <p>View, search, and manage all forum members</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-label">Total Members</div>
                    <div class="stat-card-number"><?php echo $total_members; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Administrators</div>
                    <div class="stat-card-number"><?php echo $admin_count; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Regular Members</div>
                    <div class="stat-card-number"><?php echo $total_members - $admin_count; ?></div>
                </div>
            </div>
        </div>

        <div class="alerts">
            <?php if ($deleted_msg): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $deleted_msg; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($delete_error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $delete_error; ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="controls-section">
            <div class="search-container">
                <form method="GET" style="display: flex; gap: 10px; flex: 1; min-width: 250px;">
                    <div class="search-box">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by username or email..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                        <button type="submit" class="btn btn-search">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
                <?php if (!empty($search)): ?>
                    <a href="manageuser.php" class="btn btn-reset">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-section">
            <?php if (count($users) > 0): ?>
                <div class="table-header">
                    Showing <?php echo count($users); ?> member<?php echo count($users) !== 1 ? 's' : ''; ?>
                    <?php if (!empty($search)): ?>
                        <span style="color: #666;"> matching "<?php echo htmlspecialchars($search); ?>"</span>
                    <?php endif; ?>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $member): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper($member['username'][0]); ?>
                                        </div>
                                        <div class="user-details">
                                            <div class="username"><?php echo htmlspecialchars($member['username']); ?></div>
                                            <div class="email"><?php echo htmlspecialchars($member['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge <?php echo $member['role'] === 'admin' ? 'role-admin' : 'role-member'; ?>">
                                        <?php echo ucfirst($member['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td>
                                    <div class="date-joined">
                                        <?php echo date('M d, Y', strtotime($member['created_at'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ((int)$member['user_id'] !== (int)$user['user_id']): ?>
                                        <button 
                                            class="action-btn btn-delete"
                                            onclick="confirmDelete(<?php echo $member['user_id']; ?>, '<?php echo htmlspecialchars($member['username']); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 12px;">Your Account</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <h3>No members found</h3>
                    <p>
                        <?php if (!empty($search)): ?>
                            No members match your search: "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            No members in the system yet.
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Delete Member</div>
            <div class="modal-body">
                <p>Are you sure you want to delete member: <strong id="memberName"></strong>?</p>
                <p style="margin-top: 10px; color: #f44336; font-weight: 600;">This will also delete all their posts and replies.</p>
                <p style="margin-top: 10px; font-size: 12px; color: #999;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeModal()">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" id="deleteUserId" name="delete_user_id">
                    <button type="submit" class="btn btn-confirm">Delete Member</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId, username) {
            document.getElementById('memberName').textContent = username;
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>