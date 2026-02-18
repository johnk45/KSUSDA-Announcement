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
    $stmt = $pdo->prepare("DELETE FROM bulletins WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_bulletins.php');
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bulletin_date = $_POST['bulletin_date'];
    $theme = $_POST['theme'];
    $presiding_elder = $_POST['presiding_elder'];
    $sermon_speaker = $_POST['sermon_speaker'];
    $sermon_title = $_POST['sermon_title'];
    $special_music = $_POST['special_music'];
    $schedule_text = $_POST['schedule_text'];
    $pdf_path = $_POST['pdf_path']; // simplified; you might want file upload

    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO bulletins (bulletin_date, theme, presiding_elder, sermon_speaker, sermon_title, special_music, schedule_text, pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$bulletin_date, $theme, $presiding_elder, $sermon_speaker, $sermon_title, $special_music, $schedule_text, $pdf_path]);
    } elseif ($action == 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE bulletins SET bulletin_date = ?, theme = ?, presiding_elder = ?, sermon_speaker = ?, sermon_title = ?, special_music = ?, schedule_text = ?, pdf_path = ? WHERE id = ?");
        $stmt->execute([$bulletin_date, $theme, $presiding_elder, $sermon_speaker, $sermon_title, $special_music, $schedule_text, $pdf_path, $id]);
    }
    header('Location: manage_bulletins.php');
    exit;
}

// Fetch data for editing
$bulletin = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM bulletins WHERE id = ?");
    $stmt->execute([$id]);
    $bulletin = $stmt->fetch();
    if (!$bulletin) {
        header('Location: manage_bulletins.php');
        exit;
    }
}

// Fetch all bulletins for list view
$bulletins = $pdo->query("SELECT * FROM bulletins ORDER BY bulletin_date DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <h2><?php echo $action == 'add' ? 'Add New Bulletin' : 'Edit Bulletin'; ?></h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div class="form-group">
            <label>Bulletin Date *</label>
            <input type="date" name="bulletin_date" required value="<?php echo $bulletin['bulletin_date'] ?? ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Theme</label>
            <input type="text" name="theme" value="<?php echo htmlspecialchars($bulletin['theme'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Presiding Elder</label>
            <input type="text" name="presiding_elder" value="<?php echo htmlspecialchars($bulletin['presiding_elder'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Sermon Speaker</label>
            <input type="text" name="sermon_speaker" value="<?php echo htmlspecialchars($bulletin['sermon_speaker'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Sermon Title</label>
            <input type="text" name="sermon_title" value="<?php echo htmlspecialchars($bulletin['sermon_title'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Special Music</label>
            <input type="text" name="special_music" value="<?php echo htmlspecialchars($bulletin['special_music'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Schedule (text)</label>
            <textarea name="schedule_text" rows="4" class="form-control"><?php echo htmlspecialchars($bulletin['schedule_text'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>PDF Path (relative to root)</label>
            <input type="text" name="pdf_path" value="<?php echo htmlspecialchars($bulletin['pdf_path'] ?? ''); ?>" class="form-control" placeholder="e.g. pdfs/bulletin-april12.pdf">
        </div>
        <button type="submit" class="btn">Save Bulletin</button>
        <a href="manage_bulletins.php" class="btn btn-outline">Cancel</a>
    </form>
<?php else: ?>
    <h2>Bulletins</h2>
    <a href="manage_bulletins.php?action=add" class="btn" style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add New Bulletin</a>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Theme</th>
                <th>Sermon Speaker</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bulletins as $b): ?>
            <tr>
                <td><?php echo htmlspecialchars($b['bulletin_date']); ?></td>
                <td><?php echo htmlspecialchars($b['theme']); ?></td>
                <td><?php echo htmlspecialchars($b['sermon_speaker']); ?></td>
                <td>
                    <a href="manage_bulletins.php?action=edit&id=<?php echo $b['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                    <a href="manage_bulletins.php?action=delete&id=<?php echo $b['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
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