<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enkel konfiguration
$cookiepath = "/tmp/cookies.txt";
$baseurl    = "http://193.93.250.83:8080/";
$tmeout     = 3600;

$message = "";
$valdt_bokning = "";
$vald_datum    = "";
$lediga_tider  = array();

/* 1. Logga in i ERPNext */
$ch = curl_init($baseurl . 'api/method/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"e24halal@student.his.se", "pwd":"Mustafa65@1999!"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

/* 2. Hämta inloggad patient från sessionen (i stället för G6) */
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['patient_name'])) {
    // Om ingen patient finns i sessionen → skicka tillbaka till startsidan eller login
    header("Location: index.php");
    exit;
}

$patient_id   = $_SESSION['patient_id'];   // t.ex. "PAT-0005"
$patient_name = $_SESSION['patient_name']; // visas i "Inloggad som"

/* 3. Hämta alla bokningar för patienten */
$fields_appt  = urlencode('["name","appointment_date","appointment_time","patient","practitioner","appointment_type"]');
$filters_appt = urlencode('[["patient","=","' . $patient_id . '"]]');

$appointments_url = $baseurl . "api/resource/Patient%20Appointment?fields=" . $fields_appt . "&filters=" . $filters_appt;

$ch = curl_init($appointments_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$appointments_response = curl_exec($ch);
curl_close($ch);

$appointments_data = json_decode($appointments_response, true);
$appointments = array();

if (isset($appointments_data['data'])) {
    $appointments = $appointments_data['data'];
}

if (count($appointments) === 0) {
    // har ingen bokad tid → gå till boka
    header("Location: boka.php");
    exit;
}

/* Hjälp: hitta practitioner för vald bokning */
function hitta_practitioner_for_bokning($appointments, $appointment_name) {
    foreach ($appointments as $appt) {
        if (isset($appt["name"]) && $appt["name"] === $appointment_name) {
            if (isset($appt["practitioner"])) {
                return $appt["practitioner"];
            }
        }
    }
    return "";
}

/* 4. Hantera POST (Visa lediga tider / Omboka) */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["appointment_name"])) {
        $valdt_bokning = $_POST["appointment_name"];
    }
    if (isset($_POST["new_date"])) {
        $vald_datum = $_POST["new_date"];
    }

    // A) Visa lediga tider
    if (isset($_POST["check_availability"])) {

        if ($valdt_bokning === "" || $vald_datum === "") {
            $message = "Välj bokning och datum först.";
        } else {

            $prac_id = hitta_practitioner_for_bokning($appointments, $valdt_bokning);

            if ($prac_id === "") {
                $message = "Kunde inte hitta vårdgivare för bokningen.";
            } else {

                $fields_booked  = urlencode('["appointment_time"]');
                $filters_booked = urlencode(
                    '[["practitioner","=","' . $prac_id . '"],["appointment_date","=","' . $vald_datum . '"]]'
                );

                $booked_url = $baseurl . "api/resource/Patient%20Appointment?fields=" .
                              $fields_booked . "&filters=" . $filters_booked;

                $ch = curl_init($booked_url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
                curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $booked_response = curl_exec($ch);
                curl_close($ch);

                $booked_data  = json_decode($booked_response, true);
                $booked_times = array();

                if (isset($booked_data["data"])) {
                    foreach ($booked_data["data"] as $row) {
                        if (isset($row["appointment_time"])) {
                            $booked_times[] = $row["appointment_time"];
                        }
                    }
                }

                // alla tider 08:00–15:00 (30 min)
                $alla_tider = array(
                    "08:00:00","08:30:00",
                    "09:00:00","09:30:00",
                    "10:00:00","10:30:00",
                    "11:00:00","11:30:00",
                    "12:00:00","12:30:00",
                    "13:00:00","13:30:00",
                    "14:00:00","14:30:00",
                    "15:00:00"
                );

                $lediga_tider = array();
                foreach ($alla_tider as $tid) {
                    if (!in_array($tid, $booked_times)) {
                        $lediga_tider[] = $tid;
                    }
                }

                if (count($lediga_tider) === 0) {
                    $message = "Inga lediga tider för valt datum.";
                }
            }
        }
    }

    // B) Gör ombokningen
    if (isset($_POST["do_reschedule"])) {

        $new_time = "";
        if (isset($_POST["new_time"])) {
            $new_time = $_POST["new_time"];
        }

        if ($valdt_bokning === "" || $vald_datum === "" || $new_time === "") {
            $message = "Välj bokning, datum och tid.";
        } else {

            if (strlen($new_time) === 5) {
                $new_time = $new_time . ":00";
            }

            $payload = array(
                "appointment_date" => $vald_datum,
                "appointment_time" => $new_time
            );

            $update_url = $baseurl . "api/resource/Patient%20Appointment/" . urlencode($valdt_bokning);

            $ch = curl_init($update_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $update_response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err !== "") {
                $message = "Fel vid ombokning: " . $err;
            } else {
                $message = "Tiden är ombokad!";
                $lediga_tider = array();
            }
        }
    }
}
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Omboka tid</title>
  <style>
    :root {
        --primary-blue: #1F6F78;
        --primary-blue-light: #C2EBE8;
        --accent-orange: #FCA06A;
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

    .nav-brand a {
        color: var(--white);
        font-weight: bold;
        font-size: 1.2rem;
    }

    .nav-brand a:hover {
        text-decoration: underline;
    }

    body {
      font-family: "Segoe UI", Arial, sans-serif;
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
      padding: 24px;
      border: 2px solid var(--primary-blue);
      box-shadow: 0 6px 30px rgba(0,0,0,0.06);
    }
    form.booking {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .field { display: flex; flex-direction: column; }
    .full { grid-column: 1 / -1; }
    select, input[type="date"] {
      padding: 8px 10px;
      border: 1px solid var(--primary-blue-light);
      border-radius: 8px;
    }
    .btn-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    button.btn, a.btn {
      background: var(--primary-blue);
      color: #fff;
      border: none;
      padding: 8px 14px;
      border-radius: 10px;
      text-decoration: none;
      cursor: pointer;
      box-shadow: 0 4px 12px var(--shadow-primary);
    }
    a.btn.orange { background: var(--accent-orange); }
    .message {
      padding: 8px 10px;
      border-radius: 8px;
      margin-bottom: 16px;
      background: #dcfce7;
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

  <div class="container">
    <h1>Omboka tid</h1>
    <p>Inloggad som: <strong><?php echo htmlspecialchars($patient_name); ?></strong></p>

    <?php if ($message !== ""): ?>
      <div class="message"><strong><?php echo htmlspecialchars($message); ?></strong></div>
    <?php endif; ?>

    <form method="post" class="booking">

      <div class="field full">
        <label for="appointment_name">Välj bokning att omboka</label>
        <select id="appointment_name" name="appointment_name" required>
          <option value="">-- Välj --</option>
          <?php foreach ($appointments as $appt): ?>
            <?php
              $date = isset($appt["appointment_date"]) ? $appt["appointment_date"] : "";
              $time = isset($appt["appointment_time"]) ? $appt["appointment_time"] : "";
              $pat  = isset($appt["patient"]) ? $appt["patient"] : "";
              $prac = isset($appt["practitioner"]) ? $appt["practitioner"] : "";
              $name = isset($appt["name"]) ? $appt["name"] : "";
              $type = isset($appt["appointment_type"]) ? $appt["appointment_type"] : "";
              $label = $date . " " . $time . " - " . $pat ;
              $selected_attr = "";

              if ($type !== "") {
                 $label .= " – " . $type;
              }
              if ($valdt_bokning !== "" && $valdt_bokning === $name) {
                  $selected_attr = "selected";
              }
            ?>
            <option value="<?php echo htmlspecialchars($name); ?>" <?php echo $selected_attr; ?>>
              <?php echo htmlspecialchars($label); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="new_date">Nytt datum</label>
        <input type="date" id="new_date" name="new_date"
               value="<?php echo htmlspecialchars($vald_datum); ?>" required>
      </div>

      <div class="field full">
        <div class="btn-row">
          <button class="btn" type="submit" name="check_availability">Visa lediga tider</button>
        </div>
      </div>

      <?php if (count($lediga_tider) > 0): ?>
        <div class="field">
          <label for="new_time">Lediga tider</label>
          <select id="new_time" name="new_time" required>
            <option value="">-- Välj tid --</option>
            <?php foreach ($lediga_tider as $tid): ?>
              <?php $visad_tid = substr($tid, 0, 5); ?>
              <option value="<?php echo htmlspecialchars($tid); ?>">
                <?php echo htmlspecialchars($visad_tid); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field full">
          <div class="btn-row">
            <button class="btn" type="submit" name="do_reschedule">Omboka</button>
          </div>
        </div>
      <?php endif; ?>

      <div class="btn-row">
        <a href="boka.php" class="btn orange">Boka ny tid</a>
        <a href="index.php" class="btn orange">Till startsidan</a>
      </div>

    </form>
  </div>
</body>
</html>
