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
    $stmt = $pdo->prepare("DELETE FROM community_news WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_community_news.php');
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'];
    $date_posted = $_POST['date_posted'];
    $attendees_count = $_POST['attendees_count'] ?? 0;

    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO community_news (title, content, category, image_url, date_posted, attendees_count) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $category, $image_url, $date_posted, $attendees_count]);
    } elseif ($action == 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE community_news SET title = ?, content = ?, category = ?, image_url = ?, date_posted = ?, attendees_count = ? WHERE id = ?");
        $stmt->execute([$title, $content, $category, $image_url, $date_posted, $attendees_count, $id]);
    }
    header('Location: manage_community_news.php');
    exit;
}

// Fetch data for editing
$news_item = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM community_news WHERE id = ?");
    $stmt->execute([$id]);
    $news_item = $stmt->fetch();
    if (!$news_item) {
        header('Location: manage_community_news.php');
        exit;
    }
}

// Fetch all news
$all_news = $pdo->query("SELECT * FROM community_news ORDER BY date_posted DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <h2><?php echo $action == 'add' ? 'Add New Community News' : 'Edit Community News'; ?></h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($news_item['title'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" rows="4" required class="form-control"><?php echo htmlspecialchars($news_item['content'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" value="<?php echo htmlspecialchars($news_item['category'] ?? ''); ?>" class="form-control" placeholder="e.g. Welcome, Ministry, Education">
        </div>
        <div class="form-group">
            <label>Image URL</label>
            <input type="text" name="image_url" value="<?php echo htmlspecialchars($news_item['image_url'] ?? ''); ?>" class="form-control" placeholder="https://...">
        </div>
        <div class="form-group">
            <label>Date Posted *</label>
            <input type="date" name="date_posted" required value="<?php echo $news_item['date_posted'] ?? date('Y-m-d'); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Attendees Count (optional)</label>
            <input type="number" name="attendees_count" value="<?php echo $news_item['attendees_count'] ?? 0; ?>" class="form-control">
        </div>
        <button type="submit" class="btn">Save News</button>
        <a href="manage_community_news.php" class="btn btn-outline">Cancel</a>
    </form>
<?php else: ?>
    <h2>Community News</h2>
    <a href="manage_community_news.php?action=add" class="btn" style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add New News</a>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Category</th>
                <th>Attendees</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_news as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['date_posted']); ?></td>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td><?php echo htmlspecialchars($item['category']); ?></td>
                <td><?php echo $item['attendees_count'] ?: '-'; ?></td>
                <td>
                    <a href="manage_community_news.php?action=edit&id=<?php echo $item['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                    <a href="manage_community_news.php?action=delete&id=<?php echo $item['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
    /* same styles as before */
    .form-group { margin-bottom: 20px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
    .btn { background: #0056a6; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; margin-right: 10px; }
    .btn-outline { background: white; color: #0056a6; border: 2px solid #0056a6; }
    .data-table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .data-table th { background: #0056a6; color: white; }
    .action-btn { color: #0056a6; margin: 0 5px; font-size: 1.2rem; }
    .action-btn.delete { color: #d32f2f; }
</style>

<?php include 'includes/footer.php'; ?>