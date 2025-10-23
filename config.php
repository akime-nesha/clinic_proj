<?php
// config.php
session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'clinic2_db';
$DB_USER = 'root';
$DB_PASS = ''; // change if needed

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}


function current_user()
{
    return $_SESSION['user'] ?? null;
}

function require_login()
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
