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
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se", "pwd":"HisLeo25!"}'); 
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
$ch = curl_init($baseurl . 'api/resource/Healthcare%20Practitioner?fields=[%22first_name%22,%20%22name%22]&filters=[[%22first_name%22,%22LIKE%22,%22%G6%%22]]'); 

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
  echo $value["name"]."<br>";
}

?>

<?php
session_start();

//   FETCH('http://193.93.250.83:8080/api/resource/Healthcare%20Practitioner?fields=[%22first_name%22,%20%22name%22]&filters=[[%22first_name%22,%22LIKE%22,%22%G6%%22]]', {
//     headers: {
//         'Authorization': '49faecfb2c53bd2:7fe935b2a6dbd0b'
//     }
//     })
//     .then(r => r.json())
//     .then(r => {
//     console.log(r);
//})


?>
<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Boka tid</title>
</head>
<body>

<h1>Boka tid hos oss ssk</h1>
<!-- Todo: Gör kontroll på maxord -->
<form method="post" action="process_booking.php">
  <input type="hidden" name="patientname" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
  <label for="field1">Ge en kort beskrivning av dina besvär <i> Max 150 ord</i></label><br>
  <input type="text" id="field1" name="field1" required><br><br>
  
  <label for="field2">Hur länge har du haft besvären?<i> Max 50 ord</i></label><br>
  <input type="text" id="field2" name="field2" required><br><br>
  
  <label for="field3">Har du sökt vård för detta tidigare? <i>Ja/nej, om ja vart?</i></label><br>
  <input type="text" id="field3" name="field3" required><br><br>
  <input type="submit" value="Boka tid">

</body>
</html>