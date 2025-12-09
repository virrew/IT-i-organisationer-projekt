<?php
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)
$baseurl = 'http://193.93.250.83:8080/';





$contactData = $_SESSION['contact_data'] ?? null;


try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ', $e->getMessage(), "\n";
}

/* -----------------------------
   LOGIN TO ERPNext
------------------------------*/
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

/* -----------------------------
   FETCH HEALTHCARE PRACTITIONERS
------------------------------*/
$ch = curl_init($baseurl . 'api/resource/Healthcare%20Practitioner?fields=["name","first_name","last_name","department"]&filters=[["first_name","LIKE","%G6%"]]');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$response = json_decode($response, true);
$practitioners = $response['data'] ?? [];
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);
// $practitioners = json_decode($response, true)['data'] ?? [];

/* -----------------------------
   FETCH PATIENT INFO
------------------------------*/
$fields = urlencode('["name","patient_name","sex"]');
$filters = urlencode('[["patient_name","LIKE","%G6%"]]');

$patient_url = $baseurl . "api/resource/Patient?fields=$fields&filters=$filters";
$ch = curl_init($patient_url);
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

// $patients = json_decode($patient_response, true)['data'] ?? [];

if (!empty($patients)) {
    $_SESSION['patient_id']   = $patients[0]['name'] ?? '';
    $_SESSION['patient_name'] = $patients[0]['patient_name'] ?? '';
    $_SESSION['patient_sex']  = $patients[0]['sex'] ?? '';
} else {
    die("Kunde inte hitta patientdata.");
}
$session_user = $_SESSION['username'] ?? '';

/* -----------------------------
   PROCESS BOOKING FORM (POST)
------------------------------*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_type'])) {
// Fixa time-format om det saknar sekunder
$time = $_POST["appointment_time"];
if (strlen($time) == 5) { // HH:MM
    $time .= ":00";
}

// Hämta practitioner-id och name
$practitioner_id = $_POST["healthcare_practitioner"] ?? '';
$practitioner_name = $_POST["practitioner_name"] ?? '';
$department = $_POST["department"] ?? '';

// Patient-ID (från ERPNext, inte användarnamn)
    $patient_id   = $_SESSION["patient_id"];
    $patient_name = $_SESSION["patient_name"];
    $patient_sex  = $_SESSION["patient_sex"];

    // Bygg data-arrayen
    $data = [
    "appointment_type"        => $_POST["appointment_type"],
    "appointment_date"        => $_POST["appointment_date"],
    "appointment_time"        => $time,                  // fast time format
    "healthcare_practitioner" => $practitioner_id,       // FIX
    "practitioner"            => $practitioner_id,       // FIX
    "practitioner_name"       => $practitioner_name,
    "department"              => $department,
    "duration"                => intval($_POST["duration"]),
    "patient"                 => $_SESSION["patient_id"],
    "patient_name"            => $patient_name,
    "patient_sex"             => $patient_sex,
    "notes"                   => $_POST["notes"] ?? ""
];

    $json = json_encode($data, JSON_UNESCAPED_SLASHES);

    // POST till ERPNext
    $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment');
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
        die("<h3>Tekniskt fel vid bokning:</h3><pre>$error</pre>");
    }

if (!isset($result["data"])) {
    echo "<h3>ERPNext kunde inte skapa bokningen:</h3>";

    echo "<h4>JSON som skickas:</h4>";
    var_dump($json);

    echo "<h4>Svar från ERPNext:</h4>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    exit;
}


    $appointment_id = $result["data"]["name"];
    header("Location: index.php");
    exit;
}
      // alla fält som behövs:
      // appointment_type
      // appointment_date
      // appointment_time
      // healthcare_practitioner
      // practitioner_name
      // department
      // duration
      // patient
      // patient_name
      // patient_sex

      // Sedan klickas check availability och då behövs dessa fält fyllas i:

      // Medical department (department)
      // practitioner
      // appointment_date
      // appointment_time (från klockan 8-15 (kolla availability))
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Boka tid</title>
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

    * { box-sizing: border-box; }

    body {
      font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background-color: var(--gray-light);
      margin: 0;
      padding: 0;
      color: var(--text-dark);
    }

    .container {
      max-width: 900px;
      margin: 24px auto;
      background: var(--white);
      border-radius: 12px;
      padding: 28px;
      border: 2px solid var(--primary-blue);
      box-shadow: 0 6px 30px rgba(0,0,0,0.06);
    }

    h1 {
      margin: 0 0 12px;
      font-size: 1.5rem;
      color: var(--primary-blue);
    }

    p {
      margin: 0 0 20px;
      color: var(--text-dark);
      font-size: 1rem;
    } 

    form.booking {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      align-items: start;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      font-size: 0.92rem;
      color: var(--text-dark);
    }

    .field {
      display: flex;
      flex-direction: column;
      
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    input[type="number"],
    select,
    textarea {
      padding: 10px 12px;
      border: 1px solid var(--primary-blue-light);
      border-radius: 8px;
      background: white;
      outline: none;
      font-size: 0.95rem;
      transition: box-shadow .15s, border-color .15s;
    }

    input:focus, select:focus, textarea:focus {
      border-color: var(--primary-blue);
      box-shadow: 0 4px 14px var(--shadow-primary);
    }

    textarea { min-height: 110px; resize: vertical; padding-top: 10px; }
    .full { grid-column: 1 / -1; }
    .btn-row {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-top: 6px;
    }

    button.btn {
      background: var(--primary-blue);
      color: #fff;
      border: none;
      padding: 10px 14px;
      font-weight: 600;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 6px 18px rgba(31,111,120,0.25);
      transition: transform .06s ease, box-shadow .12s ease, opacity .12s;
    }

    button.btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(31,111,120,0.35);
    }

    .error {
      background: #fee2e2;
      color: #7f1d1d;
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 12px;
      border: 1px solid rgba(239,68,68,0.1);
    }

    .select-wrap { position: relative; }
    select option { padding: 8px; }
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

      <!-- alla fält som behövs:
      appointment_type
      appointment_date
      appointment_time
      healthcare_practitioner
      practitioner_name
      department
      duration
      patient
      patient_name
      patient_sex

      Sedan klickas check availability och då behövs dessa fält fyllas i:

      Medical department (department)
      practitioner
      appointment_date
      appointment_time (från klockan 8-15 (kolla availability)) -->

  <?php if ($contactData): ?>
  <div class="container" style="background: var(--mint-green); border: 2px solid var(--primary-blue); margin-bottom: 24px;">
      <h2 style="color: var(--primary-blue); margin-top:0;">Information du skickade in</h2>

      <p><strong>Beskrivning av besvären:</strong><br>
          <?php echo htmlspecialchars($contactData['field1']); ?></p>

      <p><strong>Hur länge du haft besvären:</strong><br>
          <?php echo htmlspecialchars($contactData['field2']); ?></p>

      <p><strong>Tidigare vårdkontakt:</strong><br>
          <?php echo htmlspecialchars($contactData['field3']); ?></p>
  </div>
  <?php endif; ?>

  <div class="container">
    <h1>Välj en tid</h1>
    <p class="lead">Inloggad som: <strong><?php echo htmlspecialchars($session_user); ?></strong></p>

<form class="booking" method="post" novalidate>

  <!-- Behövs för ERPNext -->
  <input type="hidden" name="patient" value="<?php echo htmlspecialchars($session_user); ?>">
  <input type="hidden" name="patient_name" value="<?php echo htmlspecialchars($session_user); ?>">
  <input type="hidden" name="patient_sex" value="<?php echo htmlspecialchars($_SESSION['patient_sex'] ?? ''); ?>">

  <!-- Appointment Type -->
  <div class="field">
    <label for="appointment_type">Typ av besök</label>
    <select id="appointment_type" name="appointment_type" required>
      <option value="G6Dietistbesök">Sjuksköterskebesök</option>
      <option value="G6Läkarbesök">Läkarbesök</option>
      <option value="G6Provtagning">Provtagning</option>
      <option value="G6Fysioterapi">Fysioterapi</option>
      <option value="G6Samtalsterapi">Samtalsterapi</option>
    </select>
  </div>

<!-- Todo: hämta endast G6 ("appointment_type": "G6Samtalsterapi") -->

  <!-- Healthcare Practitioner -->
  <div class="field">
    <label for="healthcare_practitioner">Välj sjuksköterska</label>
    <div class="select-wrap">
      <select id="healthcare_practitioner" name="healthcare_practitioner" required>
        <?php foreach ($practitioners as $p): ?>
        <?php
        $practitioner_id = $p['name'] ?? '';
        $first = $p['first_name'] ?? '';
        $last = $p['last_name'] ?? '';
        $full_name = trim("$first $last");
        $department = $p['department'] ?? 'Allmänt';
        ?>
        <option 
            value="<?php echo htmlspecialchars($practitioner_id); ?>"
            data-practitioner-name="<?php echo htmlspecialchars($full_name); ?>"
            data-department="<?php echo htmlspecialchars($department); ?>"
        >
            <?php echo htmlspecialchars($full_name); ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Practitioner Name (fylls automatiskt via JS) -->
  <input type="hidden" name="practitioner_name" id="practitioner_name">

  <!-- Department (fylls automatiskt via JS) -->
  <input type="hidden" name="department" id="department">

  <!-- Appointment Date -->
  <div class="field">
    <label for="appointment_date">Datum</label>
    <input id="appointment_date" type="date" name="appointment_date" required>
  </div>

  <!-- Appointment Time -->
  <div class="field">
    <label for="appointment_time">Tid</label>
    <input id="appointment_time" type="time" name="appointment_time" min="08:00" max="15:00" required>
  </div>

  <!-- Duration -->
  <div class="field">
    <label for="duration">Varaktighet (min)</label>
    <input id="duration" type="number" name="duration" min="1" value="30" required>
  </div>

  <!-- Notes -->
  <div class="field full">
    <label for="notes">Anteckningar</label>
    <textarea id="notes" name="notes" placeholder="Skriv eventuella kommentarer här..."></textarea>
  </div>

  <div class="field full">
    <div class="btn-row">
      <button class="btn" type="submit">Boka</button>
    </div>
  </div>

</form>

<script>
  // Fyll practitioner_name + department automatiskt när man väljer vårdgivare
  document.getElementById('healthcare_practitioner').addEventListener('change', function () {
      let selected = this.options[this.selectedIndex];
      document.getElementById('practitioner_name').value = selected.dataset.practitionerName;
      document.getElementById('department').value = selected.dataset.department;
  });
  
  // Kör direkt vid laddning
  document.getElementById('healthcare_practitioner').dispatchEvent(new Event('change'));
</script>

  </div>
</body>
</html>
