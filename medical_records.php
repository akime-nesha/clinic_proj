<?php
require_once 'config.php';
require_login();

$patient_id = (int)($_GET['patient_id'] ?? 0);

// Fetch lists
$patients = $pdo->query("SELECT id, first_name, last_name FROM patients ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$doctors = $pdo->query("SELECT id, first_name, last_name, specialty FROM doctors ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$medicines = $pdo->query("SELECT id, name FROM medicines ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Add medical record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $pid = (int)$_POST['patient_id'];
    $did = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : null;
    $visit_date = $_POST['visit_date'] ?: date('Y-m-d');
    $notes = trim($_POST['notes']);
    $diagnosis = trim($_POST['diagnosis']);

    $stmt = $pdo->prepare("
        INSERT INTO medical_records (patient_id, doctor_id, visit_date, notes, diagnosis)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$pid, $did, $visit_date, $notes, $diagnosis]);
    $record_id = $pdo->lastInsertId();

    // Optional prescription
    if (!empty($_POST['medicine_id'])) {
        $stmt2 = $pdo->prepare("
            INSERT INTO prescriptions (medical_record_id, medicine_id, dosage, instructions)
            VALUES (?, ?, ?, ?)
        ");
        $stmt2->execute([
            $record_id,
            $_POST['medicine_id'],
            $_POST['dosage'],
            $_POST['instructions']
        ]);
    }

    header('Location: medical_records.php?patient_id=' . $pid);
    exit;
}

// Filter
$where = "";
$params = [];
if ($patient_id) {
    $where = "WHERE mr.patient_id = ?";
    $params[] = $patient_id;
}

// Fetch records
$stmt = $pdo->prepare("
    SELECT mr.*, 
           p.first_name AS pf, p.last_name AS pl,
           d.first_name AS df, d.last_name AS dl
    FROM medical_records mr
    JOIN patients p ON mr.patient_id = p.id
    LEFT JOIN doctors d ON mr.doctor_id = d.id
    $where
    ORDER BY mr.visit_date DESC
");
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<h2>Medical Records</h2>

<div class="card" style="padding:20px; max-width:700px; margin:20px auto;">
    <h3>Add New Record</h3>
    <form method="post">
        <label>Patient<br>
            <select name="patient_id" required>
                <option value="" disabled selected>Select patient</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $patient_id == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Doctor<br>
            <select name="doctor_id">
                <option value="">-- Select doctor --</option>
                <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?> — <?= htmlspecialchars($d['specialty'] ?? 'General') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Visit Date<br>
            <input type="date" name="visit_date" value="<?= date('Y-m-d') ?>">
        </label><br><br>

        <label>Diagnosis<br>
            <input type="text" name="diagnosis" placeholder="e.g., Flu, Hypertension" style="width:100%;">
        </label><br><br>

        <label>Notes<br>
            <textarea name="notes" placeholder="Enter consultation notes..." style="width:100%;height:80px;"></textarea>
        </label><br><br>

        <fieldset style="border:1px solid #ccc; padding:15px; border-radius:8px;">
            <legend><strong>Optional Prescription</strong></legend>
            <label>Medicine<br>
                <select name="medicine_id">
                    <option value="">-- None --</option>
                    <?php foreach ($medicines as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label><br><br>
            <label>Dosage<br><input type="text" name="dosage" placeholder="e.g., 1 tablet twice a day"></label><br><br>
            <label>Instructions<br><textarea name="instructions" placeholder="e.g., After meals"></textarea></label>
        </fieldset><br>

        <button class="btn" name="add_record" style="width:100%;">Add Record</button>
    </form>
</div>

<h3 style="margin-top:40px;">Patient Records</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Visit Date</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Diagnosis</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$records): ?>
            <tr>
                <td colspan="6" style="text-align:center;color:#777;">No records found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= date('M d, Y', strtotime($r['visit_date'])) ?></td>
                    <td><?= htmlspecialchars($r['pf'] . ' ' . $r['pl']) ?></td>
                    <td><?= htmlspecialchars($r['df'] . ' ' . $r['dl'] ?: '—') ?></td>
                    <td><strong><?= htmlspecialchars($r['diagnosis']) ?></strong></td>
                    <td style="max-width:250px;"><?= nl2br(htmlspecialchars($r['notes'])) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>