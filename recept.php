<?php
session_start();

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Kontrollera att användaren är inloggad
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}
// Hämta patientnamnet från sessionen
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
//  ----------  Här sätter ni era login-data ------------------ //
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
echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '$response<br><pre>';
echo print_r($response) . "</pre><br>";
echo "</div>";
$fields = ["practitioner_name", "patient_name", "medication_item", "status", "order_date", "quantity", "dosage_form", "dosage"];
$filters = [
    ["patient_name", "=", $patient_name]
];

// Definierar vilka fält som ska hämtas från Medication Request
$fields = ["practitioner_name", "patient_name", "medication_item", "status", "order_date", "quantity", "dosage_form", "dosage"];

// Filtrera baserat på inloggad patients namn
$filters = [
    ["patient_name", "LIKE", "%$patient_name%"]
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
echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '$response<br><pre>';
echo print_r($response) . "</pre><br>";
echo "</div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept</title>
</head>
<body>
    <style>
/* === Grundlayout === */
body {
  font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  background-color: #f6f8fb;
  margin: 0;
  padding: 0;
  color: #222;
}

/* === Sidhuvud === */
.header {
  background-color: #0077b6;
  color: white;
  padding: 40px 20px;
  text-align: center;
}

.header h1 {
  margin: 0;
  font-size: 2rem;
  font-weight: 600;
}

.header p {
  font-size: 1.1rem;
  margin-top: 8px;
  opacity: 0.9;
}

/* === Sektioner === */
.recept-list {
  margin: 40px auto;
  max-width: 900px;
  padding: 0 20px 30px;
}

.recept-list h2 {
  border-bottom: 3px solid #0077b6;
  padding-bottom: 8px;
  margin-bottom: 25px;
  color: #003049;
  font-size: 1.6rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* === Receptkort === */
.recept-card {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
  padding: 20px 25px;
  margin-bottom: 25px;
  transition: all 0.2s ease;
  border-left: 6px solid #0077b6;
}

.recept-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 14px rgba(0, 0, 0, 0.15);
}

/* === Header på kortet === */
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
  color: #023e8a;
  font-weight: 600;
}

.recept-status {
  background-color: #d8f3dc;
  color: #1b4332;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.9rem;
}

/* === Info och metadata === */
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
  color: #333;
}

.value {
  color: #555;
}

/* === Utgångna recept === */
.recept-card.expired {
  background-color: #fff0f0;
  border: 1px solid #ffcccc;
  opacity: 0.9;
  border-left: 6px solid #c1121f;
}

.recept-card.expired .recept-status {
  background-color: #ffe5e5;
  color: #c1121f;
}

/* === “Inga recept”-text === */
.recept-list p {
  background-color: #f1f1f1;
  padding: 12px;
  border-radius: 8px;
  color: #555;
  font-style: italic;
  text-align: center;
  margin-top: 15px;
}

/* === Små förbättringar === */
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
    </style>


    <section class="header">
    <h1>Mina recept</h1>
    <p>Översikt över dina aktiva och utgångna recept</p>
    </section>

    <!-- Sektion: Aktiva recept, genererar recept eftersom patienten får fler recept -->
    <section class="recept-list">
  <h2>Aktiva recept</h2>

  <?php
  // Dela upp recept i aktiva och utgångna
$aktiva = [];
$utgangna = [];

// För att veta om receptet är aktivt eller utgånget
if (!empty($response['data'])) {
    foreach ($response['data'] as $r) {
        $status = strtolower($r['status'] ?? '');

        if (strpos($status, 'ended') !== false) {
            $utgangna[] = $r;
        } else {
            $aktiva[] = $r;
        }
    }
}
?>
  <?php foreach ($aktiva as $r): ?>
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
          <span class="value">Utfärdare: <?= htmlspecialchars($r['practitioner_name'] ?? 'Okänd läkare') ?></span><br>
          <span class="utfardat">Utfärdat datum: <?= htmlspecialchars($r['order_date'] ?? 'Okänt datum') ?></span><br>
        </div>

        <div class="giltig-tom">
          <span class="label">Patient</span>
          <span class="value"><?= htmlspecialchars($r['patient_name'] ?? 'Okänd patient') ?></span><br>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
</section>
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
        </div>

        <div class="giltig-tom">
          <span class="label">Patient</span>
          <span class="value"><?= htmlspecialchars($r['patient_name'] ?? 'Okänd patient') ?></span><br>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
</section>
</body>
</html>