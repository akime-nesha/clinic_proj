<?php
require_once 'config.php';
require_login();

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $fn = trim($_POST['first_name']);
    $ln = trim($_POST['last_name']);
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?? 'Other';
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("INSERT INTO patients (first_name,last_name,dob,gender,phone,email,address) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$fn, $ln, $dob, $gender, $phone, $email, $address]);

    header('Location: patients.php');
    exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: patients.php');
    exit;
}

include 'header.php';

$patients = $pdo->query("SELECT * FROM patients ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="max-width:1000px;margin:30px auto;">
    <h2 style="color:#2a7ae2;">👥 Patient Management</h2>
    <p class="small" style="color:#555;">Manage patient records, view demographics, and access medical histories.</p>

    <!-- Add Patient Form -->
    <div style="background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-top:20px;">
        <h3 style="color:#2a7ae2;">➕ Add New Patient</h3>
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:10px;">
            <div>
                <label><strong>First Name</strong><br>
                    <input type="text" name="first_name" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Last Name</strong><br>
                    <input type="text" name="last_name" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Date of Birth</strong><br>
                    <input type="date" name="dob" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Gender</strong><br>
                    <select name="gender" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </label>
            </div>

            <div>
                <label><strong>Phone</strong><br>
                    <input type="text" name="phone" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div>
                <label><strong>Email</strong><br>
                    <input type="email" name="email" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
            </div>

            <div style="grid-column:1/3;">
                <label><strong>Address</strong><br>
                    <textarea name="address" rows="2" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;"></textarea>
                </label>
            </div>

            <div style="grid-column:1/3;text-align:right;">
                <button class="btn" name="add_patient"
                    style="background:#2a7ae2;color:white;padding:10px 20px;border:none;border-radius:6px;font-weight:bold;cursor:pointer;">
                    ➕ Add Patient
                </button>
            </div>
        </form>
    </div>

    <!-- Patient List -->
    <div style="margin-top:40px;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
        <h3 style="color:#2a7ae2;margin-bottom:10px;">📋 All Patients</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f0f4ff;">
                    <th style="padding:10px;text-align:left;">ID</th>
                    <th style="padding:10px;text-align:left;">Name</th>
                    <th style="padding:10px;text-align:left;">DOB</th>
                    <th style="padding:10px;text-align:left;">Phone</th>
                    <th style="padding:10px;text-align:left;">Email</th>
                    <th style="padding:10px;text-align:left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($patients): ?>
                    <?php foreach ($patients as $p): ?>
                        <tr style="border-top:1px solid #eee;">
                            <td style="padding:10px;"><?= $p['id'] ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                            <td style="padding:10px;"><?= $p['dob'] ?: '-' ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($p['phone']) ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($p['email']) ?></td>
                            <td style="padding:10px;">
                                <a class="btn" href="medical_records.php?patient_id=<?= $p['id'] ?>">Records</a>
                                <a class="btn" href="appointments.php?patient_id=<?= $p['id'] ?>">Appointments</a>
                                <a class="btn" href="patients_edit.php?id=<?= $p['id'] ?>">Edit</a>
                                <a class="btn" href="patients.php?action=delete&id=<?= $p['id'] ?>"
                                    onclick="return confirm('Delete this patient?')"
                                    style="background:#e03131;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:15px;color:#777;">No patients found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>