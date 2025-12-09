<?php 
session_start();
 if (!isset($_SESSION['patient_id'])) {
    // Om ingen är inloggad, skicka användaren till login
    header("Location: login.php");
    exit;
}

$patient = $_SESSION['patient_id']; // Inloggad patient
echo "PatientID i session: " . $patient;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)
$baseurl = 'http://193.93.250.83:8080/';

function erp_get($endpoint) {
    global $cookiepath, $tmeout, $baseurl;

    $ch = curl_init($baseurl . $endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        die("cURL error: $err");
    }

    $json = json_decode($response, true);
    return $json['data'] ?? [];
}

// HÄMTA VÅRDGIVARE //
$practitioner = erp_get('api/resource/Healthcare%20Practitioner?fields=["name","first_name","last_name","department"]&filters=[["first_name","LIKE","%G6%"]]');

// HÄMTA JOURNALER //
$fields = ['name','subject','communication_date','owner','status','reference_doctype','reference_name'];
$filters = [['patient', '=', $patient]];

$journaler = erp_get(
    'api/resource/Patient%20Medical%20Record?fields=' . urlencode(json_encode($fields)) .
    '&filters=' . urlencode(json_encode($filters))
);

// HÄMTAR JOURNALINFO FRÅN ENCOUNTERS I ERP //
$encounters = erp_get(
    'api/resource/Patient%20Encounter?fields=' . urlencode(json_encode([
        "patient",
        "patient_name",
        "notes",
        "custom_symtom",
        "custom_diagnos",
        "status",
        "encounter_date",
        "practitioner_name",
        "medical_department"
    ])) .
    '&filters=' . urlencode(json_encode([
        ["patient", "=", $_SESSION['patient_id']]
    ]))
);
echo "<pre>";
print_r($encounters);
echo "</pre>";

// LOGGA IN //

//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24hedan@student.his.se", "pwd":"9901hed0199And!"}'); 
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
//curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
//curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//$loginResponse = curl_exec($ch);
//$loginResponse = json_decode($login_response, true);

//$error_no = curl_errno($ch);
//$error = curl_error($ch);
//curl_close($ch);

//echo "<div style='background-color:lightgray; border:1px solid black'>";
//echo 'LOGIN RESPONSE:<br><pre>';
//print_r($login_response) . "</pre><br>";
//echo "</div>";

// Hämtar alla fält
//$fields = urlencode('["*"]');

// Filter som inte är en sträng
//$filters_array = [
// ["patient", "=", $patient]
//];
//$filters = urlencode(json_encode($filters_array));

// HÄMTAR JOURNALDATA //
//$ch = curl_init($baseurl . "api/resource/Patient%20Medical%20Record?fields=$fields&filters=$filters"); 
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
//curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
//curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//$journalResponse = curl_exec($ch);
//$journalResponse = json_decode($journalResponse, true);
//$error_no = curl_errno($ch);
//$error = curl_error($ch);
//curl_close($ch);

//här väljer jag att loopa över alla poster i [data] och för varje resultat så skriver jag ut name
//echo "<strong>LISTA:</strong><br>";
//foreach($journalResponse['data'] AS $key => $value){
//  echo $value["name"]."<br>";
//}

// Journaldata (Säker hantering)
//$journaler = [];
// Om data finns läggs den här
//if (isset($journalResponse['data']) && is_array($journalResponse['data'])) {
//    $journaler = $journalResponse['data'];
//}

// Visa journal i tabell
//echo "<h2>Journal för: $patient</h2>";
//echo "<strong>LISTA:</strong><br>";

//if (empty($journaler)) {
//    echo "Din journal är tom.";
//} else {
//    foreach ($journaler as $row) {
//        echo htmlspecialchars($row["name"]) . "<br>";
//    }
//}

// HÄMTAR VÅRDGIVARE //
//$ch = curl_init($baseurl . 'api/resource/Healthcare%20Practitioner?fields=["name","first_name","last_name","department"]&filters=[["first_name","LIKE","%G6%"]]');
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
//curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
//curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//$practResponse = curl_exec($ch);
//$practResponse = json_decode($practResponse, true);
//$practitioner = $practResponse['data'] ?? [];
//$error_no = curl_errno($ch);
//$error = curl_error($ch);
//curl_close($ch);

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

        .nav-brand a {
        color: var(--white);
        font-weight: bold;
        font-size: 1.2rem;
        }

        .nav-brand a:hover {
            text-decoration: underline;
        }

    h1, h2 {
        color: var(--primary-blue);
        margin-bottom: 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    }

    th {
        background: var(--primary-blue);
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 0.95rem;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid var(--primary-blue-light);
        font-size: 0.9rem;
        word-break: break-word;
        white-space: normal;
        overflow-wrap: break-word;
    }

    tr:nth-child(even) {
        background: var(--mint-green);
    }

    tr:hoover {
        background: var(--primary-blue-light);
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
            <?php if (!empty($encounter['notes'])): ?>
                <p><strong>Anteckning:</strong><br><?= nl2br(htmlspecialchars($encounter['notes'])) ?></p>
            <?php endif; ?>
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
<?php if (!empty($encounter['lab_test_prescription'])): ?>
<div class="card">
        <p><strong>Provnamn:</strong> <?=htmlspecialchars($encounter['lab_test_prescription']) ?></p>
        <p><strong>Datum:</strong> <?=htmlspecialchars($encounter['lab_test_date']) ?></p>
        <p><strong>Resultat</strong> <?=htmlspecialchars($encounter['lab_test_result']) ?></p>
        <p><strong>Referensintervall:</strong> <?=htmlspecialchars($encounter['lab_test_reference']) ?></p>
    </div>
    <?php endif; ?>
</div>

</div>
</body>
