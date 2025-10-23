<?php
require_once 'config.php';
require_login();

include 'header.php';

// Quick stats
$patientCount = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$doctorCount = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$appointmentCount = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();

$upcoming = $pdo->prepare("
  SELECT a.*, p.first_name, p.last_name, d.first_name AS dfn, d.last_name AS dln 
  FROM appointments a 
  JOIN patients p ON a.patient_id = p.id 
  JOIN doctors d ON a.doctor_id = d.id 
  WHERE a.appointment_datetime >= NOW() 
  ORDER BY a.appointment_datetime ASC 
  LIMIT 5
");
$upcoming->execute();
$upcomingList = $upcoming->fetchAll(PDO::FETCH_ASSOC);

$user = $_SESSION['user'];
?>

<div style="max-width:1100px;margin:30px auto;">
    <h2 style="color:#2a7ae2;">🏥 Dashboard</h2>
    <p class="small" style="color:#555;">
        Welcome back, <strong><?= htmlspecialchars($user['full_name']) ?></strong>
        (<?= htmlspecialchars($user['role']) ?>)
    </p>

    <!-- Statistics Section -->
    <div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:25px;">
        <div style="flex:1;min-width:200px;background:linear-gradient(135deg,#4dabf7,#2a7ae2);color:white;padding:20px;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,0.1);text-align:center;">
            <h3 style="margin:0;font-size:16px;">Patients</h3>
            <div style="font-size:32px;font-weight:bold;margin-top:10px;"><?= $patientCount ?></div>
        </div>

        <div style="flex:1;min-width:200px;background:linear-gradient(135deg,#69db7c,#38b000);color:white;padding:20px;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,0.1);text-align:center;">
            <h3 style="margin:0;font-size:16px;">Doctors</h3>
            <div style="font-size:32px;font-weight:bold;margin-top:10px;"><?= $doctorCount ?></div>
        </div>

        <div style="flex:1;min-width:200px;background:linear-gradient(135deg,#ffd43b,#f59f00);color:white;padding:20px;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,0.1);text-align:center;">
            <h3 style="margin:0;font-size:16px;">Appointments</h3>
            <div style="font-size:32px;font-weight:bold;margin-top:10px;"><?= $appointmentCount ?></div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div style="margin-top:40px;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);">
        <h3 style="color:#2a7ae2;margin-bottom:10px;">📅 Upcoming Appointments</h3>
        <?php if ($upcomingList): ?>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f0f4ff;">
                        <th style="text-align:left;padding:10px;">Date & Time</th>
                        <th style="text-align:left;padding:10px;">Patient</th>
                        <th style="text-align:left;padding:10px;">Doctor</th>
                        <th style="text-align:left;padding:10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingList as $u): ?>
                        <tr style="border-top:1px solid #eee;">
                            <td style="padding:10px;"><?= htmlspecialchars($u['appointment_datetime']) ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                            <td style="padding:10px;">Dr. <?= htmlspecialchars($u['dfn'] . ' ' . $u['dln']) ?></td>
                            <td style="padding:10px;">
                                <span style="background:
                  <?= $u['status'] == 'Scheduled' ? '#e7f5ff' : ($u['status'] == 'Completed' ? '#d3f9d8' : '#ffe3e3') ?>;
                  color:
                  <?= $u['status'] == 'Scheduled' ? '#1c7ed6' : ($u['status'] == 'Completed' ? '#2b8a3e' : '#c92a2a') ?>;
                  padding:4px 8px;
                  border-radius:5px;
                  font-size:0.9em;">
                                    <?= htmlspecialchars($u['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#777;">No upcoming appointments scheduled.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>