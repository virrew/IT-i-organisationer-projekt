<?php 
session_start(); 

// Se till att användaren är inloggad
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Hämtas från login.php där vi sparade ERPNext-ID
$patient_id   = $_SESSION['patient_id'];      // Ex: HLC-PAT-2025-00018
$patient_name = $_SESSION['patient_name'];    // Ex: G6Doris Dorisson

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600;
$baseurl = 'http://193.93.250.83:8080/';

/* -----------------------------
   1) Logga in i ERPNext
------------------------------*/
$ch = curl_init($baseurl . 'api/method/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se", "pwd":"HisLeo25!"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

/* -----------------------------
   2) Hämta bokningar för inloggad patient
------------------------------*/

$fields = [
    "name",
    "appointment_type",
    "appointment_date",
    "appointment_time",
    "status",
    "practitioner_name"
];

$filters = [
    ["patient", "=", $patient_id],
    ["status", "=", "Scheduled"]
];

$url = $baseurl . 'api/resource/Patient%20Appointment?' .
    'fields=' . urlencode(json_encode($fields)) . '&' .
    'filters=' . urlencode(json_encode($filters));

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$appointments = $data['data'] ?? [];   // Kommande bokningar
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mölndalsvårdcentral – Mina sidor</title>

    <style>
        /* Globala färgvariabler */
        :root {
            --primary-blue: #1F6F78;
            --primary-blue-light: #C2EBE8;

            --mint-green: #E7FFF3;
            --accent-orange: #FCA06A;
            --info-blue: #0A5360;
            --warning-red: #D9534F;

            --white: #FFFFFF;
            --gray-light: #F5F5F5;
            --text-dark: #0E2A2C;
        }

        .appointment-card {
            background: var(--white);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border-left: 6px solid var(--primary-blue);
        }

        .appointment-row {
            margin: 4px 0;
            font-size: 0.95rem;
        }

        .appointment-label {
            font-weight: bold;
            color: var(--primary-blue);
        }

        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
        }

        .status-Upcoming { background: var(--primary-blue); }
        .status-Completed { background: green; }
        .status-No\ Show { background: var(--warning-red); }
        .status-Cancelled { background: gray; }


        body {
            margin: 0;
            padding: 0;
            background: var(--gray-light);
            font-family: Arial, sans-serif;
            color: var(--text-dark);
        }

        /* NAVBAR */
        .navbar {
            background: var(--primary-blue);
            color: var(--white);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .nav-brand {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .nav-user {
            font-size: 0.95rem;
        }

        /* PAGE LAYOUT */
        h1 {
            text-align: center;
            margin-top: 40px;
            color: var(--primary-blue);
        }

        .page-container {
            max-width: 800px;
            margin: 20px auto 0;
            padding: 0 16px;
        }

        .welcome-card, .booking-card {
            margin-top: 20px;
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .welcome-card h2, .booking-card h2 {
            margin-top: 0;
            color: var(--text-dark);
        }

        .welcome-card p, .booking-card p {
            margin-bottom: 16px;
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 18px;
            background: var(--primary-blue);
            color: var(--white);
            border-radius: 999px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            transition: 0.1s ease;
        }

        .btn-primary:hover {
            background: var(--info-blue);
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.2);
        }

    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
    <div class="nav-brand">Mölndals Vårdcentral</div>
    <div class="nav-links">
      <a href="index.php">Hem</a>
      <a href="recept.php">Mina recept</a>
      <a href="bokningar.php">Mina bokningar</a>
      <a href="journal.php">Min journal</a>
      <a href="Kontakt.php">Kontakt</a>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
      <?= htmlspecialchars($_SESSION['username']) ?>
      <a href="logout.php">Logga ut</a>
      <?php else: ?>
      <a href="login.php">Logga in</a>
      <?php endif; ?>
    </div>
    </nav>

    <div class="page-container">
    <h1>Välkommen <?= htmlspecialchars($_SESSION['username']) ?></h1>

    <div class="welcome-card">
        <h2>Din översikt</h2>
        <p>
            Här kan du som patient se och hantera dina digitala tjänster hos
            Mölndals vårdcentral.
        </p>
    </div>

    <div class="booking-card">
    <h2>Kommande bokningar</h2>
    <?php if (empty($appointments)): ?>
        <p>Inga kommande bokningar.</p>
        <a href="kontaktformulär.php" class="btn-primary">Boka tid</a>

    <?php else: ?>
        <?php foreach ($appointments as $a): ?>
            <div class="appointment-card">

                <div class="appointment-row">
                    <span class="appointment-label">📅 Datum:</span>
                    <?= htmlspecialchars($a['appointment_date']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">⏰ Tid:</span>
                    <?= htmlspecialchars($a['appointment_time']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">🏥 Typ:</span>
                    <?= htmlspecialchars($a['appointment_type']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">👩‍⚕️ Vårdgivare:</span>
                    <?= htmlspecialchars($a['practitioner_name']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">📌 Status:</span>
                    <span class="status-pill status-<?= str_replace(' ', '', htmlspecialchars($a['status'])) ?>">
                        <?= htmlspecialchars($a['status']) ?>
                    </span>
                </div>

                <div class="appointment-row" style="margin-top:10px;">
    <a href="Avboka.php?id=<?= urlencode($a['name']) ?>" 
       style=" display:inline-block; 
           padding:4px 10px;
           background:#D9534F;
           color:white;
           border-radius:4px;
           font-size:0.8rem;
           text-decoration:none;
           font-weight:bold;">
        ❌ Avboka min tid
    </a>
</div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    </div>
</body> 
</html>
