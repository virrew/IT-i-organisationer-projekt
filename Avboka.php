<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; 
$baseurl = 'http://193.93.250.83:8080/';

// ------------------- LOGIN (DIN KOD INTakt) -------------------
try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ',  $e->getMessage(), "\n";
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
curl_close($ch);

// ---------------------------------------------------------
// AVBOKA (DELETE) 
// ---------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancel_id"])) {

    $cancel_id = $_POST["cancel_id"];

    $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment/' . $cancel_id);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    echo "<div style='background:lightgreen;padding:10px;margin-bottom:10px;'>
            Bokningen <strong>$cancel_id</strong> har tagits bort.
          </div>";
}

// ---------------------------------------------------------
// HÄMTA ALLA BOKNINGAR
// ---------------------------------------------------------
$ch = curl_init($baseurl . 'api/resource/Patient%20Appointment?fields=["name","appointment_date","status"]');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

// ---------------------------------------------------------
// VISA LISTA MED BOKNINGAR
// ---------------------------------------------------------
echo "<h2>Bokade tider</h2>";

foreach ($data["data"] as $app) {

    echo "{$app["name"]} – {$app["appointment_date"]} – {$app["status"]} ";

    // Avboka-knapp som DELETE
    echo '<form method="post" style="display:inline;">
            <input type="hidden" name="cancel_id" value="' . $app["name"] . '">
            <button type="submit" style="color:red;margin-left:10px;">Avboka</button>
          </form>';

    echo "<br>";
}

?>
