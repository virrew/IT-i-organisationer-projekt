<?php 
session_start(); 
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) { // För att skicka ut användare som inte har ett konto
    header('Location: login.php');
    exit;
}
$patient = $_SESSION['patient'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)
$baseurl = 'http://193.93.250.83:8080/';

try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ', $e->getMessage(), "\n";
}

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se", "pwd":"HisLeo25!"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$response = json_decode($response, true);

$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

$bokningar = $baseurl . '/api/resource/Patient%20Appointment?fields=[%22*%22]&filters=[[%22patient_name%22,%22LIKE%22,%22%G6%%22]]';
$fields = [
    "name",
    "appointment_date",
    "appointment_time",
    "duration",
    "patient",
    "patient_name",
    "practitioner",
    "practitioner_name",
    "appointment_type",
    "notes",
    "status"
];

$filters = [
    ["patient", "LIKE", "%G6%"],
    ["patient", "LIKE", "%$patient%"]
];

$url = $baseurl . '/api/resource/Patient%20Appointment?' .
    'fields=' . urlencode(json_encode($fields)) .
    '&filters=' . urlencode(json_encode($filters));

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$response = json_decode($response, true);
$patients = $response['data'] ?? [];
curl_close($ch);
print_r($url);
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
        <?php
        if (empty($patients)) {
            echo "<p>Inga kommande bokningar.</p>";
            echo "<p>Gör en bokning här:</p>";
            echo "<a href='kontaktformulär.php' class='btn-primary'>Boka tid</a>"; 
        } else {
            echo "<ul>";
            foreach ($patients as $appointment) {
                echo "<li>";
                echo "Datum: " . htmlspecialchars($appointment['appointment_date']) . ", Tid: " . htmlspecialchars($appointment['appointment_time']) . ", Typ: " . htmlspecialchars($appointment['appointment_type']) . ", Status: " . htmlspecialchars($appointment['status']);
                echo "</li>";
            }
            echo "</ul>";
        }
        ?>
    </div>

<!-- http://193.93.250.83:8080/api/resource/Patient%20Appointment?fields=[%22*%22]&filters=[[%22patient%22,%20%22=%22,%20%22G5Torkeli%20Knipa%22]] -->
</body> 
</html>
