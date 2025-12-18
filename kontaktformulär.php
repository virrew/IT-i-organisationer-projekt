  <?php
  session_start();



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)

$baseurl = 'http://193.93.250.83:8080/'; 


try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ',  $e->getMessage(), "\n";
}

curl_setopt($ch, CURLOPT_POST, true);


curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24amala@student.his.se", "pwd":"VisslanChess15"}');  
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

if (isset($_POST['field1'])) {
    $contact_field1 = trim($_POST['field1']);
} else {
    $contact_field1 = "";
}

if (isset($_POST['field2'])) {
    $contact_field2 = trim($_POST['field2']);
} else {
    $contact_field2 = "";
}

if (isset($_POST['field3'])) {
    $contact_field3 = trim($_POST['field3']);
} else {
    $contact_field3 = "";
}



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)
$baseurl = 'http://193.93.250.83:8080/';



$postfields = json_encode([
    "patientname" => $_SESSION['patient_name'],
    "field1" => $contact_field1,
    "field2" => $contact_field2,
    "field3" => $contact_field3
]);

$ch = curl_init(
    $baseurl . "api/resource/G6Kontaktform"
);


curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$response = json_decode($response, true);

$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);


header("Location: boka.php");
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

    body {
      font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background-color: var(--gray-light);
      margin: 0;
      padding: 0;
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


    /* FORM CONTAINER */
    .form-container {
      max-width: 700px;
      margin: 60px auto;
      background: var(--white);
      padding: 35px;
      border-radius: 14px;
      border: 2px solid var(--primary-blue);
      box-shadow: 0 8px 30px rgba(0,0,0,0.07);
    }

    h1 {
      margin: 0 0 20px;
      font-size: 1.8rem;
      color: var(--primary-blue);
      text-align: center;
    }

    /* FORM INPUTS */
    .field { margin-bottom: 20px; }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      font-size: 0.96rem;
      color: var(--text-dark);
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid var(--primary-blue-light);
      border-radius: 8px;
      background: #ffffff;
      font-size: 1rem;
      transition: 0.15s;
    }

    input:focus, textarea:focus {
      border-color: var(--primary-blue);
      box-shadow: 0 4px 14px var(--shadow-primary);
      outline: none;
    }

    .btn-row {
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }

    button.btn {
      background: var(--primary-blue);
      color: #fff;
      border: none;
      padding: 12px 18px;
      font-weight: 600;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 6px 18px rgba(31,111,120,0.25);
      transition: transform .06s ease, box-shadow .12s ease;
    }

    button.btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(31,111,120,0.35);
    }

    footer {
            background: var(--primary-blue);
            color: var(--white);
            margin-top: 80px;
            padding: 25px;
            text-align: center;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 900px;
            margin: auto;
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
  
  <div class="form-container">
    <h1>Boka tid hos oss</h1>
    <!-- Todo: Gör kontroll på maxord -->
    <form id="intake-form" method="post" action="kontaktformulär.php">
      <input type="hidden" name="patientname" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">

      <div class="field">
        <label for="field1">Ge en kort beskrivning av dina besvär <i> Max 150 ord</i></label>
        <input type="text" id="field1" name="field1" required>
      </div>

      <div class="field">
        <label for="field2">Hur länge har du haft besvären? <i> Max 50 ord</i></label>
        <input type="text" id="field2" name="field2" required>
      </div>

      <div class="field">
        <label for="field3">Har du sökt vård för detta tidigare? <i>Ja/nej, om ja vart?</i></label>
        <input type="text" id="field3" name="field3" required>
      </div>

      <div class="field full">
        <div class="btn-row">
          <button class="btn" type="submit">Boka tid</button>
          <div style="flex:1"></div>
        </div>
      </div>
    </form>
  </div>

    <footer>
    <div class="footer-grid">
        <div>
            <h3>Kontakt</h3>
            <p>✉️ info@molndalsvardcentral.se</p>
            <p>📍 Mölndalsvägen 22</p>
        </div>

        <div>
            <h3>Öppettider</h3>
            <p>Mån–Fre: 08–20</p>
            <p>Lör: 10–14</p>
        </div>

        <div>
            <h3>Akut hjälp</h3>
            <p>Ring 112 vid livshotande tillstånd.</p>
            <p>För rådgivning – 1177 Vårdguiden.</p>
        </div>
    </div>
    <p style="margin-top:20px;">© 2025 Mölndalsvårdcentral</p>
</footer>
</body>
</html>