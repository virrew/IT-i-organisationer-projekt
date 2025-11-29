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

//skriver ut response
echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '$response<br><pre>';
echo print_r($response) . "</pre><br>";
echo "</div>";


$ch = curl_init($baseurl . 'api/resource/Healthcare%20Practitioner?fields=[%22first_name%22,%20%22name%22]&filters=[[%22first_name%22,%22LIKE%22,%22%G6%%22]]'); länk grejer ändra

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
<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kontaktsida</title>
</head>
<body>


<h1>Formulär för bemötande<h1>

<form method="post" action="kontakt.php">

  <input type="number" id="age" name="age">
            <label for="age">Hur gammal är du?</label><br>
            
            <input type="text" id="gender" name="gender">
            <label for="gender">Kön...</label><br>


   <fieldset>
            <legend>Allmänt om ditt besök på vårdcentralen</legend>
            
            
            <p>Fick du möjlighet att ställa frågorna du önskade?</p>
            <input type="radio" id="Yes" name="Quest" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="Quest" value="No">
            <label for="No">Nej</label><br>
            


            <p>Var det enkelt att ta till sig informationen under vårdmötet?</p>
            <input type="radio" id="Yes" name="info" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="info" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Är du nöjd med det sätt du kan komma i kontakt med vårdcentralen?</p>
            <input type="radio" id="Yes" name="nojd" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="nojd" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Fick du besöka vårdcentralen inom en rimlig tid?</p>
            <input type="radio" id="Yes" name="rimlig" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="rimlig" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Var väntan i väntrummet längre än 20 min?</p>
            <input type="radio" id="Yes" name="rum" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="rum" value="No">
            <label for="No">Nej</label><br>
        
        </fieldset>

         <fieldset>
            <legend>Information och kunskap</legend>
            
            
            <p>Fick du tillräckligt med information om din behandling och eventuella bieffekter?</p>
            <input type="radio" id="Yes" name="be" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="be" value="No">
            <label for="No">Nej</label><br>
            

            <p>Om du ställde frågor till vårdpersonalen fick du svar som du förstod?</p>
            <input type="radio" id="Yes" name="stod" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="stod" value="No">
            <label for="No">Nej</label><br>



            <p>Förklarade läkaren/sjuksköterskan/annan vårdpersonal behandlingen på ett sätt som du förstod?</p>
            <input type="radio" id="Yes" name="klar" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="klar" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Blev du informerade om ett kommande världsförlopp?</p>
            <input type="radio" id="Yes" name="kommande" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="kommande" value="No">
            <label for="No">Nej</label><br>
        
        </fieldset>
        <label for="extra">Är det något från de ovannämnda frågorna som du specifikt vill utveckla? </label><br>
   <textarea maxlength="500" id="extra"></textarea>

  <input type="submit" value="Skicka in">

</body>
</html>