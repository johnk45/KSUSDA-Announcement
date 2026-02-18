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
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_events.php');
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, event_time, location, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $event_date, $event_time, $location, $description]);
    } elseif ($action == 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE events SET title = ?, event_date = ?, event_time = ?, location = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $event_date, $event_time, $location, $description, $id]);
    }
    header('Location: manage_events.php');
    exit;
}

// Fetch data for editing
$event = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if (!$event) {
        header('Location: manage_events.php');
        exit;
    }
}

// Fetch all events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <h2><?php echo $action == 'add' ? 'Add New Event' : 'Edit Event'; ?></h2>
    <form method="post" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Event Date *</label>
            <input type="date" name="event_date" required value="<?php echo $event['event_date'] ?? ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Event Time (e.g. 6:00 PM)</label>
            <input type="text" name="event_time" value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($event['location'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn">Save Event</button>
        <a href="manage_events.php" class="btn btn-outline">Cancel</a>
    </form>
<?php else: ?>
    <h2>Events</h2>
    <a href="manage_events.php?action=add" class="btn" style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add New Event</a>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Time</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
                <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                <td><?php echo htmlspecialchars($e['title']); ?></td>
                <td><?php echo htmlspecialchars($e['event_time']); ?></td>
                <td><?php echo htmlspecialchars($e['location']); ?></td>
                <td>
                    <a href="manage_events.php?action=edit&id=<?php echo $e['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                    <a href="manage_events.php?action=delete&id=<?php echo $e['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<style>
    /* same styles */
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