<?php
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $role_id = (int)$_POST['role_id'];

    if (!$username || !$password) {
        $errors[] = "Username and password are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username already taken.";
        } else {
            // Plain text password if required by your instructor, otherwise keep it hashed
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $full_name, $role_id]);
            header('Location: login.php');
            exit;
        }
    }
}

$roles = $pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
include 'header.php';
?>

<div style="max-width: 600px; margin: 30px auto;">
    <h2 style="color:#2a7ae2;text-align:center;">📝 User Registration</h2>

    <?php if ($errors): ?>
        <div style="background:#ffe3e3;color:#b30000;padding:10px 15px;border-radius:6px;margin-bottom:15px;">
            <?php foreach ($errors as $e): ?>
                <p style="margin:4px 0;"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="background:#fff;padding:25px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1)">
        <label style="display:block;margin-bottom:10px;">
            <strong>Full Name</strong><br>
            <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
        </label>

        <label style="display:block;margin-bottom:10px;">
            <strong>Username</strong><br>
            <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
        </label>

        <label style="display:block;margin-bottom:10px;">
            <strong>Password</strong><br>
            <input type="password" name="password"
                style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
        </label>

        <label style="display:block;margin-bottom:20px;">
            <strong>Role</strong><br>
            <select name="role_id" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px">
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit" class="btn"
            style="width:100%;padding:10px;background:#2a7ae2;border:none;color:white;border-radius:6px;font-weight:bold;cursor:pointer;">
            Register
        </button>

        <p style="text-align:center;margin-top:15px;">
            Already have an account? <a href="login.php">Login here</a>.
        </p>
    </form>
</div>

<?php include 'footer.php'; ?>