<?php
require_once 'config.php';
require_login();

// --- Create invoice ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_invoice'])) {
    $patient_id = (int)$_POST['patient_id'];
    $appointment_id = !empty($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : null;
    $amount = (float)$_POST['amount'];

    $stmt = $pdo->prepare("INSERT INTO invoices (patient_id, appointment_id, amount) VALUES (?, ?, ?)");
    $stmt->execute([$patient_id, $appointment_id, $amount]);
    header('Location: invoices.php');
    exit;
}

// --- Mark invoice as paid ---
if (isset($_GET['pay'])) {
    $id = (int)$_GET['pay'];
    $pdo->prepare("UPDATE invoices SET status='Paid' WHERE id=?")->execute([$id]);
    header('Location: invoices.php');
    exit;
}

// --- Fetch lists ---
$invoices = $pdo->query("
    SELECT inv.*, p.first_name, p.last_name, a.appointment_datetime
    FROM invoices inv
    JOIN patients p ON inv.patient_id = p.id
    LEFT JOIN appointments a ON inv.appointment_id = a.id
    ORDER BY inv.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$patients = $pdo->query("SELECT id, first_name, last_name FROM patients ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$appointments = $pdo->query("SELECT id, appointment_datetime FROM appointments WHERE status='Scheduled' ORDER BY appointment_datetime ASC")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<h2>Invoices</h2>

<div class="card" style="max-width:700px;margin:20px auto;padding:20px;">
    <h3>Create New Invoice</h3>
    <form method="post">
        <label>Patient<br>
            <select name="patient_id" required style="width:100%;">
                <option value="" disabled selected>Select patient</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Appointment (optional)<br>
            <select name="appointment_id" style="width:100%;">
                <option value="">-- None --</option>
                <?php foreach ($appointments as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= date('M d, Y h:i A', strtotime($a['appointment_datetime'])) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Amount (₱)<br>
            <input type="number" step="0.01" name="amount" required style="width:100%;" placeholder="Enter total amount">
        </label><br><br>

        <button class="btn" name="create_invoice" style="width:100%;">Create Invoice</button>
    </form>
</div>

<h3 style="margin-top:40px;">All Invoices</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Appointment</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$invoices): ?>
            <tr>
                <td colspan="7" style="text-align:center;color:#777;">No invoices available.</td>
            </tr>
        <?php else: ?>
            <?php
            $total_unpaid = 0;
            foreach ($invoices as $inv):
                if ($inv['status'] != 'Paid') $total_unpaid += $inv['amount'];
            ?>
                <tr>
                    <td><?= $inv['id'] ?></td>
                    <td><?= htmlspecialchars($inv['first_name'] . ' ' . $inv['last_name']) ?></td>
                    <td>
                        <?= $inv['appointment_datetime']
                            ? date('M d, Y h:i A', strtotime($inv['appointment_datetime']))
                            : '<em>—</em>' ?>
                    </td>
                    <td>₱<?= number_format($inv['amount'], 2) ?></td>
                    <td>
                        <?php if ($inv['status'] === 'Paid'): ?>
                            <span style="color:green;font-weight:bold;">Paid</span>
                        <?php else: ?>
                            <span style="color:#c00;font-weight:bold;">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M d, Y', strtotime($inv['created_at'])) ?></td>
                    <td>
                        <?php if ($inv['status'] != 'Paid'): ?>
                            <a class="btn" href="invoices.php?pay=<?= $inv['id'] ?>" onclick="return confirm('Mark this invoice as paid?')">Mark Paid</a>
                        <?php else: ?>
                            <span style="color:#666;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($total_unpaid)): ?>
    <div style="margin-top:15px;text-align:right;color:#c00;">
        <strong>Total Outstanding Balance: ₱<?= number_format($total_unpaid, 2) ?></strong>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>