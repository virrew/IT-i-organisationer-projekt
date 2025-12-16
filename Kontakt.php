<?php
//Ser till att alla fel visas, tas bort när sidan är klar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Väg och tid för cookie
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

//Inlogg data
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24amala@student.his.se", "pwd":"VisslanChess15"}');  

// Ställer in hur det ska skickas
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

//Kollar erp
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

//Sätter cookies
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);

//Timeout sätts och return transfer
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//Får svar om inlogg lyckats
$response = curl_exec($ch);
$response = json_decode($response, true);

//Kollar efter fel vid inlogg
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);



//ser till att saker bara händer om ålder skickats, random val
if(isset($_POST['age'])){
   
  //Variabler blir tilldelade värden
  if (isset($_POST['age'])) {
    $age = $_POST['age'];
} else {
    $age = "";
}

if (isset($_POST['gender'])) {
    $gender = $_POST['gender'];
} else {
    $gender = "";
}

if (isset($_POST['able'])) {
    $able = $_POST['able'];
} else {
    $able = "";
}

if (isset($_POST['easy'])) {
    $easy = $_POST['easy'];
} else {
    $easy = "";
}

if (isset($_POST['happy'])) {
    $happy = $_POST['happy'];
} else {
    $happy = "";
}

if (isset($_POST['meet'])) {
    $meet = $_POST['meet'];
} else {
    $meet = "";
}

if (isset($_POST['time'])) {
    $time = $_POST['time'];
} else {
    $time = "";
}

if (isset($_POST['info'])) {
    $info = $_POST['info'];
} else {
    $info = "";
}

if (isset($_POST['understand'])) {
    $understand = $_POST['understand'];
} else {
    $understand = "";
}

if (isset($_POST['explain'])) {
    $explain = $_POST['explain'];
} else {
    $explain = "";
}

if (isset($_POST['did'])) {
    $did = $_POST['did'];
} else {
    $did = "";
}

if (isset($_POST['extra'])) {
    $extra = $_POST['extra'];
} else {
    $extra = "";
}
  
  //Array skapas och görs om till JSON, datan kopplas med fields i erp.
 $postfields = json_encode([
    "age" => $age,
    "gender" => $gender,
    "able" => $able,
    "easy" => $easy,
    "happy" => $happy,
    "meet" => $meet,
    "time" => $time,
    "info" => $info,
    "understand" => $understand,
    "explain" => $explain,
    "did" => $did,
    "extra" => $extra
]);

//url för bemötande
$ch = curl_init(
    $baseurl . "api/resource/G6FeedbackForm"
);

//Data skickas in
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

//Ser till att post lagrats och kan användas till meddelande för användare
 header("Location: " . $_SERVER['PHP_SELF'] . "?sent=1");
    exit;
}
?>

<!doctype html>
<html lang="sv">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Formulär för bemötande</title>
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
      margin: 0;
      padding: 0;
    }

    h1{
      text-align: center;
    }

   
    
    form{
      border-style: solid;
      border-color: var(--primary-blue);
      border-radius: 50px;
      padding: 20px;
      width: 50%;
      margin: 40px auto;
      background-color: white;
    }
    
    fieldset{
      margin-top: 20px;
      border-radius: 50px;
      border-color: var( --primary-blue-light);
    }

     legend{
       color: var(--primary-blue);
       font-weight: bold;
       text-align: center;
    }

    p{
      display: block;
margin-top: 16px; 
    }

    label {
  font-weight: 500;
  color: var(--text-dark);
}
    label[for="gender"]{
       display: block;
    }

   label[for="extra"] {
    display: block;    
    margin-top: 20px;  
    margin-bottom: 0;  
}
    textarea{
      width: 100%;
      border-radius: 20px;
    }

    input[type="submit"]{
      display: block;
      margin: 0 auto;
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

    input[type="submit"]:hover{
      transform: translateY(-2px);
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

    footer {
      font-size: 80%;
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
        
          <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="logout.php">Logga ut</a>       
        
        <?php else: ?>
          <a href="login.php">Logga in</a>
        <?php endif; ?>
      </div>
    </nav>
    
    <?php
    //om data skickats så ska tack meddelande visas.
    if (isset($_GET['sent']) && $_GET['sent'] == 1) {
      echo "<h2 style='color: green; text-align:center;'>Tack! Ditt formulär har skickats.</h2>";
    }
    ?>
    
    <form method="post" action="">
      <h1>Formulär för bemötande</h1>
      <label for="age">Hur gammal är du?</label><br>
      <input type="number" id="age" name="age" required>
      
      <label for="gender">Kön</label>
      <input type="text" id="gender" name="gender">
      
      
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