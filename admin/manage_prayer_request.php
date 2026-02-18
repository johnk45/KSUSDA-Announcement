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
    $stmt = $pdo->prepare("DELETE FROM prayer_requests WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_prayer_requests.php');
    exit;
}

// Handle mark as answered
if ($action == 'answer' && $id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $answer_description = $_POST['answer_description'] ?? '';
        $stmt = $pdo->prepare("UPDATE prayer_requests SET answered = 1, answer_description = ? WHERE id = ?");
        $stmt->execute([$answer_description, $id]);
        header('Location: manage_prayer_requests.php');
        exit;
    } else {
        // Show form to enter answer description
        $stmt = $pdo->prepare("SELECT * FROM prayer_requests WHERE id = ?");
        $stmt->execute([$id]);
        $prayer = $stmt->fetch();
        if (!$prayer) {
            header('Location: manage_prayer_requests.php');
            exit;
        }
    }
}

// Handle edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'edit') {
    $name = $_POST['name'];
    $request = $_POST['request'];
    $privacy = $_POST['privacy'];
    $date_submitted = $_POST['date_submitted'];
    $prayed_count = $_POST['prayed_count'];
    $answered = isset($_POST['answered']) ? 1 : 0;
    $answer_description = $_POST['answer_description'] ?? '';

    $stmt = $pdo->prepare("UPDATE prayer_requests SET name = ?, request = ?, privacy = ?, date_submitted = ?, prayed_count = ?, answered = ?, answer_description = ? WHERE id = ?");
    $stmt->execute([$name, $request, $privacy, $date_submitted, $prayed_count, $answered, $answer_description, $id]);
    header('Location: manage_prayer_requests.php');
    exit;
}

// Fetch data for editing
$prayer = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM prayer_requests WHERE id = ?");
    $stmt->execute([$id]);
    $prayer = $stmt->fetch();
    if (!$prayer) {
        header('Location: manage_prayer_requests.php');
        exit;
    }
}

// Fetch all prayer requests
$prayers = $pdo->query("SELECT * FROM prayer_requests ORDER BY date_submitted DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($action == 'answer' && $id): ?>
    <h2>Mark Prayer as Answered</h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px;">
        <p><strong>Request:</strong> <?php echo htmlspecialchars($prayer['request']); ?></p>
        <div class="form-group">
            <label>Answer Description</label>
            <textarea name="answer_description" rows="3" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn">Mark Answered</button>
        <a href="manage_prayer_requests.php" class="btn btn-outline">Cancel</a>
    </form>

<?php elseif ($action == 'edit' && $prayer): ?>
    <h2>Edit Prayer Request</h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px;">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($prayer['name'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Request *</label>
            <textarea name="request" rows="4" required class="form-control"><?php echo htmlspecialchars($prayer['request']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Privacy</label>
            <select name="privacy" class="form-control">
                <option value="private" <?php echo $prayer['privacy'] == 'private' ? 'selected' : ''; ?>>Private (pastoral team only)</option>
                <option value="anonymous" <?php echo $prayer['privacy'] == 'anonymous' ? 'selected' : ''; ?>>Anonymous on prayer wall</option>
                <option value="public" <?php echo $prayer['privacy'] == 'public' ? 'selected' : ''; ?>>Public with name</option>
            </select>
        </div>
        <div class="form-group">
            <label>Date Submitted</label>
            <input type="date" name="date_submitted" value="<?php echo $prayer['date_submitted']; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Prayed Count</label>
            <input type="number" name="prayed_count" value="<?php echo $prayer['prayed_count']; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="answered" value="1" <?php echo $prayer['answered'] ? 'checked' : ''; ?>>
                Answered
            </label>
        </div>
        <div class="form-group">
            <label>Answer Description</label>
            <textarea name="answer_description" rows="2" class="form-control"><?php echo htmlspecialchars($prayer['answer_description'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn">Update</button>
        <a href="manage_prayer_requests.php" class="btn btn-outline">Cancel</a>
    </form>

<?php else: ?>
    <h2>Prayer Requests</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Request (excerpt)</th>
                <th>Privacy</th>
                <th>Prayed</th>
                <th>Answered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prayers as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['date_submitted']); ?></td>
                <td><?php echo $p['privacy'] == 'public' ? htmlspecialchars($p['name']) : ($p['privacy'] == 'anonymous' ? 'Anonymous' : 'Private'); ?></td>
                <td><?php echo htmlspecialchars(substr($p['request'], 0, 50)) . '...'; ?></td>
                <td><?php echo ucfirst($p['privacy']); ?></td>
                <td><?php echo $p['prayed_count']; ?></td>
                <td><?php echo $p['answered'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <a href="manage_prayer_requests.php?action=edit&id=<?php echo $p['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                    <?php if (!$p['answered']): ?>
                        <a href="manage_prayer_requests.php?action=answer&id=<?php echo $p['id']; ?>" class="action-btn" style="color: green;"><i class="fas fa-check-circle"></i></a>
                    <?php endif; ?>
                    <a href="manage_prayer_requests.php?action=delete&id=<?php echo $p['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
    .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    .data-table th { background: #0056a6; color: white; }
    .action-btn { color: #0056a6; margin: 0 5px; font-size: 1.2rem; text-decoration: none; }
    .action-btn.delete { color: #d32f2f; }
</style>

<?php include 'includes/footer.php'; ?>