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
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24hedan@student.his.se", "pwd":"9901hed0199And!"}'); 
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
// if (!isset($_SESSION['patient_namn'])) {
    // Om ingen är inloggad, skicka användaren till login
//    header("Location: login.php");
//    exit;
//} 

// Tillfällig data 
$journaler = [
    [
        "datum" => "2025-11-20",
        "vardgivare" => "Mölndals Vårdcentral",
        "identitet" => "Doris Dorisson (2015-08-17)",
        "vardorsak" => "Ont i halsen",
        "diangoser" => "Viral halsinfektion",
        "undersökning" => "Halsundersökning",
        "behandling" => "Egenvård",
        "info_beslut" => "Informerad om behandling: beslut om egenvård",
        "avbojd_vard" => "Nej",
        "antecknad_av" => "Dr. Karl Svensson"
    ]
]
?> 
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Min journal</title>
</head>
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
        --shadow-primary: rgba(31,111,120,0.25);
    }

    body {
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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
        padding: 28px;
        border: 2px solid var(--primary-blue);
        box-shadow: 0 6px 30px rgba(0,0,0,0.06);
    }

    .navbar {
        background: var(--primary-blue);
        color: var(--white);
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .nav-links a {
        color: var(--white);
        text-decoration: none;
        font-weight: 500;
        margin-left: 16px;
    }

    h1, h2 {
        color: var(--primary-blue);
        margin-bottom: 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    }

    th {
        background: var(--primary-blue);
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 0.95rem;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid var(--primary-blue-light);
        font-size: 0.9rem;
    }

    tr:nth-child(even) {
        background: var(--mint-green);
    }

    tr:hoover {
        background: var(--primary-blue-light);
    }
    </style>
<body>

<!-- Visar vem som är inloggad 
    <div>
        Inloggad som: <strong><//?php  echo $_SESSION['patient_namn']; ?></strong>
        | <a href="logout.php">Logga ut</a>
    </div>
    <hr>
-->

<nav class="navbar">
    <div class="nav-brand">Mölndals Vårdcentral</div>

    <div class="nav-links">
        <a href="index.php">Hem</a>
        <a href="recept.php">Mina recept</a>
        <a href="boka.php">Mina bokningar</a>
        <a href="Kontakt.php">Kontakt</a>
        <a href="logout.php">Logga ut</a>
    </div>
</nav>

<div class="container">

    <h1>Min journal</h1>

    <h2>Journalanteckningar</h2>
    <table border="1">
        <tr>
            <th>Datum</th>
            <th>Vårdgivare</th>
            <th>Identitet</th>
            <th>Vårdorsak</th>
            <th>Diagnoser</th>
            <th>Undersökningar</th>
            <th>Behandlingar</th>
            <th>Information och beslut</th>
            <th>Avböjd vård (Ja/Nej)</th>
            <th>Antecknad av</th>
        </tr>

        <?php foreach ($journaler as $journal): ?>
            <tr>
                <td><?php echo $journal['datum']; ?></td>
                <td><?php echo $journal['vardgivare']; ?></td>
                <td><?php echo $journal['identitet']; ?></td>
                <td><?php echo $journal['vardorsak']; ?></td>
                <td><?php echo $journal['diagnoser']; ?></td>
                <td><?php echo $journal['undersokning']; ?></td>
                <td><?php echo $journal['behandling']; ?></td>
                <td><?php echo $journal['info_beslut']; ?></td>
                <td><?php echo $journal['avbojd_vard']; ?></td>
                <td><?php echo $journal['antecknad_av']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Provsvar-tabell -->
    <h2>Provsvar</h2>
    <table border="1">
        <tr>
            <th>Provnamn</th>
            <th>Datum</th>
            <th>Resultat</th>
            <th>Referensintervall</th>
        </tr>
        <tr>
            <td>Hemoglobin</td>
            <td>2025-11-18</td>
            <td>132 g/L</td>
            <td>120–155 g/L</td>
        </tr>
    </table>

</div>
</body>
