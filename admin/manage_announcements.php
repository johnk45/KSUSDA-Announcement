<?php
require_once '../config.php';
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// Handle delete
if ($action == 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_announcements.php');
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $priority = isset($_POST['priority']) ? 1 : 0;
    $date_posted = $_POST['date_posted'];
    $deadline = $_POST['deadline'] ?: null;
    $link_text = $_POST['link_text'] ?: null;
    $link_url = $_POST['link_url'] ?: null;

    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, priority, date_posted, deadline, link_text, link_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $priority, $date_posted, $deadline, $link_text, $link_url]);
    } elseif ($action == 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ?, priority = ?, date_posted = ?, deadline = ?, link_text = ?, link_url = ? WHERE id = ?");
        $stmt->execute([$title, $content, $priority, $date_posted, $deadline, $link_text, $link_url, $id]);
    }
    header('Location: manage_announcements.php');
    exit;
}

// Fetch data for editing
$announcement = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->execute([$id]);
    $announcement = $stmt->fetch();
    if (!$announcement) {
        header('Location: manage_announcements.php');
        exit;
    }
}

// Fetch all announcements for list view
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY date_posted DESC, priority DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <h2><?php echo $action == 'add' ? 'Add New Announcement' : 'Edit Announcement'; ?></h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($announcement['title'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" rows="5" required class="form-control"><?php echo htmlspecialchars($announcement['content'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="priority" value="1" <?php echo ($announcement['priority'] ?? 0) ? 'checked' : ''; ?>>
                Mark as Urgent / Priority
            </label>
        </div>
        <div class="form-group">
            <label>Date Posted *</label>
            <input type="date" name="date_posted" required value="<?php echo $announcement['date_posted'] ?? date('Y-m-d'); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Deadline (optional)</label>
            <input type="date" name="deadline" value="<?php echo $announcement['deadline'] ?? ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Link Text (optional, e.g. 'Read More')</label>
            <input type="text" name="link_text" value="<?php echo htmlspecialchars($announcement['link_text'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Link URL (optional)</label>
            <input type="url" name="link_url" value="<?php echo htmlspecialchars($announcement['link_url'] ?? ''); ?>" class="form-control" placeholder="https://...">
        </div>
        <button type="submit" class="btn">Save Announcement</button>
        <a href="manage_announcements.php" class="btn btn-outline">Cancel</a>
    </form>
<?php else: ?>
    <h2>Announcements</h2>
    <a href="manage_announcements.php?action=add" class="btn" style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add New Announcement</a>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Priority</th>
                <th>Deadline</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($announcements as $a): ?>
            <tr>
                <td><?php echo htmlspecialchars($a['date_posted']); ?></td>
                <td><?php echo htmlspecialchars($a['title']); ?></td>
                <td><?php echo $a['priority'] ? '<span style="color:red; font-weight:bold;">Urgent</span>' : 'Normal'; ?></td>
                <td><?php echo $a['deadline'] ? htmlspecialchars($a['deadline']) : '-'; ?></td>
                <td>
                    <a href="manage_announcements.php?action=edit&id=<?php echo $a['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                    <a href="manage_announcements.php?action=delete&id=<?php echo $a['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
    .form-group { margin-bottom: 20px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
    .btn { background: #0056a6; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; margin-right: 10px; cursor: pointer; }
    .btn-outline { background: white; color: #0056a6; border: 2px solid #0056a6; }
    .data-table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .data-table th { background: #0056a6; color: white; }
    .action-btn { color: #0056a6; margin: 0 5px; font-size: 1.2rem; text-decoration: none; }
    .action-btn.delete { color: #d32f2f; }
</style>

<?php include 'includes/footer.php'; ?>