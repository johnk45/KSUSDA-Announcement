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
    $stmt = $pdo->prepare("DELETE FROM weekly_scripture WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_scripture.php');
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $verse_text = $_POST['verse_text'];
    $reference = $_POST['reference'];
    $week_start_date = $_POST['week_start_date'];

    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO weekly_scripture (verse_text, reference, week_start_date) VALUES (?, ?, ?)");
        $stmt->execute([$verse_text, $reference, $week_start_date]);
    } elseif ($action == 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE weekly_scripture SET verse_text = ?, reference = ?, week_start_date = ? WHERE id = ?");
        $stmt->execute([$verse_text, $reference, $week_start_date, $id]);
    }
    header('Location: manage_scripture.php');
    exit;
}

// Fetch data for editing
$scripture = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM weekly_scripture WHERE id = ?");
    $stmt->execute([$id]);
    $scripture = $stmt->fetch();
    if (!$scripture) {
        header('Location: manage_scripture.php');
        exit;
    }
}

// Fetch all scriptures
$scriptures = $pdo->query("SELECT * FROM weekly_scripture ORDER BY week_start_date DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <h2><?php echo $action == 'add' ? 'Add New Weekly Scripture' : 'Edit Scripture'; ?></h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div class="form-group">
            <label>Week Starting Date *</label>
            <input type="date" name="week_start_date" required value="<?php echo $scripture['week_start_date'] ?? ''; ?>" class="form-control">
            <small>Usually the Monday of the week.</small>
        </div>
        <div class="form-group">
            <label>Verse Text *</label>
            <textarea name="verse_text" rows="3" required class="form-control"><?php echo htmlspecialchars($scripture['verse_text'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Reference *</label>
            <input type="text" name="reference" required value="<?php echo htmlspecialchars($scripture['reference'] ?? ''); ?>" class="form-control" placeholder="e.g. Isaiah 40:31 (ESV)">
        </div>
        <button type="submit" class="btn">Save Scripture</button>
        <a href="manage_scripture.php" class="btn btn-outline">Cancel</a>
    </form>
<?php else: ?>
    <h2>Weekly Scriptures</h2>
    <a href="manage_scripture.php?action=add" class="btn" style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add New Scripture</a>
    <table class="data-table">
        <thead>
            <tr>
                <th>Week Start</th>
                <th>Reference</th>
                <th>Verse (excerpt)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scriptures as $s): ?>
            <tr>
                <td><?php echo htmlspecialchars($s['week_start_date']); ?></td>
                <td><?php echo htmlspecialchars($s['reference']); ?></td>
                <td><?php echo htmlspecialchars(substr($s['verse_text'], 0, 50)) . '...'; ?></td>
                <td>
                    <a href="manage_scripture.php?action=edit&id=<?php echo $s['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                    <a href="manage_scripture.php?action=delete&id=<?php echo $s['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
    /* reuse same styles as in manage_bulletins.php */
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