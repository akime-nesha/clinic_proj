<?php
// header.php
require_once 'config.php';
$user = current_user();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Clinic System</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #eef1f5;
            color: #333;
        }

        /* NAVIGATION HEADER */
        .nav {
            background: linear-gradient(135deg, #2a7ae2, #1c5bbf);
            color: white;
            padding: 12px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.4em;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            background: white;
            color: #2a7ae2;
            padding: 3px 6px;
            border-radius: 6px;
            font-weight: bold;
        }

        /* NAV LINKS */
        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 8px;
            font-weight: 500;
            transition: 0.3s;
            padding: 6px 10px;
            border-radius: 4px;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* USER GREETING */
        .user-info {
            font-size: 0.95em;
            color: #e3e3e3;
        }

        /* PAGE CONTAINER */
        .container {
            max-width: 1100px;
            margin: 25px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        a {
            color: #2a7ae2;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f2f6fc;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            background: #2a7ae2;
            color: white;
            text-decoration: none;
            font-size: 0.9em;
            transition: 0.3s;
        }

        .btn:hover {
            background: #1c5bbf;
        }

        .small {
            font-size: 0.9em;
            color: #777;
        }

        /* RESPONSIVE */
        @media(max-width: 768px) {
            .topbar {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .nav-links a {
                display: inline-block;
                margin: 3px 4px;
            }
        }
    </style>
</head>

<body>
    <div class="nav">
        <div class="topbar">
            <div class="logo">
                <span>+</span> Clinic Management System
            </div>
            <div class="nav-links">
                <?php if ($user): ?>
                    <span class="user-info">👋 Hello, <?= htmlspecialchars($user['full_name']) ?></span> |
                    <a href="index.php">🏠 Dashboard</a>
                    <a href="patients.php">🧍 Patients</a>
                    <a href="appointments.php">📅 Appointments</a>
                    <a href="doctors.php">👨‍⚕️ Doctors</a>
                    <a href="medical_records.php">📋 Records</a>
                    <a href="logout.php">🚪 Logout</a>
                <?php else: ?>
                    <a href="login.php">🔐 Login</a>
                    <a href="register.php">📝 Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">