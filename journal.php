<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

$patient_id   = $_SESSION['patient_id'] ?? '';
$patient_name = $_SESSION['patient_name'] ?? 'Patient';
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// cURL-konfiguration
$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600;
$baseurl = 'http://193.93.250.83:8080/';

// LOGGA IN I ERP 
$ch = curl_init($baseurl . 'api/method/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24hedan@student.his.se", "pwd":"9901hed0199And!"}'); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$loginResponse = curl_exec($ch);
$loginResponse = json_decode($loginResponse, true);
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

echo "<div style='background-color:lightgray; border:1px solid black'>";
echo 'LOGIN RESPONSE:<br><pre>';
print_r($loginResponse) . "</pre><br>";
echo "</div>";

// Hämta journalanteckningar (Patient Encounter)
$fields = ["patient","patient_name","notes","custom_symtom","custom_diagnos","status","encounter_date","practitioner_name","medical_department","lab_test_prescription"];
$filters = [["patient","=",$patient_id]];

// HÄMTAR JOURNALINFO FRÅN ENCOUNTERS I ERP //
$encounters = $baseurl .
    'api/resource/Patient%20Encounter?fields=['.urlencode('"patient","patient_name","custom_symtom","custom_diagnos","status","encounter_date","practitioner_name","medical_department"').']' .
    '&filters=' . urlencode('[["patient","=","' . $_SESSION['patient_id'] .'"]]');

echo $encounters;

$ch = curl_init($encounters); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$encountersresponse = curl_exec($ch);
$encountersresponse = json_decode($encountersresponse, true);
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

echo "<pre>";
print_r($encountersresponse);
echo "</pre>";


$labtests = $baseurl .
    'api/resource/Lab%20Test?fields=[' .
        urlencode('"name","patient","patient_name","status","result_date","docstatus"') .
    ']' .
    '&filters=' . urlencode('[["patient_name","like","g6%"]]');

// Hämta provsvar (Lab Test) 
$filters_lab = urlencode(json_encode([
    ["docstatus","=",1],
    ["patient_name","=",$patient_name]
]));

$url = $baseurl . "api/resource/Lab Test?" .
"filters=" . urlencode(json_encode($filters_lab)) .
"&limit_page_length=1000";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if (!empty($error_no)) {
    echo "<div style='background-color:red'>GET Lab Test cURL error ($error_no): $error</div>";
    $labtests = [];
} else {
    $labtests = json_decode($response, true)['data'] ?? [];
}

// Hämta labresultat
$lab_results = [];
foreach ($labtests as $test) {
    $url = $baseurl . "api/resource/Lab Test/" . $test["name"];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $full_response = curl_exec($ch);
    curl_close($ch);

    $full = json_decode($full_response, true)['data'] ?? null;
    if (!$full) continue;

    $lab_results[] = [
        "id" => $full["name"],
        "date" => $full["result_date"],
        "patient" => $full["patient_name"],
        "template" => $full["template"],
        "status" => $full["status"],
        "practitioner" => $full["practitioner_name"],
        "results" => $full["normal_test_items"] ?? [],
        "descriptive" => $full["descriptive_test_items"] ?? []
    ];
}

// 5. Visa resultat (exempel)
echo "\nLab Results:\n";
print_r($lab_results);

?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Min journal</title>
</head>
<style>
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
        --shadow-primary: rgba(31,111,120,0.25);
    }

    body {
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: var(--gray-light);
        margin: 0;
        padding: 0;
        color: var(--text-dark);
    }

    .container {
        max-width: 1100px;
        margin: 32px auto;
        background: var(--white);
        border-radius: 14px;
        padding: 36px;
        border: 2px solid var(--primary-blue);
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
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

    h1, h2 {
        color: var(--primary-blue);
        margin-bottom: 16px;
    }

    .card-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    }

    .card {
        background: var(--white);
        border: 1px solid var(--primary-blue-light);
        border-radius: 12px;
        padding: 20px;
        width: 250px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .card p {
        margin: 6px 0;
    }
    </style>
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

            <!-- Höger sida – användarnamn + logga ut -->
            <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logga ut</a>

        <?php else: ?>

            <a href="login.php">Logga in</a>

        <?php endif; ?>

        </div>
    </nav>

<div class="container">

<h2>Journalanteckningar</h2>

<?php if (!empty($encounters)): ?>
<div class="card-container">
    <?php foreach ($encounters as $encounter): ?>
        <div class="card">
            <p><strong>Datum:</strong> <?= htmlspecialchars($encounter['encounter_date']) ?></p>
            <p><strong>Vårdgivare:</strong> <?= htmlspecialchars($encounter['practitioner_name'] ?? 'Okänd') ?></p>
            <p><strong>Avdelning:</strong> <?= htmlspecialchars($encounter['medical_department'] ?? '') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($encounter['status'] ?? '') ?></p>
    <?php endforeach; ?>
</div>

<?php else: ?>
    <p>Ingen journaldata hittades för dig.</p>
<?php endif; ?>

<div class="card">
        <p><strong>Diagnoser:</strong></p>

        <?php if (!empty($encounter['custom_diagnos'])): ?>
            <ul>
            <?php foreach ($encounter['custom_diagnos'] as $diag): ?>
                <li>
                    <strong><?= htmlspecialchars($diag['custom_diagnos']) ?></strong>
                    – <?= htmlspecialchars($diag['description'] ?? '') ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Inga diagnoser registrerade.</p>
        <?php endif; ?>
    </div>

<h2>Provsvar</h2>
<?php if (!empty($lab_results)): ?>
<div class="card-container">
    <?php foreach ($lab_results as $lab): ?>
        <div class="card">
            <p><strong>Provnamn:</strong> <?=htmlspecialchars($lab["template"]) ?></p>
            <p><strong>Datum:</strong> <?=htmlspecialchars($lab["date"]) ?></p>
            <p><strong>Status:</strong> <?=htmlspecialchars($lab["status"]) ?></p>

            <?php if (!empty($lab["results"])): ?>
                <p><strong>Resultat:</strong><p>
                <ul>
                    <?php foreach ($lab["results"] as $r): ?>
                        <li>
                            <?= htmlspecialchars($r["lab_test_name"]) ?>:
                            <?= htmlspecialchars($r["result_value"]) ?>
                            (Ref: <?= htmlspecialchars($r["normal_range"]) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>    
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p> Inga provsvar hittades.</p>
<?php endif; ?>
</div>
</body>
