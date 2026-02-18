<?php
require_once 'config.php';

// Latest bulletin
$bulletin = $pdo->query("SELECT * FROM bulletins ORDER BY bulletin_date DESC LIMIT 1")->fetch();

// Upcoming events (next 5)
$events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5")->fetchAll();

// Current weekly scripture (based on current week)
$week_start = date('Y-m-d', strtotime('monday this week')); // or use a simpler approach: just get the most recent one
$scripture = $pdo->prepare("SELECT * FROM weekly_scripture WHERE week_start_date <= ? ORDER BY week_start_date DESC LIMIT 1");
$scripture->execute([$week_start]);
$weekly_scripture = $scripture->fetch();

// Important announcements
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY priority DESC, date_posted DESC LIMIT 3")->fetchAll();

// Community news
$community_news = $pdo->query("SELECT * FROM community_news ORDER BY date_posted DESC LIMIT 3")->fetchAll();

// Public prayer requests
$prayer_requests = $pdo->query("SELECT * FROM prayer_requests WHERE privacy != 'private' ORDER BY date_submitted DESC LIMIT 3")->fetchAll();
?>