<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kontrollera att patient är inloggad
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Sessionshantering
$patient_id   = $_SESSION['patient_id']   ?? '';
$patient_name = $_SESSION['patient_name'] ?? '';
$patient_sex  = $_SESSION['patient_sex']  ?? '';
$username     = $_SESSION['username']     ?? '';

if ($patient_id === '' || $patient_name === '') {
    die("Kunde inte ladda patientdata från sessionen. Logga in igen.");
}

// ERPnext-anslutning
$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600;
$baseurl = 'http://193.93.250.83:8080/';

$login = curl_init($baseurl . "api/method/login");
curl_setopt($login, CURLOPT_POST, true);
curl_setopt($login, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se","pwd":"HisLeo25!"}');
curl_setopt($login, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($login, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($login, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_setopt($login, CURLOPT_TIMEOUT, $tmeout);
$login_response = curl_exec($login);
curl_close($login);

// Hämta patientdata
$fields  = urlencode('["name","patient_name","sex"]');
$filters = urlencode('[["name","=","'.$patient_id.'"]]');

$patient_url = $baseurl . "api/resource/Patient?fields=$fields&filters=$filters";

$ch = curl_init($patient_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);

$response = curl_exec($ch);
curl_close($ch);

$patients = json_decode($response, true)['data'] ?? [];

if (empty($patients)) {
    die("Kunde inte hitta patientdata i ERPNext.");
}

// Uppdatera session:
$_SESSION['patient_name'] = $patients[0]['patient_name'];
$_SESSION['patient_sex']  = $patients[0]['sex'];

// Hämta vårdpersonal förutom läkare
$practitioners = [];

$prac = curl_init(
    $baseurl . 'api/resource/Healthcare%20Practitioner?' .
    'fields=["name","first_name","last_name","department","designation","status"]' .
    '&filters=[["first_name","LIKE","G6%"]]'
);

curl_setopt($prac, CURLOPT_HTTPGET, true);
curl_setopt($prac, CURLOPT_RETURNTRANSFER, true);
curl_setopt($prac, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($prac, CURLOPT_TIMEOUT, $tmeout);

$resp = curl_exec($prac);
curl_close($prac);

$data = json_decode($resp, true)['data'] ?? [];

foreach ($data as $p) {

    $dep  = strtolower($p["department"]   ?? "");
    $desg = strtolower($p["designation"]  ?? "");

    if (strpos($dep, "läkare") !== false || strpos($desg, "specialist") !== false) {
        continue;
    }
    $practitioners[] = $p;
}

// Bokningshantering
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_type'])) {

    $time = $_POST["appointment_time"];
    if (strlen($time) == 5) $time .= ":00";

    $data = [
        "appointment_type" => $_POST["appointment_type"],
        "appointment_date" => $_POST["appointment_date"],
        "appointment_time" => $time,
        "healthcare_practitioner" => $_POST["healthcare_practitioner"],
        "practitioner" => $_POST["healthcare_practitioner"],
        "practitioner_name" => $_POST["practitioner_name"],
        "department" => $_POST["department"],
        "duration" => intval($_POST["duration"]),
        "patient" => $patient_id,
        "patient_name" => $patient_name,
        "patient_sex" => $patient_sex,
        "notes" => $_POST["notes"] ?? ""
    ];

    $json = json_encode($data);

    $ch = curl_init($baseurl . "api/resource/Patient%20Appointment");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $decoded = json_decode($result, true);
    curl_close($ch);

    if (!isset($decoded["data"])) {
        echo "<h2>Fel vid bokning</h2><pre>$result</pre>";
        exit;
    }

    header("Location: index.php");
    exit;
}
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

            <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logga ut</a>

        <?php else: ?>

            <a href="login.php">Logga in</a>

        <?php endif; ?>

        </div>
    </nav>

  <!-- Innehåll för bokningsformulär -->
  <div class="container">
    <h1>Välj en tid</h1>
    <p class="lead">Inloggad som: <strong><?php echo htmlspecialchars($_SESSION['patient_id']); ?></strong></p>

  <form class="booking" method="post" novalidate>

  <input type="hidden" name="patient" value="<?= htmlspecialchars($_SESSION['patient_id']) ?>">
  <input type="hidden" name="patient_name" value="<?= htmlspecialchars($_SESSION['patient_name']) ?>">
  <input type="hidden" name="patient_sex" value="<?= htmlspecialchars($_SESSION['patient_sex'] ?? '') ?>">

  <div class="field">
    <label for="appointment_type">Typ av besök</label>
    <select id="appointment_type" name="appointment_type" required>
      <option value="G6Sjuksköterskebesök">Sjuksköterskebesök</option>
      <option value="G6Provtagning">Provtagning</option>
      <option value="G6Fysioterapi">Fysioterapi</option>
      <option value="G6Samtalsterapi">Samtalsterapi</option>
      <option value="G6Dietistbesök">Dietistbesök</option>
    </select>
  </div>

  <div class="field">
    <label for="healthcare_practitioner">Välj vårdpersonal</label>
    <div class="select-wrap">
      <select id="healthcare_practitioner" name="healthcare_practitioner" required>
        <?php foreach ($practitioners as $p): 
            $id = $p['name'];
            $first = $p['first_name'] ?? '';
            $last = $p['last_name'] ?? '';
            $full = trim("$first $last");
            $dep = $p['department'] ?? 'Allmänt';
        ?>
        <option 
            value="<?= htmlspecialchars($id) ?>"
            data-practitioner-name="<?= htmlspecialchars($full) ?>"
            data-department="<?= htmlspecialchars($dep) ?>"
        >
            <?= htmlspecialchars($full) ?> (<?= htmlspecialchars($dep) ?>)
        </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <input type="hidden" name="practitioner_name" id="practitioner_name">
  <input type="hidden" name="department" id="department">

  <div class="field">
    <label for="appointment_date">Datum</label>
    <input id="appointment_date" type="date" name="appointment_date" required>
  </div>

  <div class="field">
    <label for="appointment_time">Tid</label>
    <select id="appointment_time" name="appointment_time" required>
      <?php 
        for ($hour = 8; $hour <= 15; $hour++): 
            $time = sprintf("%02d:00", $hour);
      ?>
        <option value="<?= $time ?>"><?= $time ?></option>
      <?php endfor; ?>
    </select>
  </div>

  <div class="field">
    <label for="duration">Varaktighet (min)</label>
    <input type="hidden" name="duration" value="60">
    <input id="duration" type="number" value="60" disabled>
  </div>

  <div class="field full">
    <label for="notes">Anteckningar <i>(max 150 ord)</i></label>
    <textarea id="notes" name="notes" placeholder="Skriv eventuella kommentarer här..."></textarea>
  </div>

  <div class="field full">
    <div class="btn-row">
      <button class="btn" type="submit">Boka</button>
    </div>
  </div>

</form>
</div>
</body>
</html>
