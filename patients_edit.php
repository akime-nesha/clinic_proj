<?php
// patients_edit.php
require_once 'config.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: patients.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<p style='color:red;text-align:center;'>Patient not found.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fn = trim($_POST['first_name']);
    $ln = trim($_POST['last_name']);
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Other';
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $update = $pdo->prepare("UPDATE patients 
        SET first_name=?, last_name=?, dob=?, gender=?, phone=?, email=?, address=? 
        WHERE id=?");
    $update->execute([$fn, $ln, $dob, $gender, $phone, $email, $address, $id]);

    header('Location: patients.php');
    exit;
}

include 'header.php';
?>

<div style="max-width:800px;margin:40px auto;">
    <h2 style="color:#2a7ae2;">📝 Edit Patient</h2>
    <p style="color:#555;">Modify the details of an existing patient and save changes.</p>

    <a href="patients.php"
        style="display:inline-block;margin-bottom:20px;color:#2a7ae2;text-decoration:none;font-weight:bold;">
        ← Back to Patients
    </a>

    <div style="background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
            <div>
                <label><strong>First Name</strong><br>
                    <input type="text" name="first_name"
                        value="<?= htmlspecialchars($data['first_name']) ?>" required
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Last Name</strong><br>
                    <input type="text" name="last_name"
                        value="<?= htmlspecialchars($data['last_name']) ?>" required
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Date of Birth</strong><br>
                    <input type="date" name="dob"
                        value="<?= htmlspecialchars($data['dob']) ?>"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Gender</strong><br>
                    <select name="gender" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                        <option <?= $data['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option <?= $data['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option <?= $data['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </label>
            </div>

            <div>
                <label><strong>Phone</strong><br>
                    <input type="text" name="phone"
                        value="<?= htmlspecialchars($data['phone']) ?>"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Email</strong><br>
                    <input type="email" name="email"
                        value="<?= htmlspecialchars($data['email']) ?>"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div style="grid-column:1/3;">
                <label><strong>Address</strong><br>
                    <textarea name="address" rows="2"
                        style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;"><?= htmlspecialchars($data['address']) ?></textarea>
                </label>
            </div>

            <div style="grid-column:1/3;text-align:right;">
                <button type="submit" class="btn"
                    style="background:#2a7ae2;color:white;padding:10px 20px;border:none;border-radius:6px;font-weight:bold;cursor:pointer;">
                    💾 Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>