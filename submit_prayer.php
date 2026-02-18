<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$request = trim($_POST['request'] ?? '');
$privacy = $_POST['privacy'] ?? 'private';
$date_submitted = date('Y-m-d');

if (empty($request)) {
    echo json_encode(['success' => false, 'error' => 'Prayer request cannot be empty']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO prayer_requests (name, request, privacy, date_submitted) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$name ?: null, $request, $privacy, $date_submitted]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database insert failed']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}