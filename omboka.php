<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ====== Grundinställningar ======
$cookiepath = "/tmp/cookies.txt";
$baseurl    = "http://193.93.250.83:8080/";
$timeout    = 3600;

$message = "";

// ============================
// 1. Logga in i ERPNext
// ============================

$login_url = $baseurl . "api/method/login";

$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"e24halal@student.his.se","pwd":"Mustafa65@1999!"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_exec($ch);
curl_close($ch);

// ============================
// 2. Hämta alla bokningar
// ============================

$appointments_url =
    $baseurl .
    "api/resource/Patient%20Appointment" .
    "?fields=[\"*\"]" .
    "&filters=[[\"patient_name\",\"LIKE\",\"%G6%\"]]";

$ch = curl_init($appointments_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Utan ?? :
$appointments = array();
if (isset($data["data"])) {
    $appointments = $data["data"];
}

// ============================
// 3. PUT – Omboka
// ============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Utan ??
    $appointment_name = "";
    if (isset($_POST["appointment_name"])) {
        $appointment_name = $_POST["appointment_name"];
    }

    $new_date = "";
    if (isset($_POST["new_date"])) {
        $new_date = $_POST["new_date"];
    }

    $new_time = "";
    if (isset($_POST["new_time"])) {
        $new_time = $_POST["new_time"];
    }

    // Enkel validering
    if ($appointment_name === "" || $new_date === "" || $new_time === "") {
        $message = "Fyll i alla fält.";
    } else {

        // Lägg till sekunder om de saknas
        if (strlen($new_time) === 5) {
            $new_time = $new_time . ":00";
        }

        $payload = array(
            "appointment_date" => $new_date,
            "appointment_time" => $new_time
        );

        // PUT URL
        $update_url = $baseurl . "api/resource/Patient%20Appointment/" . urlencode($appointment_name);

        $ch = curl_init($update_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $update_response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err !== "") {
            $message = "Fel vid ombokning: " . $err;
        } else {
            $message = "Tiden är ombokad!";
        }
    }
}
?>
<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Omboka tid</title>
</head>
<body>

<h1>Omboka tid</h1>

<?php
if ($message !== "") {
    echo "<p><strong>" . htmlspecialchars($message) . "</strong></p>";
}
?>

<?php if (count($appointments) === 0): ?>

    <p>Inga bokningar hittades.</p>

<?php else: ?>

<form method="post">

    <p>
        <label>Välj bokning:</label><br>
        <select name="appointment_name" required>
            <?php
            foreach ($appointments as $appt) {

                $date = "";
                if (isset($appt["appointment_date"])) {
                    $date = $appt["appointment_date"];
                }

                $time = "";
                if (isset($appt["appointment_time"])) {
                    $time = $appt["appointment_time"];
                }

                $patient = "";
                if (isset($appt["patient"])) {
                    $patient = $appt["patient"];
                }

                $prac = "";
                if (isset($appt["practitioner"])) {
                    $prac = $appt["practitioner"];
                }

                $name = "";
                if (isset($appt["name"])) {
                    $name = $appt["name"];
                }

                $label = $date . " " . $time . " - " . $patient . " / " . $prac;

                echo "<option value='" . htmlspecialchars($name) . "'>" .
                        htmlspecialchars($label) .
                     "</option>";
            }
            ?>
        </select>
    </p>

    <p>
        <label>Nytt datum:</label><br>
        <input type="date" name="new_date" required>
    </p>

    <p>
        <label>Ny tid:</label><br>
        <input type="time" name="new_time" required>
    </p>

    <p>
        <button type="submit">Omboka</button>
    </p>

</form>

<?php endif; ?>

<p><a href="index.php">Tillbaka till startsidan</a></p>

</body>
</html>
