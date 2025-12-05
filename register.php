<?php
// register.php
// Fungerande register-sida som skapar Patient i ERPNext.
// All POST-logik körs innan någon HTML-output skickas.


// Starta session tidigt
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/* ================================
   HANTERA POST (registrering)
   ================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['firstname'])) {
    // Hämta inmatade värden (trim + fallback)
    $firstname = trim($_POST["firstname"] ?? "");
    $lastname  = trim($_POST["lastname"] ?? "");
    $sex       = trim($_POST["sex"] ?? "");
    $dob       = trim($_POST["dob"] ?? "");
    $ssn       = trim($_POST["ssn"] ?? "");
    $username  = trim($_POST["username"] ?? "");
    $password  = trim($_POST["password"] ?? "");
    $status    = trim($_POST["status"] ?? "Active");


    // Prefixa G6 framför förnamnet om det inte redan finns
    if ($firstname !== "" && strpos($firstname, "G6") !== 0) {
        $firstname = "G6" . $firstname;
    }


    // Validering av obligatoriska fält
    if ($firstname === "" || $lastname === "" || $sex === "" || $ssn === "" || $username === "" || $password === "") {
        // Visa tydligt meddelande och stoppa
        die("<h3 style='color:red;'>Något obligatoriskt fält saknas. Vänligen fyll i formuläret korrekt.</h3>");
    }


    // Fullständigt patientnamn som ERPNext förväntar sig
    $patient_name = $firstname . " " . $lastname;


    // Spara patient_name i session (kan användas i andra sidor)
    $_SESSION['patient_name'] = $patient_name;


    /* ================================
       Logga in i ERPNext (session cookie)
       ================================ */
    $baseurl = "http://193.93.250.83:8080/";
    $cookiepath = "/tmp/cookies.txt";
    $tmeout = 30;


    $login_ch = curl_init($baseurl . "api/method/login");
    curl_setopt($login_ch, CURLOPT_POST, true);
    curl_setopt($login_ch, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se","pwd":"HisLeo25!"}');
    curl_setopt($login_ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($login_ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($login_ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($login_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($login_ch, CURLOPT_TIMEOUT, $tmeout);


    $login_response = curl_exec($login_ch);
    $login_errno = curl_errno($login_ch);
    $login_error = curl_error($login_ch);
    curl_close($login_ch);


    if ($login_errno) {
        die("<h3>Tekniskt fel vid login mot ERPNext:</h3><pre>$login_error</pre>");
    }


    /* ================================
       Bygg payload för Patient
       ================================ */
    $patient_data = [
        "first_name"   => $firstname,
        "last_name"    => $lastname,
        "patient_name" => $patient_name,
        "sex"          => $sex,
        "dob"          => ($dob === "" ? null : $dob),
        "uid"          => $ssn,
        "status"       => $status
    ];


    $json = json_encode($patient_data, JSON_UNESCAPED_SLASHES);


    /* ================================
       POST till ERPNext: skapa Patient
       ================================ */
    $ch = curl_init($baseurl . "api/resource/Patient");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);


    $erp_response = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);


    if ($curl_errno) {
        die("<h3>Tekniskt fel vid POST mot ERPNext:</h3><pre>$curl_error</pre>");
    }


    $erp_result = json_decode($erp_response, true);


    // Godkända scenarion: HTTP 200/201 eller svar som innehåller data/message
    $success = false;
    if ($http_code == 200 || $http_code == 201) $success = true;
    if (isset($erp_result['data'])) $success = true;
    if (isset($erp_result['message'])) $success = true;


    if (!$success) {
        // Visa debug-info så du ser varför det misslyckades
        echo "<h2 style='color:red;'>Fel: ERPNext kunde inte skapa patienten.</h2>";
        echo "<h3>JSON som skickades:</h3><pre>" . htmlspecialchars($json) . "</pre>";
        echo "<h3>ERPNext svar (rått):</h3><pre>" . htmlspecialchars($erp_response) . "</pre>";
        echo "<h3>ERPNext svar (strukturerat):</h3><pre>" . print_r($erp_result, true) . "</pre>";
        exit;
    }


    // Om ERPNext returnerar patientens id (name), spara det i session
    if (isset($erp_result['data']['name'])) {
        $_SESSION['patient_id'] = $erp_result['data']['name'];
    } elseif (isset($erp_result['message'])) {
        // ibland svarar ERPNext med "message": "Patient Created Successfully" men inte data
        // För att vara säker kan vi försöka hämta patient via patient_name senare om behövs.
    }


    // Registrering klar — redirect till startsida eller profil
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registrera konto - Mölndals vårdcentral</title>
    <style>
        :root {
            --primary-blue: #1F6F78;
            --primary-blue-light: #C2EBE8;
            --mint-green: #E7FFF3;
            --white: #FFFFFF;
            --gray-light: #F5F5F5;
            --text-dark: #0E2A2C;
        }
        body {
            margin:0;
            padding:0;
            background:var(--gray-light);
            font-family: Arial, Helvetica, sans-serif;
            color:var(--text-dark);
        }
        h1 { text-align:center; margin-top:30px; color:var(--primary-blue); }
        .form-container {
            max-width:420px;
            margin:40px auto;
            background:var(--white);
            padding:30px;
            border-radius:12px;
            box-shadow:0 6px 24px rgba(0,0,0,0.06);
        }
        .form-label { display:block; margin-bottom:6px; font-weight:600; }
        .form-input {
            width:100%;
            padding:10px;
            margin-bottom:16px;
            border-radius:8px;
            border:1px solid var(--primary-blue-light);
            background:var(--mint-green);
            box-sizing:border-box;
        }
        .btn-primary {
            width:100%;
            padding:12px;
            background:var(--primary-blue);
            color:white;
            border:none;
            border-radius:8px;
            font-weight:600;
            cursor:pointer;
        }
        .hint { font-size:0.9rem; color:#555; margin-bottom:12px; }
    </style>
</head>
<body>


<h1>Mölndals vårdcentral — Registrera konto</h1>


<div class="form-container">
    <h2 style="margin-top:0; color:var(--primary-blue);">Registreringsformulär</h2>


    <form action="register.php" method="POST" autocomplete="off">
        <label class="form-label">Förnamn</label>
        <input class="form-input" type="text" name="firstname" required placeholder="Ex: Karl">


        <label class="form-label">Efternamn</label>
        <input class="form-input" type="text" name="lastname" required placeholder="Ex: Karlsson">


        <label class="form-label">Kön</label>
        <select class="form-input" name="sex" required>
            <option value="">Välj...</option>
            <option value="Male">Man</option>
            <option value="Female">Kvinna</option>
        </select>


        <label class="form-label">Födelsedatum</label>
        <input class="form-input" type="date" name="dob">


        <label class="form-label">Personnummer (ÅÅÅÅMMDD-XXXX)</label>
        <input class="form-input" type="text" name="ssn" required pattern="[0-9]{8}-[0-9]{4}" placeholder="20000101-1234">


        <label class="form-label">Användarnamn</label>
        <input class="form-input" type="text" name="username" required>


        <label class="form-label">Lösenord</label>
        <input class="form-input" type="password" name="password" required>


        <input type="hidden" name="status" value="Active">


        <div class="hint">Obs: Förnamnet kommer automatiskt få prefixet <strong>G6</strong> när det sparas.</div>


        <button class="btn-primary" type="submit">Registrera</button>
    </form>
</div>


</body>
</html>



