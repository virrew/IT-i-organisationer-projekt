<?php
session_start();

// Kontrollera att användaren är inloggad
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Hämta patientnamnet från sessionen (från index.php)
$patient_id   = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)
// här sätter ni er domän
$baseurl = 'http://193.93.250.83:8080/'; 

try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ',  $e->getMessage(), "\n";
}

curl_setopt($ch, CURLOPT_POST, true);
//  ----------  Logga in till ERPNext ------------------ //
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24vicwa@student.his.se", "pwd":"Rolleman1!"}'); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$response = json_decode($response, true);

$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if (!empty($error_no)) {
  echo "<div style='background-color:red'>";
  echo '$error_no<br>';
  var_dump($error_no);
  echo "<hr>";
  echo '$error<br>';
  var_dump($error);
  echo "<hr>";
  echo "</div>";
}

 // För att begära nytt recept, posta till ERP:t
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_medication'])) {

      $requested_med = $_POST['renew_medication'] ?? '';

    $data = [
        "doctype"           => "Medication Request",
        "naming_series"     => "HMR-",
        "company"           => $_POST['company'],
        "title"             => "{$_POST['patient']} - {$_POST['renew_medication']} - Tablet",
        "medication_item"   => $_POST['renew_medication'],
        "order_date"        => date('Y-m-d'),
        "order_time"        => date('H:i:s'),
        "patient"           => $_POST['patient'],
        "patient_name"      => $_POST['patient'],
        "practitioner"      => $_POST['practitioner'],
        "practitioner_name" => $_SESSION['username'],
        "quantity"          => 1,
        "dosage_form"       => "Tablet",
        "dosage"            => "Once Daily",
        "status"            => "active-Medication Request Status",
        "period"            => "3 Week"
    ];

    $json = json_encode($data, JSON_UNESCAPED_SLASHES);

     $ch = curl_init($baseurl . 'api/resource/Medication%20Request');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error_no = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($error_no) {
        die("<h3>Tekniskt fel vid receptförfrågan:</h3><pre>$error</pre>");
    }

    if (!isset($result['data'])) {
        echo "<h3>ERPNext kunde inte skapa receptet:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        exit;
    }
    // Bekräftelse
    echo "<div style='background: #E7FFF3; border:2px solid #1F6F78; padding:10px; margin:10px 0;'>";
    echo "<h3>Receptförfrågan skickad!</h3>";
    echo "<p>Du har begärt ett nytt recept på <strong>" . htmlspecialchars($requested_med) . "</strong>.</p>";
    echo "</div>";
}

// Definierar vilka fält som ska hämtas från Medication Request
$fields = [
  "practitioner_name",
  "practitioner",
  "patient_name",
  "patient",
  "company",
  "medication_item",
  "status",
  "order_date",
  "quantity",
  "dosage_form",
  "dosage",
  "period"
];

// Filtrera baserat på inloggad patients namn
$filters = [
    ["patient", "=", $patient_id]
];

// Enklare sätt att bygga URL:en
$url = $baseurl . 'api/resource/Medication%20Request?' .
    'fields=' . urlencode(json_encode($fields)) .
    '&filters=' . urlencode(json_encode($filters));

$ch = curl_init($url);


// man kan även specificera vilka fält man vill se
// urlencode krävs när du har specialtecken eller mellanslag  
// $ch = curl_init($baseurl . 'api/resource/User?fields='. urlencode('["name", "first_name", "last_login"]'));
// det funkerar lika bra att ta bort mellanslaget i denna fråga
// $ch = curl_init($baseurl . 'api/resource/User?fields=["name","first_name","last_login"]');

//jag kör en get request, ibland vill man kanske köra en annan typ av request, och ibland så beöver man ha med postfields
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


$response = curl_exec($ch);
//echo $response;
$response = json_decode($response, true);

$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if (!empty($error_no)) {
  echo "<div style='background-color:red'>";
  echo '$error_no<br>';
  var_dump($error_no);
  echo "<hr>";
  echo '$error<br>';
  var_dump($error);
  echo "<hr>";
  echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept</title>
<style>
    /*  Globala färger  */
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

/*  Grundlayout  */
    body {
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: var(--gray-light);
        margin: 0;
        padding: 0;
        color: var(--text-dark);
}

/*  Sidhuvud  */
    .header {
        background-color: var(--primary-blue);
        color: var(--white);
        padding: 40px 20px;
        text-align: center;
        margin-top: 0;       /* standard */
}

    .header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
}

    .header p {
        font-size: 1.1rem;
        margin-top: 8px;
        opacity: 0.95;
}

/*  Sektioner  */
    .recept-list {
        margin: 40px auto;
        max-width: 900px;
        padding: 0 20px 30px;
}

    .recept-list h2 {
        border-bottom: 3px solid var(--primary-blue);
        padding-bottom: 8px;
        margin-bottom: 25px;
        color: var(--primary-blue);
        font-size: 1.6rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
}

/*  Receptkort  */
    .recept-card {
        background-color: var(--white);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 20px 25px;
        margin-bottom: 25px;
        transition: all 0.2s ease;
        border-left: 6px solid var(--primary-blue);
}

    .recept-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

/*  Header på kortet  */
    .recept-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        margin-bottom: 15px;
}

    .recept-namn {
        font-size: 1.2rem;
        margin: 0;
        color: var(--primary-blue);
        font-weight: 600;
}

    .recept-status {
        background-color: var(--mint-green);
        color: var(--info-blue);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.9rem;
}

/*  Info och metadata  */
    .recept-info,
    .recept-meta {
        margin-top: 10px;
        line-height: 1.6;
}

    .recept-info span,
    .recept-meta span {
        display: inline-block;
        margin-right: 5px;
}

    .label {
        font-weight: bold;
        color: var(--text-dark);
}

    .value {
        color: var(--text-dark);
}
/*  Utgångna recept  */
    .recept-card.expired {
        background-color: #fff5f5;
        border-left-color: var(--warning-red);
}

    .recept-card.expired .recept-status {
        background-color: #ffe4e4;
        color: var(--warning-red);
}
/*  “Inga recept”-text  */
    .recept-list p {
        background-color: var(--white);
        padding: 12px;
        border-radius: 8px;
        color: var(--text-dark);
        font-style: italic;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-top: 15px;
}
/*  Ikoner före rubrik  */
    h2 {
        display: flex;
        align-items: center;
        gap: 10px;
}

    h2::before {
        content: "💊";
        font-size: 1.3rem;
}

    .recept-list:last-of-type h2::before {
        content: "⚠️";
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

            <!-- Höger sida – användarnamn + logga ut -->
            <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logga ut</a>

        <?php else: ?>

            <a href="login.php">Logga in</a>

        <?php endif; ?>

        </div>
    </nav>

    <!-- Sektion: Aktiva recept, genererar recept eftersom patienten får fler recept -->
    <section class="recept-list">
  <h2>Aktiva recept</h2>
  <?php
  // Dela upp recept i aktiva och utgångna
$aktiva = [];
$utgangna = [];

$today = new DateTime();

if (!empty($response['data'])) {
    foreach ($response['data'] as $r) {
        // Kontrollera att order_date och period finns
        if (empty($r['order_date']) || empty($r['period'])) {
            continue;
        }

        // Beräkna slutdatum baserat på period 
        $end_date = new DateTime($r['order_date']);
        $end_date->modify('+' . $r['period']);

        // Dagar kvar
        $days_left = (int)$today->diff($end_date)->format('%r%a');

        $r['end_date'] = $end_date->format('Y-m-d');
        $r['days_left'] = $days_left;

        // Om receptet har gått ut, flytta till utgångna
        if ($days_left < 0) {
            $utgangna[] = $r;
        } else {
            $aktiva[] = $r;
        }
    }
}

?>
<!-- Sektion: Aktiva recept -->
  <?php foreach ($aktiva as $r): ?>
    <article class="recept-card">
      <div class="recept-card-header">
        <h3 class="recept-namn">Läkemedel: <?= htmlspecialchars($r['medication_item'] ?? 'Okänt läkemedel') ?></h3>
        <span class="recept-status">Status: <?= htmlspecialchars($r['status'] ?? 'Okänd status') ?></span><br>
        <span class="recept-antal">Antal: <?= htmlspecialchars($r['quantity'] ?? 'Okänt antal') ?></span><br>
      </div>

      <div class="recept-info">
        <!-- Information om dosering här -->
        <span class="recept-antal">Dosering: <?= htmlspecialchars($r['dosage_form'] ?? 'Okänd dosering') ?></span><br>
        <span class="recept-antal">När du ska ta: <?= htmlspecialchars($r['dosage'] ?? 'Okänd dosering') ?></span><br>
      </div>

      <div class="recept-meta">
        <div class="forskrivare">
          <span class="label">Förskrivare</span>
          <span class="value"><?= htmlspecialchars($r['practitioner_name'] ?? 'Okänd läkare') ?></span><br>
          <span class="utfardat">Utfärdat datum: <?= htmlspecialchars($r['order_date'] ?? 'Okänt datum') ?></span><br>
          <span class="utfardat">
          <?= ($r['days_left'] > 0)
            ? "Återstår {$r['days_left']} dagar"
            : "Utgått för " . abs($r['days_left']) . " dagar sedan"; ?>
        </span><br>
      </div>

      <div class="giltig-tom">
        <span class="label">Patient</span>
        <span class="value"><?= htmlspecialchars($r['patient_name'] ?? 'Okänd patient') ?></span><br>
      </div>
    </div>

    <!-- Visa knapp om mindre än 14 dagar kvar -->
    <?php if ($r['days_left'] <= 14): ?>
      <form method="POST" action="recept.php" class="renew-form">
        <input type="hidden" name="renew_medication" value="<?= htmlspecialchars($r['medication_item'] ?? '') ?>">
        <input type="hidden" name="patient" value="<?= htmlspecialchars($r['patient'] ?? '') ?>">
        <input type="hidden" name="practitioner" value="<?= htmlspecialchars($r['practitioner'] ?? '') ?>">
        <input type="hidden" name="company" value="<?= htmlspecialchars($r['company'] ?? '') ?>">
        <button type="submit" class="btn-renew">Begär nytt recept</button>
      </form>
      <p style="color:#D9534F; font-style:italic;">Detta recept går ut snart!</p>
    <?php endif; ?>
    </article>
  <?php endforeach; ?>

    <!-- Sektion: Utgångna recept -->
    <h2>Utgångna recept</h2>
    <?php foreach ($utgangna as $r): ?>
    <article class="recept-card">
      <div class="recept-card-header">
        <h3 class="recept-namn">Läkemedel: <?= htmlspecialchars($r['medication_item'] ?? 'Okänt läkemedel') ?></h3>
        <span class="recept-status">Status: <?= htmlspecialchars($r['status'] ?? 'Okänd status') ?></span><br>
        <span class="recept-antal">Antal: <?= htmlspecialchars($r['quantity'] ?? 'Okänt antal') ?></span><br>        
      </div>

      <div class="recept-info">
        <!-- Lägg till mer information om dosering här, typ vilken stryka på preparatet -->
        <span class="recept-antal">Dosering: <?= htmlspecialchars($r['dosage_form'] ?? 'Okänd dosering') ?></span><br>
        <span class="recept-antal">När du ska ta: <?= htmlspecialchars($r['dosage'] ?? 'Okänd dosering') ?></span><br>
      </div>

      <div class="recept-meta">
        <div class="forskrivare">
          <span class="label">Förskrivare</span>
          <span class="value"><?= htmlspecialchars($r['practitioner_name'] ?? 'Okänd läkare') ?></span><br>
          <span class="utfardat">Utfärdat datum: <?= htmlspecialchars($r['order_date'] ?? 'Okänt datum') ?></span><br>
          <?php if (!empty($r['order_date']) && !empty($r['period'])): ?>
          <?php
                $end_date = new DateTime($r['order_date']);
                $end_date->modify('+' . $r['period']);
            ?>
          <span class="utfardat">
                Giltigt t.o.m: <?= $end_date->format('Y-m-d'); ?>
          </span><br>
          <?php endif; ?>
        </div>

        <div class="giltig-tom">
          <span class="label">Patient</span>
          <span class="value"><?= htmlspecialchars($r['patient_name'] ?? 'Okänd patient') ?></span><br>
        </div>
      </div>
      <!-- Knapp för nytt recept -->
    <form method="POST" action="recept.php" class="renew-form">
      <input type="hidden" name="renew_medication" value="<?= htmlspecialchars($r['medication_item'] ?? '') ?>">
      <input type="hidden" name="patient" value="<?= htmlspecialchars($r['patient'] ?? '') ?>">
      <input type="hidden" name="practitioner" value="<?= htmlspecialchars($r['practitioner'] ?? '') ?>">
      <input type="hidden" name="company" value="<?= htmlspecialchars($r['company'] ?? '') ?>">
        <button type="submit" class="btn-renew">Begär nytt recept</button>
    </form>
    </article>
  <?php endforeach; ?>
</section>

</body>
</html>