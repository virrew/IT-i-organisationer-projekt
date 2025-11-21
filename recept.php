<?php
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
$ch = curl_init($baseurl . 'api/resource/Medication%20Request?fields=[%22practitioner_name%22]&filters=[[%22practitioner_name%22,%22LIKE%22,%22%G6%%22]]'); 

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

//här väljer jag att loopa över alla poster i [data] och för varje resultat så skriver jag ut name
echo "<strong>LISTA:</strong><br>";
foreach($response['data'] AS $key => $value){
  echo $value["practitioner_name"]."<br>";
}

 //Hämtar patienter som har utskrivna recept

 $patient_url = $baseurl . 'api/resource/Medication%20Request?fields=[%22patient_name%22]&filters[[%22patient_name%22,%20%22LIKE%22,%20%22%G6%%22]]';

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

curl_close($ch);

// Visa responsen för debugging
echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '<pre>';
print_r($response);
echo '</pre>';
echo "</div>";

// Loopar igenom patienterna
echo "<strong>Patientlista:</strong><br>";
foreach ($response['data'] as $patient) {
    echo $patient["patient_name"] . "<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept</title>
</head>
<body>

    <section class="header">
    <h1>Mina recept</h1>
    <p>Översikt över dina aktiva och utgångna recept</p>
    </section>

    <!-- Sektion: Aktiva recept -->
    <section class="recept-list">
        <h2>Aktiva recept</h2>

        <!-- RECEPTKORT 1 -->
        <article class="recept-card">

            <!-- översta raden: namn + status -->
            <div class="recept-card-header">
                <h3 class="recept-namn">Metformin Actavis</h3>
                <span class="recept-status">Aktivt</span>
            </div>

            <!-- styrka och dosering -->
            <div class="recept-info">
                <div class="styrka">500 mg</div>
                <div class="dosering">2 tabletter 2 gånger dagligen</div>
            </div>

            <!-- förskrivare och giltighet -->
            <div class="recept-meta">
                <div class="forskrivare">
                    <span class="label">Förskrivare</span>
                    <span class="value">Dr. Anna Svensson</span>
                </div>

                <div class="giltig-tom">
                    <span class="label">Giltig t.o.m.</span>
                    <span class="value">15 oktober 2025</span>
                </div>

                <div class="utfardat">
                    <span class="label">Utfärdat</span>
                    <span class="value">15 oktober 2024</span>
                </div>
            </div>
        </article>

        <!-- RECEPTKORT 2 -->
        <article class="recept-card">

            <div class="recept-card-header">
                <h3 class="recept-namn">Enalapril Sandoz</h3>
                <span class="recept-status">Aktivt</span>
            </div>

            <div class="recept-info">
                <div class="styrka">10 mg</div>
                <div class="dosering">1 tablett 1 gång dagligen</div>
            </div>

            <div class="recept-meta">
                <div class="forskrivare">
                    <span class="label">Förskrivare</span>
                    <span class="value">Dr. Erik Andersson</span>
                </div>

                <div class="giltig-tom">
                    <span class="label">Giltig t.o.m.</span>
                    <span class="value">1 november 2025</span>
                </div>

                <div class="utfardat">
                    <span class="label">Utfärdat</span>
                    <span class="value">1 november 2024</span>
                </div>
            </div>
        </article>

    </section>

    <h2>Utgångna recept</h2>
        <!-- RECEPTKORT 1 -->
          <article class="recept-card">

            <div class="recept-card-header">
                <h3 class="recept-namn">Enalapril Sandoz</h3>
                <span class="recept-status">Aktivt</span>
            </div>

            <div class="recept-info">
                <div class="styrka">10 mg</div>
                <div class="dosering">1 tablett 1 gång dagligen</div>
            </div>

            <div class="recept-meta">
                <div class="forskrivare">
                    <span class="label">Förskrivare</span>
                    <span class="value">Dr. Erik Andersson</span>
                </div>

                <div class="giltig-tom">
                    <span class="label">Giltig t.o.m.</span>
                    <span class="value">1 november 2025</span>
                </div>

                <div class="utfardat">
                    <span class="label">Utfärdat</span>
                    <span class="value">1 november 2024</span>
                </div>
            </div>
        </article>
</body>
</html>