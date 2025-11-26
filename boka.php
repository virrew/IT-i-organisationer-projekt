<?php
session_start();

// Från formuläret:
$contact_field1 = trim($_POST['field1'] ?? '');
$contact_field2 = trim($_POST['field2'] ?? '');
$contact_field3 = trim($_POST['field3'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Spara bara om alla fält är ifyllda
    if ($contact_field1 !== '' && $contact_field2 !== '' && $contact_field3 !== '') {
        $_SESSION['contact_data'] = [
            'field1' => $contact_field1,
            'field2' => $contact_field2,
            'field3' => $contact_field3
        ];
    }
}
$contactData = $_SESSION['contact_data'] ?? null;

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

$ch = curl_init($baseurl . 'api/resource/Healthcare%20Practitioner?fields=["first_name","last_name"]&filters=[["first_name","LIKE","%G6%"]]');
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

$patient_url = $baseurl . 'api/resource/Patient?fields=["patient_name"]&filters=[["patient_name","LIKE","%G6%"]]';
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

$session_user = $_SESSION['username'] ?? 'Guest';
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
    <nav class="navbar">
    <div class="nav-brand">Mölndals Vårdcentral</div>
    <div class="nav-links">
      <a href="index.php">Hem</a>
      <a href="recept.php">Mina recept</a>
      <a href="boka.php">Mina bokningar</a>
      <a href="journal.php">Min journal</a>
      <a href="Kontakt.php">Kontakt</a>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
      <!-- Namnet i navbaren är bortkommenterat -->
      <!-- <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span> -->
      <a href="logout.php">Logga ut</a>
      <?php else: ?>
      <a href="login.php">Logga in</a>
      <?php endif; ?>
    </div>
  </nav>

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

    <!-- Ifall man vill se vem som är inloggad -->
    <p class="lead">Inloggad som: <strong><?php echo htmlspecialchars($session_user); ?></strong></p>

    <form class="booking" method="post" novalidate>
      <input type="hidden" name="patient" value="<?php echo htmlspecialchars($session_user); ?>">

      <div class="field">
        <label for="practitioner">Välj vårdgivare</label>
        <div class="select-wrap">
          <select id="practitioner" name="practitioner" required>
            <?php foreach ($practitioners as $practitioner): ?>
              <option value="<?php echo htmlspecialchars($practitioner['name'] ?? ($practitioner['first_name'].' '.$practitioner['last_name'])); ?>">
                <?php echo htmlspecialchars(trim(($practitioner['first_name'] ?? '').' '.($practitioner['last_name'] ?? ''))); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="field">
        <label for="appointment_date">Datum</label>
        <input id="appointment_date" type="date" name="appointment_date" required>
      </div>

      <div class="field">
        <label for="appointment_time">Tid</label>
        <input id="appointment_time" type="time" name="appointment_time" required>
      </div>

      <div class="field">
        <label for="duration">Varaktighet (min)</label>
        <input id="duration" type="number" name="duration" min="1" value="30">
      </div>

      <div class="field">
        <label for="notes">Anteckningar</label>
        <textarea id="notes" name="notes" placeholder="Skriv eventuella kommentarer här..."></textarea>
      </div>

      <div class="field full">
        <div class="btn-row">
          <button class="btn" type="submit">Välj</button>
          <div style="flex:1"></div>
        </div>
      </div>
    </form>
  </div>
</body>
</html>
