<?php
require_once 'config.php';
require_login();

// Add doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $fn = trim($_POST['first_name']);
    $ln = trim($_POST['last_name']);
    $spec = trim($_POST['specialty']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("INSERT INTO doctors (first_name, last_name, specialty, phone, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$fn, $ln, $spec, $phone, $email]);

    header('Location: doctors.php');
    exit;
}

// Delete doctor
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM doctors WHERE id = ?")->execute([$id]);
    header('Location: doctors.php');
    exit;
}

$doctors = $pdo->query("SELECT * FROM doctors ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
include 'header.php';
?>

<div style="max-width:1000px;margin:30px auto;">
    <h2 style="color:#2a7ae2;">👨‍⚕️ Doctors Management</h2>
    <p style="color:#555;">Add, view, and manage clinic doctors efficiently.</p>

    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-top:20px;">
        <!-- Add Doctor Form -->
        <div style="flex:1;min-width:300px;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0;color:#2a7ae2;">➕ Add Doctor</h3>
            <form method="post" style="display:grid;gap:10px;">
                <label><strong>First Name</strong>
                    <input type="text" name="first_name" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
                <label><strong>Last Name</strong>
                    <input type="text" name="last_name" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
                <label><strong>Specialty</strong>
                    <input type="text" name="specialty" placeholder="e.g. Pediatrics, Cardiology" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
                <label><strong>Phone</strong>
                    <input type="text" name="phone" placeholder="09XX-XXXXXXX" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
                <label><strong>Email</strong>
                    <input type="email" name="email" placeholder="doctor@example.com" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                </label>
                <button class="btn" name="add_doctor" style="background:#2a7ae2;color:white;padding:10px;border:none;border-radius:6px;cursor:pointer;font-weight:bold;">Add Doctor</button>
            </form>
        </div>

        <!-- Doctor List -->
        <div style="flex:2;min-width:500px;">
            <h3 style="color:#2a7ae2;">📋 All Doctors</h3>
            <input type="text" id="search" placeholder="Search doctor..." style="width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;margin-bottom:10px;">

            <div style="overflow-x:auto;background:white;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                <table id="doctorTable" style="width:100%;border-collapse:collapse;">
                    <thead style="background:#f0f4ff;">
                        <tr>
                            <th style="padding:10px;text-align:left;">ID</th>
                            <th style="padding:10px;text-align:left;">Name</th>
                            <th style="padding:10px;text-align:left;">Specialty</th>
                            <th style="padding:10px;text-align:left;">Contact</th>
                            <th style="padding:10px;text-align:left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $d): ?>
                            <tr style="border-bottom:1px solid #eee;">
                                <td style="padding:10px;"><?= $d['id'] ?></td>
                                <td style="padding:10px;"><?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?></td>
                                <td style="padding:10px;"><?= htmlspecialchars($d['specialty']) ?></td>
                                <td style="padding:10px;">
                                    📞 <?= htmlspecialchars($d['phone']) ?><br>
                                    ✉️ <?= htmlspecialchars($d['email']) ?>
                                </td>
                                <td style="padding:10px;">
                                    <a class="btn" href="doctors.php?delete=<?= $d['id'] ?>"
                                        onclick="return confirm('Are you sure you want to delete this doctor?')"
                                        style="background:#e74c3c;color:white;padding:6px 10px;border-radius:6px;text-decoration:none;">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($doctors)): ?>
                            <tr>
                                <td colspan="5" style="padding:10px;text-align:center;color:#999;">No doctors found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple search filter
    document.getElementById('search').addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        const rows = document.querySelectorAll('#doctorTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });
</script>

<?php include 'footer.php'; ?>