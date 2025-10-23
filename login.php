<?php
require_once 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // password_verify if hashed; plain comparison if plain text is used
    if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role_name']
        ];

        // log activity
        $log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip) VALUES (?, ?, ?)");
        $log->execute([$user['id'], 'Logged in', $_SERVER['REMOTE_ADDR'] ?? '']);

        header('Location: index.php');
        exit;
    } else {
        $errors[] = "Invalid username or password.";
    }
}

include 'header.php';
?>

<div style="max-width: 400px; margin: 60px auto; text-align: center;">
    <div style="background:#fff; padding: 30px 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="color:#2a7ae2; margin-bottom: 20px;">🔐 Login to Clinic System</h2>

        <?php if ($errors): ?>
            <div style="background:#ffe3e3; color:#b30000; padding:10px; border-radius:6px; margin-bottom:15px; text-align:left;">
                <?php foreach ($errors as $e): ?>
                    <p style="margin:4px 0;"><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div style="text-align:left; margin-bottom:15px;">
                <label style="font-weight:bold;">Username</label><br>
                <input type="text" name="username" required
                    style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <div style="text-align:left; margin-bottom:20px;">
                <label style="font-weight:bold;">Password</label><br>
                <input type="password" name="password" required
                    style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <button type="submit"
                style="width:100%; padding:10px; background:#2a7ae2; color:white; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">
                Login
            </button>

            <p style="margin-top:15px; color:#555;">
                Don’t have an account? <a href="register.php" style="color:#2a7ae2; font-weight:bold;">Register here</a>.
            </p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>