<?php
require_once 'config.php';
require_login();

// Handle new booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $patient_id = (int)$_POST['patient_id'];
    $doctor_id = (int)$_POST['doctor_id'];
    $dt = $_POST['appointment_datetime'];
    $reason = trim($_POST['reason']);

    $stmt = $pdo->prepare("
        INSERT INTO appointments (patient_id, doctor_id, appointment_datetime, reason, status)
        VALUES (?, ?, ?, ?, 'Scheduled')
    ");
    $stmt->execute([$patient_id, $doctor_id, $dt, $reason]);
    header('Location: appointments.php');
    exit;
}

// Cancel appointment
if (isset($_GET['cancel'])) {
    $id = (int)$_GET['cancel'];
    $pdo->prepare("UPDATE appointments SET status='Cancelled' WHERE id = ?")->execute([$id]);
    header('Location: appointments.php');
    exit;
}

// Fetch patients and doctors
$patients = $pdo->query("SELECT id, first_name, last_name FROM patients ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$doctors = $pdo->query("SELECT id, first_name, last_name, specialty FROM doctors ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch appointments
$where = "";
$params = [];
if (isset($_GET['patient_id'])) {
    $where = "WHERE a.patient_id = ?";
    $params[] = (int)$_GET['patient_id'];
}

$stmt = $pdo->prepare("
    SELECT a.*, 
           p.first_name AS pf, p.last_name AS pl,
           d.first_name AS df, d.last_name AS dl, d.specialty
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    $where
    ORDER BY a.appointment_datetime DESC
");
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<h2>Appointments</h2>

<div class="card" style="padding:20px; max-width:600px; margin:20px auto;">
    <h3>Book Appointment</h3>
    <form method="post">
        <label>Patient<br>
            <select name="patient_id" required>
                <option value="" disabled selected>Select patient</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Doctor<br>
            <select name="doctor_id" required>
                <option value="" disabled selected>Select doctor</option>
                <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?> — <?= htmlspecialchars($d['specialty'] ?? 'General') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Date & Time<br>
            <input type="datetime-local" name="appointment_datetime" required>
        </label><br><br>

        <label>Reason<br>
            <textarea name="reason" placeholder="Reason for visit (optional)" style="width:100%;height:70px;"></textarea>
        </label><br><br>

        <button class="btn" name="book" style="width:100%;">Book Appointment</button>
    </form>
</div>

<h3 style="margin-top:40px;">All Appointments</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date & Time</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$appointments): ?>
            <tr>
                <td colspan="6" style="text-align:center;color:#777;">No appointments found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= date('M d, Y h:i A', strtotime($a['appointment_datetime'])) ?></td>
                    <td><?= htmlspecialchars($a['pf'] . ' ' . $a['pl']) ?></td>
                    <td><?= htmlspecialchars($a['df'] . ' ' . $a['dl']) ?></td>
                    <td>
                        <?php if ($a['status'] === 'Scheduled'): ?>
                            <span style="color:green;font-weight:bold;">Scheduled</span>
                        <?php elseif ($a['status'] === 'Cancelled'): ?>
                            <span style="color:red;font-weight:bold;">Cancelled</span>
                        <?php else: ?>
                            <span style="color:gray;">Other</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($a['status'] === 'Scheduled'): ?>
                            <a class="btn" href="appointments.php?cancel=<?= $a['id'] ?>" onclick="return confirm('Cancel appointment?')">Cancel</a>
                        <?php endif; ?>
                        <a class="btn" href="invoices.php?appointment_id=<?= $a['id'] ?>">Invoice</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>