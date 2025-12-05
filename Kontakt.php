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
curl_close($ch);

echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '$response<br><pre>';
echo print_r($response) . "</pre><br>";
echo "</div>";






$fields = urlencode('["appointment_date","patient_name","status","duration","appointment_based_on_check_in"]');
$filters = urlencode('[["patient_name","LIKE","%G6%"]]');

$bokningar = $baseurl . '/api/resource/Patient%20Appointment?fields=' . $fields . '&filters=' . $filters;


$ch = curl_init($bokningar);
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

echo "<div style='background-color:lightgray; border:1px solid black'>";
echo '$response<br><pre>';
echo print_r($response) . "</pre><br>";
echo "</div>";
?>









<?php
if(isset($_POST['age'])){
   $postfields = json_encode([
        "age" => $_POST['age'] ?? '',
        "gender" => $_POST['gender'] ?? '',
        "able" => $_POST['able'] ?? '',
        "easy" => $_POST['easy'] ?? '',
        "happy" => $_POST['happy'] ?? '',
        "meet" => $_POST['meet'] ?? '',
        "time" => $_POST['time'] ?? '',
        "info" => $_POST['info'] ?? '',
        "understand" => $_POST['understand'] ?? '',
        "explain" => $_POST['explain'] ?? '',
        "did" => $_POST['did'] ?? '',
        "extra" => $_POST['extra'] ?? ''
    ]);

$ch = curl_init(
    $baseurl . "api/resource/G6FeedbackForm"
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


 header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


?>

<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kontaktsida</title>
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
}

  body{
font-size: 150%;
background-color: var(--gray-light);
  }
  h1, legend{
     text-align: center;
  }

    form{
       border-style: solid;
  border-color: var(--primary-blue);
  border-radius: 50px;
  padding: 20px;
  width: 50%;
  margin: auto auto;
  background-color: white;
    }

    fieldset{
  margin-top: 20px;
 border-radius: 50px;
 border-color: var( --primary-blue-light);
    }

    textarea{
      width: 100%;
      border-radius: 20px;
    }

    input[type="submit"]{
      display: block;
      margin: 0 auto;
      background-color: var(--primary-blue);
      color: white;
      padding: 10px;
      border-radius: 10px;
    }

    input[type="submit"]:hover{
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
<form method="post" action="">
<h1>Formulär för bemötande</h1>
  <input type="number" id="age" name="age" required>
            <label for="age">Hur gammal är du?</label><br>
            
            <input type="text" id="gender" name="gender" required>
            <label for="gender">Kön...</label><br>


   <fieldset>
            <legend>Allmänt om ditt besök på vårdcentralen</legend>
            
            
            <p>Fick du möjlighet att ställa frågorna du önskade?</p>
            <input type="radio" id="Yes1" name="able" value="1" required>
            <label for="Yes1">Ja</label><br>
            
            <input type="radio" id="No1" name="able" value="0">
            <label for="No1">Nej</label><br>
            


            <p>Var det enkelt att ta till sig informationen under vårdmötet?</p>
            <input type="radio" id="Yes2" name="easy" value="1" required>
            <label for="Yes2">Ja</label><br>
            
            <input type="radio" id="No2" name="easy" value="0">
            <label for="No2">Nej</label><br>
            

            
            <p>Är du nöjd med det sätt du kan komma i kontakt med vårdcentralen?</p>
            <input type="radio" id="Yes3" name="happy" value="1" required>
            <label for="Yes3">Ja</label><br>
            
            <input type="radio" id="No3" name="happy" value="0">
            <label for="No3">Nej</label><br>
            

            
            <p>Fick du besöka vårdcentralen inom en rimlig tid?</p>
            <input type="radio" id="Yes4" name="meet" value="1" required>
            <label for="Yes4">Ja</label><br>
            
            <input type="radio" id="No4" name="meet" value="0">
            <label for="No4">Nej</label><br>
            

            
            <p>Var väntan i väntrummet längre än 20 min?</p>
            <input type="radio" id="Yes5" name="time" value="1" required>
            <label for="Yes5">Ja</label><br>
            
            <input type="radio" id="No5" name="time" value="0">
            <label for="No5">Nej</label><br>
        
        </fieldset>

         <fieldset>
            <legend>Information och kunskap</legend>
            
            
            <p>Fick du tillräckligt med information om din behandling och eventuella bieffekter?</p>
            <input type="radio" id="Yes6" name="info" value="1" required>
            <label for="Yes6">Ja</label><br>
            
            <input type="radio" id="No6" name="info" value="0">
            <label for="No6">Nej</label><br>
            

            <p>Om du ställde frågor till vårdpersonalen fick du svar som du förstod?</p>
            <input type="radio" id="Yes7" name="understand" value="1" required>
            <label for="Yes7">Ja</label><br>
            
            <input type="radio" id="No7" name="understand" value="0">
            <label for="No7">Nej</label><br>



            <p>Förklarade läkaren/sjuksköterskan/annan vårdpersonal behandlingen på ett sätt som du förstod?</p>
            <input type="radio" id="Yes8" name="explain" value="1" required>
            <label for="Yes8">Ja</label><br>
            
            <input type="radio" id="No8" name="explain" value="0">
            <label for="No8">Nej</label><br>
            

            
            <p>Blev du informerade om ett kommande världsförlopp?</p>
            <input type="radio" id="Yes9" name="did" value="1" required>
            <label for="Yes9">Ja</label><br>
            
            <input type="radio" id="No9" name="did" value="0">
            <label for="No9">Nej</label><br>
        
        </fieldset>
        <label for="extra">Är det något från de ovannämnda frågorna som du specifikt vill utveckla? </label><br>
   <textarea name="extra" maxlength="500" id="extra"></textarea>

  <input type="submit" value="Skicka in">
</form>



</body>
</html>