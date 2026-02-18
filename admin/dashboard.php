<?php
require_once '../config.php';
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Fetch some counts for dashboard widgets
$bulletins_count = $pdo->query("SELECT COUNT(*) FROM bulletins")->fetchColumn();
$announcements_count = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$prayer_count = $pdo->query("SELECT COUNT(*) FROM prayer_requests")->fetchColumn();
$events_count = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard-header">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-bullhorn"></i>
        <div class="stat-number"><?php echo $bulletins_count; ?></div>
        <div>Bulletins</div>
        <a href="manage_bulletins.php" class="stat-link">Manage</a>
    </div>
    <div class="stat-card">
        <i class="fas fa-exclamation-circle"></i>
        <div class="stat-number"><?php echo $announcements_count; ?></div>
        <div>Announcements</div>
        <a href="manage_announcements.php" class="stat-link">Manage</a>
    </div>
    <div class="stat-card">
        <i class="fas fa-users"></i>
        <div class="stat-number"><?php echo $prayer_count; ?></div>
        <div>Prayer Requests</div>
        <a href="manage_prayer_requests.php" class="stat-link">Manage</a>
    </div>
    <div class="stat-card">
        <i class="fas fa-calendar-alt"></i>
        <div class="stat-number"><?php echo $events_count; ?></div>
        <div>Events</div>
        <a href="manage_events.php" class="stat-link">Manage</a>
    </div>
</div>

<div class="quick-actions">
    <h2>Quick Actions</h2>
    <div class="action-buttons">
        <a href="manage_bulletins.php?action=add" class="btn"><i class="fas fa-plus"></i> New Bulletin</a>
        <a href="manage_announcements.php?action=add" class="btn"><i class="fas fa-plus"></i> New Announcement</a>
        <a href="manage_community_news.php?action=add" class="btn"><i class="fas fa-plus"></i> New Community News</a>
        <a href="manage_scripture.php" class="btn"><i class="fas fa-bible"></i> Update Weekly Scripture</a>
    </div>
</div>

<style>
    .dashboard-header { margin-bottom: 30px; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
    .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
    .stat-card i { font-size: 2.5rem; color: #0056a6; margin-bottom: 15px; }
    .stat-number { font-size: 2rem; font-weight: 700; color: #333; }
    .stat-link { display: inline-block; margin-top: 15px; color: #0056a6; text-decoration: none; font-weight: 600; }
    .quick-actions { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .action-buttons { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 20px; }
    .btn { background: #0056a6; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: 600; }
    .btn:hover { background: #003d82; }
</style>

<?php include 'includes/footer.php'; ?>