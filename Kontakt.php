
<!doctype html>
<html lang="sv">
          <?php
//Ser till att alla fel visas, tas bort när sidan är klar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//Väg och tid för cookie, görs inget på en viss tid ..
$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)


// URL som jag ska jobba med
$baseurl = 'http://193.93.250.83:8080/'; 


//metod för inloggning
try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ',  $e->getMessage(), "\n";
}

curl_setopt($ch, CURLOPT_POST, true);

//  ----------  Här sätter ni era login-data ------------------ //
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24amala@student.his.se", "pwd":"VisslanChess15"}');  

// Atäller in hur det ska skickas
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

//Kolalr erp
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

//Sätter cookies
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);

//Timeout sätts och return transfer
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//Får svar om inlogg lyckats
$response = curl_exec($ch);
$response = json_decode($response, true);

//Kollar efter fel vid inlogg
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);


//Skriver ut vad som är fel när det är fel.
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

//skriver ut response, status
echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '$response<br><pre>';
echo print_r($response) . "</pre><br>";
echo "</div>";


$fields = urlencode(json_encode(["*"]));

$ch = curl_init(
    $baseurl . "api/resource/G6FeedbackForm?fields=$fields"
);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$response = json_decode($response, true);
curl_close($ch);


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
if (isset($response['data']) && is_array($response['data'])) {
    foreach ($response['data'] as $array) {
      foreach($array as $a => $row)
        echo $row . "<br>";
    }
} else {
    echo "Inga data kunde hämtas.";
}

echo "insert into tabell(name, owner, creation, modified, modif) values()"

?>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kontaktsida</title>
</head>
<body>


<h1>Formulär för bemötande<h1>


<?php

echo "<form method='post' action='kontakt.php'>";

echo "<input type='number' id='age' name='age'>";
echo "<label for='age'>Hur gammal är du?</label><br>";

echo "<input type='text' id='gender' name='gender'>";
echo "<label for='gender'>Kön...</label><br>";

echo "<fieldset>";
echo "<legend>Allmänt om ditt besök på vårdcentralen</legend>";

echo "<p>Fick du möjlighet att ställa frågorna du önskade?</p>";
echo "<input type='radio' id='Yes1' name='Quest' value='Yes'>";
echo "<label for='Yes1'>Ja</label><br>";
echo "<input type='radio' id='No1' name='Quest' value='No'>";
echo "<label for='No1'>Nej</label><br>";

echo "<p>Var det enkelt att ta till sig informationen under vårdmötet?</p>";
echo "<input type='radio' id='Yes2' name='info' value='Yes'>";
echo "<label for='Yes2'>Ja</label><br>";
echo "<input type='radio' id='No2' name='info' value='No'>";
echo "<label for='No2'>Nej</label><br>";

echo "<p>Är du nöjd med det sätt du kan komma i kontakt med vårdcentralen?</p>";
echo "<input type='radio' id='Yes3' name='nojd' value='Yes'>";
echo "<label for='Yes3'>Ja</label><br>";
echo "<input type='radio' id='No3' name='nojd' value='No'>";
echo "<label for='No3'>Nej</label><br>";

echo "<p>Fick du besöka vårdcentralen inom en rimlig tid?</p>";
echo "<input type='radio' id='Yes4' name='rimlig' value='Yes'>";
echo "<label for='Yes4'>Ja</label><br>";
echo "<input type='radio' id='No4' name='rimlig' value='No'>";
echo "<label for='No4'>Nej</label><br>";

echo "<p>Var väntan i väntrummet längre än 20 min?</p>";
echo "<input type='radio' id='Yes5' name='rum' value='Yes'>";
echo "<label for='Yes5'>Ja</label><br>";
echo "<input type='radio' id='No5' name='rum' value='No'>";
echo "<label for='No5'>Nej</label><br>";

echo "</fieldset>";

echo "<fieldset>";
echo "<legend>Information och kunskap</legend>";

echo "<p>Fick du tillräckligt med information om din behandling och eventuella bieffekter?</p>";
echo "<input type='radio' id='Yes6' name='be' value='Yes'>";
echo "<label for='Yes6'>Ja</label><br>";
echo "<input type='radio' id='No6' name='be' value='No'>";
echo "<label for='No6'>Nej</label><br>";

echo "<p>Om du ställde frågor till vårdpersonalen fick du svar som du förstod?</p>";
echo "<input type='radio' id='Yes7' name='stod' value='Yes'>";
echo "<label for='Yes7'>Ja</label><br>";
echo "<input type='radio' id='No7' name='stod' value='No'>";
echo "<label for='No7'>Nej</label><br>";

echo "<p>Förklarade läkaren/sjuksköterskan/annan vårdpersonal behandlingen på ett sätt som du förstod?</p>";
echo "<input type='radio' id='Yes8' name='klar' value='Yes'>";
echo "<label for='Yes8'>Ja</label><br>";
echo "<input type='radio' id='No8' name='klar' value='No'>";
echo "<label for='No8'>Nej</label><br>";

echo "<p>Blev du informerad om ett kommande vårdförlopp?</p>";
echo "<input type='radio' id='Yes9' name='kommande' value='Yes'>";
echo "<label for='Yes9'>Ja</label><br>";
echo "<input type='radio' id='No9' name='kommande' value='No'>";
echo "<label for='No9'>Nej</label><br>";

echo "</fieldset>";

echo "<label for='extra'>Är det något från de ovannämnda frågorna som du specifikt vill utveckla?</label><br>";
echo "<textarea maxlength='500' id='extra' name='extra'></textarea>";

echo "<input type='submit' value='Skicka in'>";

echo "</form>";

?>

</body>
</html>