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
//$fields = ["patient","patient_name","notes","custom_symtom","custom_diagnos","status","encounter_date","practitioner_name","medical_department","lab_test_prescription"];
//$filters = [["patient","=",$patient_id]];

// HÄMTAR JOURNALINFO FRÅN ENCOUNTERS I ERP //
$encounters = $baseurl .
    'api/resource/Patient%20Encounter?fields=['.urlencode('"patient","patient_name","custom_symtom","custom_diagnos","encounter_date","practitioner_name","medical_department"').']' .
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

$encounters = $encountersresponse['data'] ?? [];

// Hämta provsvar (Lab Test)
$labtests = $baseurl .
    'api/resource/Lab%20Test?fields=[' .urlencode('"name","lab_test_name","date","result_date","practitioner_name","normal_test_items"').']' .
    '&filters=' . urlencode('[["docstatus","=","1"],["patient","=","' . $_SESSION['patient_id'] . '"]]') .'&limit_page_length=1000';

$ch = curl_init($labtests);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$labtestresponse = curl_exec($ch);
$labtestresponse = json_decode($labtestresponse, true);
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

$labtests = $labtestresponse['data'] ?? [];

echo "<pre>";
print_r($labtestresponse);
echo "</pre>";

// Hämta detaljer för varje labtest inklusive normal_test_items
foreach ($labtests as &$lab) {
    $lab_detail_url = $baseurl . 'api/resource/Lab%20Test/' . urlencode($lab['name']);
    $ch = curl_init($lab_detail_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $detail_response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // Lägg till normal_test_items från detaljvyn
    $lab['normal_test_items'] = $detail_response['data']['normal_test_items'] ?? [];
}
unset($lab); // bryt referens

echo "<pre>";
print_r($detail_response);
echo "</pre>";

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

     .welcome-text {
    background-color: var(--white);
    color: var(--primary-blue);
    padding: 20px 24px;
    border-radius: 8px;
    text-align: center;
    max-width: 800px;
    margin: 0 auto 24px auto;
    }

    .welcome-text .welcome-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    }

    .welcome-text .welcome-subtitle {
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
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

    /* === JOURNALANTECKNINGAR === */

    .card-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    box-sizing: border-box;
    }

    .card {
        width: 100%;
        background: var(--white);
        border: 1px solid var(--primary-blue);
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    .journal-header {
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--info-blue);
    margin-bottom: 12px;
    border-bottom: 1px solid #e5e5e5;
    padding-bottom: 6px;

    }

    .card p {
        margin: 6px 0;
        color: var(--text-dark);
    }

    /* === PROVSVAR === */

    .lab-entry {
        width: 100%;
        max-width: 100%;
        background: var(--white);
        border: 1px solid var(--primary-blue);
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        box-sizing: border-box;
        overflow-x: auto;
    }

    .lab-entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    color: var(--info-blue);
    margin-bottom: 12px;
    border-bottom: 1px solid #e5e5e5;
    padding-bottom: 6px;
    }

    .lab-entry p {
        margin: 6px 0;
        color: var(--text-dark);
    }

    .lab-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 0.95rem;
    table-layout: fixed;
    word-wrap: break-word;
    }

    .lab-table th {
        text-align: left;
        padding: 8px;
        background: var(--primary-blue-light);
        color: var(--info-blue);
    }

    .lab-table td {
        padding: 8px;
        border-bottom: 1px solid #e0e0e0;
    }

    /* === FOOTER === */

    .site-footer {
    text-align: center;
    padding: 20px;
    background-color: #1F6F78;
    color: white;
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
    <div class="welcome-text">
        <p class="welcome-title">Välkommen till din <strong>Journal</strong></p>
        <p class="welcome-subtitle">Här kan du ta del av dina journalanteckningar och provsvar på ett tryggt och enkelt sätt.</p>
    </div>

<h2>Journalanteckningar</h2>

<?php if (!empty($encounters)): ?>
<div class="card-container">
    <?php foreach ($encounters as $encounter): ?>
        <div class="card">
            <div class="journal-header">
                <?= htmlspecialchars($encounter['encounter_date'] ?? '') ?>
            </div>
            <p><strong>Symtom: </strong><?= htmlspecialchars($encounter['custom_symtom'] ?? 'Ej angivet') ?></p>
            <p><strong>Diagnos: </strong><?= htmlspecialchars($encounter['custom_diagnos'] ?? 'Ingen diagnos registrerad') ?></p>
            <p><strong>Vårdgivare:</strong> <?= htmlspecialchars($encounter['practitioner_name'] ?? 'Okänd') ?></p>
            <p><strong>Avdelning:</strong> <?= htmlspecialchars($encounter['medical_department'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p>Ingen journaldata hittades för dig.</p>
<?php endif; ?>


<h2>Provsvar</h2>
<?php if (!empty($labtests)): ?>
<div class="card-container">
    <?php foreach ($labtests as $lab): ?>
        <div class="lab-entry">
            <div class="lab-entry-header">
                <span><strong>Provnamn:</strong> <?=htmlspecialchars($lab["lab_test_name"]) ?></span>
                <span><strong>Datum:</strong> <?=htmlspecialchars($lab["date"]) ?></span>
            </div>

            <p><strong>Utfärdat av: </strong> <?=htmlspecialchars($lab["practitioner_name"]) ?></p>

            <?php if (!empty($lab["normal_test_items"])): ?>
                <table class="lab-table">
                    <thead>
                        <tr>
                            <th>Analys</th>
                            <th>Resultat</th>
                            <th>Referens</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lab["normal_test_items"] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item["lab_test_name"]) ?></td>
                                <td>
                                    <?= htmlspecialchars($item["result_value"]) ?>
                                    <?= htmlspecialchars($item["lab_test_uom"]) ?>
                                </td>
                                <td><?= htmlspecialchars($item["normal_range"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p> Inga provsvar hittades.</p>
<?php endif; ?>
</div>
</body>
<footer class="site-footer">
    <p>© 2025 Mölndals Vårdcentral</p>
</footer>
