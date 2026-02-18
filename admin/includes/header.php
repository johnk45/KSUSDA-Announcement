<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f4f7fb; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #0f172a; color: white; padding: 20px; }
        .sidebar h2 { color: #38bdf8; margin-bottom: 30px; font-size: 1.3rem; text-align: center; }
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin-bottom: 15px; }
        .sidebar ul li a { color: #ddd; text-decoration: none; display: block; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #1e293b; color: white; }
        .main-content { flex: 1; padding: 30px; }
        .top-bar { background: white; padding: 15px 30px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .logout-btn { background: #d32f2f; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-weight: 600; }
        .logout-btn:hover { background: #b71c1c; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Kisii SDA Admin</h2>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage_bulletins.php"><i class="fas fa-bullhorn"></i> Bulletins</a></li>
                <li><a href="manage_announcements.php"><i class="fas fa-exclamation-circle"></i> Announcements</a></li>
                <li><a href="manage_community_news.php"><i class="fas fa-users"></i> Community News</a></li>
                <li><a href="manage_prayer_requests.php"><i class="fas fa-pray"></i> Prayer Requests</a></li>
                <li><a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                <li><a href="manage_scripture.php"><i class="fas fa-bible"></i> Weekly Scripture</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>