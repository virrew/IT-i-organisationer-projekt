<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$baseurl = "http://193.93.250.83:8080/";
$timeout = 3600;

/* ===== 1. LOGGAR IN ===== */
$ch = curl_init($baseurl . "api/method/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"e24halal@student.his.se","pwd":"Mustafa65@1999!"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_exec($ch);
curl_close($ch);

/* ===== 2. HÄMTAR ALLA BOKNINGAR ===== */
$appointments = [];
$fields = urlencode('["appointment_date","patient_name","status","duration","appointment_based_on_check_in"]');
$filters = urlencode('[["patient_name","LIKE","%G6%"]]');

$bokningar = $baseurl . '/api/resource/Patient%20Appointment?fields=' . $fields . '&filters=' . $filters;


$ch = curl_init($bokningar);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!empty($data["data"])) {
    $appointments = $data["data"];
}

/* ===== 3. OM POST OMBOKA ===== */
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_name = $_POST["appointment_name"];
    $new_date = $_POST["new_date"];
    $new_time = $_POST["new_time"];

    $payload = [
        "appointment_date" => $new_date,
        "appointment_time" => $new_time
    ];

    $update_url = $baseurl . "'/api/resource/Patient%20Appointment?fields=[%22*%22]&filters=[[%22patient_name%22,%22LIKE%22,%22%G6%%22]]';" . $appointment_name;

    $ch = curl_init($update_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $update_response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        $message = "Fel: " . $err;
    } else {
        $message = "Tiden är ombokad!";
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Omboka tid</title></head>
<body>

<h1>Omboka tid</h1>

<p><strong><?php echo $message; ?></strong></p>

<form method="post">

    <label>Välj bokning:</label><br>
    <select name="appointment_name" required>
        <?php
        foreach ($appointments as $appt) {
            $label = $appt["appointment_date"] . " " . $appt["appointment_time"] .
                     " - " . $appt["patient"] . " / " . $appt["practitioner"];
            echo "<option value='" . $appt["name"] . "'>$label</option>";
        }
        ?>
    </select><br><br>

    <label>Nytt datum:</label><br>
    <input type="date" name="new_date" required><br><br>

    <label>Ny tid:</label><br>
    <input type="time" name="new_time" required><br><br>

    <button type="submit">Omboka</button>
</form>

</body>
</html>
