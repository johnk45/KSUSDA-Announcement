<?php
require_once 'config.php';

// Fetch latest announcements (limit 5)
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY priority DESC, date_posted DESC LIMIT 5")->fetchAll();

// Fetch upcoming events (next 5)
$events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5")->fetchAll();

// Fetch latest bulletin (most recent)
$bulletin = $pdo->query("SELECT * FROM bulletins ORDER BY bulletin_date DESC LIMIT 1")->fetch();

// Fetch public prayer requests (limit 5)
$prayers = $pdo->query("SELECT * FROM prayer_requests WHERE privacy != 'private' ORDER BY date_submitted DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church News & Updates | Kisii University SDA Church</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Source+Sans+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="image/assetss/kisii sda logo.png">
    <style>
        /* Root variables and base styles (same as before) */
        :root {
            --sda-blue: #0056a6;
            --sda-purple: #37337d;
            --sda-gold: #0b2036;
            --sda-green: #2e7d32;
            --light-blue: #e3f2fd;
            --light-gray: #f5f5f5;
            --dark-text: #212121;
            --accent-red: #d32f2f;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: #222;
            line-height: 1.6;
            background: linear-gradient(-45deg, #e3f2fd, #cfd8dc, #bbdefb, #90caf9);
            background-size: 400% 400%;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--sda-blue);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Church Header */
        .church-header {
            background: linear-gradient(135deg, #001f3f 0%, #0f172a 100%);
            color: #ffffff;
            padding: 15px 0 20px;
            text-align: center;
            border-bottom: 5px solid navy;
        }
        
        
        .church-name {
            font-size: 2.2rem;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            color: white;
        }
        
        .church-location {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .church-motto {
            font-style: italic;
            font-size: 1.1rem;
            color: #ffd700;
            margin-top: 10px;
            font-weight: 500;
        }
        
        /* Page Header */
        .page-header {
            background-color: white;
            padding: 30px 0;
            text-align: center;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            border-bottom: 3px solid var(--sda-purple);
        }
        
        .page-title {
            font-size: 2.5rem;
            color: var(--sda-purple);
            margin-bottom: 10px;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            color: var(--dark-text);
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Section Styling */
        .section {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--light-blue);
        }
        
        .section-title i {
            color: var(--sda-gold);
            font-size: 1.5rem;
        }
        
        .view-all {
            margin-left: auto;
            font-size: 0.9rem;
            color: var(--sda-blue);
            text-decoration: none;
            font-weight: 600;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        /* Announcement Cards (reused) */
        .announcement-card {
            background-color: var(--light-gray);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid var(--sda-gold);
            transition: var(--transition);
        }
        
        .announcement-card.priority {
            border-left-color: var(--accent-red);
            background: #fff8f8;
        }
        
        .announcement-priority {
            padding: 5px 15px;
            background-color: var(--accent-red);
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            border-radius: 0 0 8px 0;
        }
        
        .announcement-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        
        .announcement-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--sda-blue);
        }
        
        .announcement-content {
            margin-bottom: 15px;
        }
        
        .announcement-link {
            color: var(--sda-blue);
            font-weight: 600;
            text-decoration: none;
        }
        
        .announcement-link:hover {
            text-decoration: underline;
        }
        
        /* Event List */
        .event-list {
            list-style: none;
        }
        
        .event-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .event-item:last-child {
            border-bottom: none;
        }
        
        .event-date {
            min-width: 100px;
            font-weight: 600;
            color: var(--sda-purple);
        }
        
        .event-details {
            flex: 1;
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .event-time, .event-location {
            font-size: 0.9rem;
            color: #666;
        }
        
        /* Bulletin Card */
        .bulletin-card {
            background-color: var(--light-blue);
            border-radius: var(--border-radius);
            padding: 20px;
            border-left: 5px solid var(--sda-blue);
        }
        
        .bulletin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .bulletin-date {
            background-color: var(--sda-purple);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .bulletin-download {
            background: var(--sda-blue);
            color: white;
            padding: 8px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .bulletin-download:hover {
            background: var(--sda-purple);
        }
        
        .bulletin-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .bulletin-item {
            padding: 10px;
            background: white;
            border-radius: var(--border-radius);
        }
        
        /* Prayer Request Cards */
        .prayer-item {
            background-color: var(--light-gray);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border-left: 4px solid var(--sda-purple);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
        }
        
        .prayer-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .prayer-name {
            font-weight: 600;
            color: var(--sda-blue);
        }
        
        .prayer-request {
            margin-bottom: 15px;
        }
        
        .prayer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pray-count {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--sda-green);
            font-weight: 600;
        }
        
        .answered-prayer {
            background-color: #e8f5e9;
            border-left-color: var(--sda-green);
            padding: 15px;
            margin-top: 10px;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }
        
        /* Footer (same as before) */
        .footer {
            background: #0f172a;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-section h3 {
            color: #38bdf8;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #ddd;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: #ffd700;
            padding-left: 5px;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #ccc;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .church-name { font-size: 1.8rem; }
            .page-title { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <!-- Church Header -->
    <header class="church-header">
        <div class="container">
           
                    <h1 class="church-name">Kisii University Seventh-day Adventist Church</h1>
                    <p class="church-location">Kisii, Kenya • <?php echo date('Y'); ?></p>
                </div>
            </div>
            <p class="church-motto">"Preparing for Eternity, Serving in Community"</p>
        </div>
    </header>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h2 class="page-title">Church News & Updates</h2>
            <p class="page-subtitle">Stay connected with announcements, events, bulletins, and prayer requests.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Announcements Section -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-bullhorn"></i>
                <h3>Latest Announcements</h3>
                <a href="ann.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $a): ?>
                    <div class="announcement-card <?php echo $a['priority'] ? 'priority' : ''; ?>">
                        <?php if ($a['priority']): ?>
                            <div class="announcement-priority">URGENT</div>
                        <?php endif; ?>
                        <div class="announcement-meta">
                            <span><i class="fas fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($a['date_posted'])); ?></span>
                            <?php if ($a['deadline']): ?>
                                <span><i class="fas fa-hourglass-end"></i> Deadline: <?php echo date('F j, Y', strtotime($a['deadline'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <h4 class="announcement-title"><?php echo htmlspecialchars($a['title']); ?></h4>
                        <div class="announcement-content"><?php echo nl2br(htmlspecialchars(substr($a['content'], 0, 200))) . (strlen($a['content']) > 200 ? '...' : ''); ?></div>
                        <?php if ($a['link_url']): ?>
                            <a href="<?php echo htmlspecialchars($a['link_url']); ?>" class="announcement-link"><?php echo htmlspecialchars($a['link_text'] ?: 'Read More'); ?> <i class="fas fa-arrow-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No announcements at this time.</p>
            <?php endif; ?>
        </div>

        <!-- Upcoming Events Section -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-calendar-alt"></i>
                <h3>Upcoming Events</h3>
                <a href="events.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php if (count($events) > 0): ?>
                <ul class="event-list">
                    <?php foreach ($events as $e): ?>
                        <li class="event-item">
                            <div class="event-date"><?php echo date('M j, Y', strtotime($e['event_date'])); ?></div>
                            <div class="event-details">
                                <div class="event-title"><?php echo htmlspecialchars($e['title']); ?></div>
                                <?php if ($e['event_time']): ?>
                                    <div class="event-time"><i class="fas fa-clock"></i> <?php echo "Time: " . htmlspecialchars($e['event_time']); ?></div>
                                <?php endif; ?>
                                <?php if ($e['location']): ?>
                                    <div class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo "Location:" .htmlspecialchars($e['location']); ?></div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No upcoming events scheduled.</p>
            <?php endif; ?>
        </div>

        <!-- Latest Bulletin Section -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-newspaper"></i>
                <h3>Latest Bulletin</h3>
                <a href="bulletins.php" class="view-all">View Archives <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php if ($bulletin): ?>
                <div class="bulletin-card">
                    <div class="bulletin-header">
                        <div>
                            <h4>Sabbath Service - <?php echo date('F j, Y', strtotime($bulletin['bulletin_date'])); ?></h4>
                            <?php if ($bulletin['theme']): ?>
                                <div class="bulletin-date">Theme: <?php echo htmlspecialchars($bulletin['theme']); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($bulletin['pdf_path']): ?>
                            <a href="<?php echo htmlspecialchars($bulletin['pdf_path']); ?>" class="bulletin-download" download><i class="fas fa-download"></i> Download PDF</a>
                        <?php endif; ?>
                    </div>
                    <div class="bulletin-details">
                        <?php if ($bulletin['presiding_elder']): ?>
                            <div class="bulletin-item">
                                <strong><i class="fas fa-user"></i> Presiding Elder:</strong> <?php echo htmlspecialchars($bulletin['presiding_elder']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($bulletin['sermon_speaker']): ?>
                            <div class="bulletin-item">
                                <strong><i class="fas fa-microphone"></i> Sermon:</strong> <?php echo htmlspecialchars($bulletin['sermon_speaker']); ?>
                                <?php if ($bulletin['sermon_title']): ?> – <?php echo htmlspecialchars($bulletin['sermon_title']); ?><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($bulletin['special_music']): ?>
                            <div class="bulletin-item">
                                <strong><i class="fas fa-music"></i> Special Music:</strong> <?php echo htmlspecialchars($bulletin['special_music']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($bulletin['schedule_text']): ?>
                            <div class="bulletin-item">
                                <strong><i class="fas fa-calendar-check"></i> Schedule:</strong>
                                <div><?php echo nl2br(htmlspecialchars($bulletin['schedule_text'])); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p>No bulletin available.</p>
            <?php endif; ?>
        </div>

        <!-- Prayer Requests Section -->
        <div class="section">
            <div class="section-title">
                <i class="fas fa-pray"></i>
                <h3>Prayer Requests</h3>
                <a href="prayer_wall.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php if (count($prayers) > 0): ?>
                <?php foreach ($prayers as $p): ?>
                    <div class="prayer-item">
                        <div class="prayer-header">
                            <span class="prayer-name">
                                <?php if ($p['privacy'] == 'public' && $p['name']): ?>
                                    <?php echo htmlspecialchars($p['name']); ?>
                                <?php elseif ($p['privacy'] == 'anonymous'): ?>
                                    Anonymous
                                <?php else: ?>
                                    Private Request
                                <?php endif; ?>
                            </span>
                            <span><?php echo date('M j, Y', strtotime($p['date_submitted'])); ?></span>
                        </div>
                        <div class="prayer-request"><?php echo nl2br(htmlspecialchars(substr($p['request'], 0, 150))) . (strlen($p['request']) > 150 ? '...' : ''); ?></div>
                        <?php if ($p['answered'] && $p['answer_description']): ?>
                            <div class="answered-prayer">
                                <i class="fas fa-star" style="color: var(--sda-gold);"></i> 
                                <strong>Answered:</strong> <?php echo htmlspecialchars($p['answer_description']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="prayer-actions">
                            <span class="pray-count"><i class="fas fa-hands-praying"></i> <?php echo $p['prayed_count']; ?> prayed</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No prayer requests shared.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Kisii University SDA Church</h3>
                    <p>Seventh-day Adventist Church serving the Kisii University community and surrounding area since 2010.</p>
                    <p style="margin-top: 15px;"><i class="fas fa-map-marker-alt"></i> Kisii University Campus, Kisii, Kenya</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Sermons</a></li>
                        <li><a href="#">Ministries</a></li>
                        <li><a href="news.php">News & Updates</a></li>
                        <li><a href="#">Give</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <div style="display: flex; gap: 15px; margin-top: 15px;">
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
                    </div>
                    <p style="margin-top: 20px;">
                        <strong>Sabbath Services:</strong><br>
                        Sabbath School: 9:30 AM<br>
                        Divine Service: 11:00 AM
                    </p>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Kisii University Seventh-day Adventist Church. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>