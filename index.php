<?php 
session_start(); 

// Se till att användaren är inloggad
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Hämtas från login.php där vi sparade ERPNext-ID
$patient_id   = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'];

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
    /* ===== FÄRGER ===== */
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

        --card-shadow: 0 6px 20px rgba(0,0,0,0.08);
        --card-hover-shadow: 0 10px 26px rgba(0,0,0,0.12);
    }

    /* ===== PAGE ===== */
    body {
        margin: 0;
        padding: 0;
        background: var(--gray-light);
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: var(--text-dark);
    }

    /* ===== NAVBAR ===== */
    .navbar {
        background: var(--primary-blue);
        color: var(--white);
        padding: 14px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.22);
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .nav-brand a {
        color: var(--white);
        font-size: 1.4rem;
        font-weight: bold;
        text-decoration: none;
        transition: opacity .2s ease;
    }

    .nav-brand a:hover {
        opacity: 0.85;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .nav-links a {
        color: var(--white);
        text-decoration: none;
        font-weight: 500;
        transition: opacity .2s ease;
    }

    .nav-links a:hover {
        opacity: 0.75;
    }

    .nav-user {
        font-weight: bold;
        padding: 6px 12px;
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
    }

    /* ===== PAGE HEADER ===== */
    h1 {
        margin-top: 40px;
        text-align: center;
        color: var(--primary-blue);
        font-size: 2rem;
    }

    /* ===== DASHBOARD CARDS ===== */
    .page-container {
        max-width: 850px;
        margin: 30px auto;
        padding: 10px;
    }

    .card {
        background: var(--white);
        padding: 24px;
        border-radius: 14px;
        margin-bottom: 24px;
        box-shadow: var(--card-shadow);
        transition: box-shadow .15s ease, transform .15s ease;
    }

    .card h2 {
        margin-top: 0;
        color: var(--primary-blue);
    }

    .card p {
        line-height: 1.5;
    }

    /* ===== BOOKING CARDS ===== */
    .appointment-card {
        background: var(--white);
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 16px;
        box-shadow: var(--card-shadow);
        border-left: 6px solid var(--primary-blue);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .appointment-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-hover-shadow);
    }

    .appointment-row {
        margin: 6px 0;
        font-size: 1rem;
    }

    .appointment-label {
        font-weight: 600;
        color: var(--info-blue);
    }

    /* ===== MINI BUTTONS ===== */
    .mini-btn {
        display: inline-block;
        padding: 6px 12px;
        margin-right: 6px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: bold;
        text-decoration: none;
        transition: opacity .2s ease, transform .1s ease;
    }

    .mini-edit {
        background: var(--primary-blue);
        color: white;
    }

    .mini-delete {
        background: var(--warning-red);
        color: white;
    }
    </style>
</head>
<body>
<!-- Navigation -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php" style="color: white; text-decoration: none;">
                Mölndals Vårdcentral
            </a>
        </div>

        <div class="nav-links">

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>

            <a href="journal.php">Min journal</a>
            <a href="recept.php">Mina recept</a>
            <a href="kontaktformulär.php">Boka tid här</a>
            <a href="Kontakt.php">Kontakt</a>

            <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span>
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

    <div class="card">
    <h2>📅 Kommande bokningar</h2>

    <?php if (empty($appointments)): ?>
        <p>Du har inga bokade tider just nu.</p>
        <a href="kontaktformulär.php" class="mini-btn" style="background: var(--accent-orange); color:white;">
            ➕ Boka tid
        </a>

    <?php else: ?>
        <?php foreach ($appointments as $a): ?>
            <div class="appointment-card">

                <div class="appointment-row">
                    <span class="appointment-label">Datum:</span>
                    <?= htmlspecialchars($a['appointment_date']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">Tid:</span>
                    <?= htmlspecialchars($a['appointment_time']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">Typ:</span>
                    <?= htmlspecialchars($a['appointment_type']) ?>
                </div>

                <div class="appointment-row">
                    <span class="appointment-label">Vårdgivare:</span>
                    <?= htmlspecialchars($a['practitioner_name']) ?>
                </div>

                <div style="margin-top:12px;">
                    <a href="Avboka min tid.php?id=<?= urlencode($a['name']) ?>" class="mini-btn mini-delete">❌ Avboka</a>
                    <a href="omboka.php?id=<?= urlencode($a['name']) ?>" class="mini-btn mini-edit">🔁 Omboka</a>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body> 
</html>
