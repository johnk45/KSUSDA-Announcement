<?php
session_start();
//This page usually connects the rest of the files with the database
$host = 'localhost';
$dbname = 'kusda_announcements'; //you can change to your preferred database
$username = 'root';      // change to your DB username
$password = '';          // change to your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

?>
