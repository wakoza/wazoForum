<?php
session_start();
include("../db_connection.php");
include("../function.php");

// Check if user is logged in and is admin
$user = getCurrentUser();
if (!isLogedIn() || !$user || $user['role'] !== 'admin') {
    header("Location: ../login.php?message=Admin access required");
    exit;
}

// Get search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_clause = "WHERE username LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%'";
}

// Fetch deleted users
$sql = "SELECT dm.*, u.username as deleted_by_admin FROM deleted_members dm 
        LEFT JOIN users u ON dm.deleted_by = u.user_id 
        $where_clause 
        ORDER BY dm.deleted_at DESC";
$results = mysqli_query($conn, $sql);
$deleted_members = mysqli_fetch_all($results, MYSQLI_ASSOC);

// Get total deleted count
$count_sql = "SELECT COUNT(*) as total FROM deleted_members";
$count_result = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);
$total_deleted = $count_row['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Members - Admin Panel</title>
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

        .nav-links {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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

        .search-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-reset {
            background: #e0e0e0;
            color: #333;
        }

        .btn-reset:hover {
            background: #d0d0d0;
        }

        .table-section {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .table-header {
            background: #f8f9fa;
            padding: 15px 20px;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
            font-size: 14px;
        }

        table td {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        .username-badge {
            background: #dfe9ff;
            color: #667eea;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            font-size: 12px;
        }

        .role-admin {
            background: #ffe0e0;
            color: #d32f2f;
        }

        .role-member {
            background: #e0f2e0;
            color: #388e3c;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 24px;
            }

            .search-form {
                flex-direction: column;
            }

            .search-input {
                min-width: 100%;
            }

            table {
                font-size: 12px;
            }

            table th,
            table td {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="container">
        <div class="nav-links">
            <a href="../dashboad.php" class="nav-link">
                Dashboard
            </a>
            <a href="manageuser.php" class="nav-link">
                Manage Members
            </a>
            <a href="../index.php" class="nav-link">
                Back to Forum
            </a>
        </div>

        <div class="header-section">
            <h1>
                Deleted Members Log
            </h1>
            <p>View all members deleted by administrators</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-label">Deleted Members</div>
                    <div class="stat-card-number"><?php echo $total_deleted; ?></div>
                </div>
            </div>
        </div>

        <div class="search-section">
            <form method="GET" class="search-form">
                <input 
                    type="text" 
                    class="search-input" 
                    name="search" 
                    placeholder="Search deleted members by username or email..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <button type="submit" class="btn btn-search">
                    Search
                </button>
            </form>
            <?php if (!empty($search)): ?>
                <a href="deleted_members.php" class="btn btn-reset" style="margin-top: 10px; display: inline-block;">
                    Clear
                </a>
            <?php endif; ?>
        </div>

        <div class="table-section">
            <?php if (count($deleted_members) > 0): ?>
                <div class="table-header">
                    Showing <?php echo count($deleted_members); ?> deleted member<?php echo count($deleted_members) !== 1 ? 's' : ''; ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Member Since</th>
                            <th>Deleted On</th>
                            <th>Deleted By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deleted_members as $member): ?>
                            <tr>
                                <td>
                                    <span class="username-badge">
                                        <?php echo htmlspecialchars($member['username']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo strtolower($member['role']); ?>">
                                        <?php echo ucfirst($member['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($member['deleted_at'])); ?></td>
                                <td><?php echo htmlspecialchars($member['deleted_by_admin'] ?? 'Unknown'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>No deleted members found</p>
                    <p style="font-size: 14px; opacity: 0.7;">All deleted members will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
