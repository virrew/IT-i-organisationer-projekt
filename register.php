<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
            :root { /* :root är högsta nivån i CSS och används för att definiera globala variabler */
        
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

        body {
            margin: 0;
            padding: 0;
            background: var(--gray-light);
            font-family: Arial, sans-serif;
            color: var(--text-dark);
        }

        h1, h2 {
            text-align: center;
        }

        h1 {
            margin-top: 30px;
            color: var(--primary-blue);
        }

        .form-container {
            max-width: 400px;
            margin: 40px auto;
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .form-container h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: var(--primary-blue);
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border-radius: 6px;
            border: 1px solid var(--primary-blue-light);
            background: var(--mint-green);
            font-size: 1rem;
            box-sizing: border-box;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--primary-blue);
            border: none;
            border-radius: 6px;
            color: var(--white);
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary:hover {
            background: var(--info-blue);
        }
    </style>
</head>
<body>

    <h1>Mölndalsvårdcentral</h1>
    <div class="form-container">
    <h2>Registrerings formulär</h2>

    <form action="register.php" method="POST">

    <div class="form-group">
        <label for="firstname" class="form-label">Förnamn:</label>
        <input type="text" id="firstname" name="firstname" class="form-input" required>
    </div>

    <div class="form-group">
        <label for="lastname" class="form-label">Efternamn:</label>
        <input type="text" id="lastname" name="lastname" class="form-input" required>
    </div>

    <div class="form-group">
        <label for="sex" class="form-label">Kön:</label>
        <select id="sex" name="sex" class="form-input" required>
            <option value="">Välj kön...</option>
            <option value="Male">Man</option>
            <option value="Female">Kvinna</option>
        </select>
    </div>

    <div class="form-group">
        <label for="dob" class="form-label">Födelsedatum:</label>
        <input type="date" id="dob" name="dob" class="form-input">
    </div>

    <div class="form-group">
        <label for="ssn" class="form-label">Personnummer (ÅÅÅÅMMDD-XXXX):</label>
        <input type="text" id="ssn" name="ssn" class="form-input" required pattern="[0-9]{8}-[0-9]{4}">
    </div>

    <div class="form-group">
        <label for="username" class="form-label">Användarnamn:</label>
        <input type="text" id="username" name="username" class="form-input" required>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Lösenord:</label>
        <input type="password" id="password" name="password" class="form-input" required>
    </div>

    <input type="hidden" name="status" value="Active">

    <button type="submit" class="btn-primary">Registrera</button>
    </form>
</div>

<?php

// =========================
// 1. Kolla om POST skickats
// =========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Hämta inmatade värden
    $firstname = trim($_POST["firstname"] ?? "");
    $lastname  = trim($_POST["lastname"] ?? "");
    if (strpos($firstname, 'G6') !== 0) {
        $firstname = 'G6' . $firstname;
    }
    $sex       = trim($_POST["sex"] ?? "");
    $dob       = trim($_POST["dob"] ?? "");
    $ssn       = trim($_POST["ssn"] ?? "");
    $username  = trim($_POST["username"] ?? "");
    $password  = trim($_POST["password"] ?? "");

    // Kontroll – alla obligatoriska fält måste fyllas
    if ($firstname === "" || $lastname === "" || $sex === "" || $ssn === "" ||  $username === "" || $password === "") {
        die("<h3>Något obligatoriskt fält saknas. Vänligen fyll i formuläret korrekt.</h3>");
    }

    // Fullständigt namn (för ERPNext)
    $patient_name = $firstname . " " . $lastname;

    // =========================
    // 2. SPARA I MYSQL-DATABASEN
    // =========================

    // Todo: Kommenterat ut sålänge, Victor fixa databasen sen tjingeling
    // Du ska ta $patient_name (se rad 173) och spara i session sedan tror jag?

    // try {
    //     $pdo = new PDO("mysql:dbname=grupp6;host=localhost", "sqllab", "Armadillo#2025");
    //     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //     $query = "INSERT INTO logindetails (ssn, username, password) VALUES (:ssn, :username, :password)";
    //     $stmt = $pdo->prepare($query);

    //     $stmt->bindParam(":ssn", $ssn);
    //     $stmt->bindParam(":username", $username);
    //     $stmt->bindParam(":password", $password); // ev. password_hash() om du vill kryptera

    //     $stmt->execute();

    // } catch (Exception $e) {
    //     die("Kunde inte spara användare i databasen: " . $e->getMessage());
    // }

    // =========================
    // 3. SKAPA PATIENT I ERPNEXT
    // =========================

    $baseurl = "http://193.93.250.83:8080/";
    $cookiepath = "/tmp/cookies.txt";
    $tmeout = 3600;

    // LOGIN
    $ch = curl_init($baseurl . "api/method/login");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se", "pwd":"HisLeo25!"}');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $login_response = curl_exec($ch);
    curl_close($ch);


    // Patient-data som skickas till ERPNext
    $patient_data = [
        "first_name"   => $firstname,
        "last_name"    => $lastname,
        "patient_name" => $patient_name,
        "sex"          => $sex,
        "dob"          => $dob ?: null,
        "uid"          => $ssn,
        "status"       => "Active"
    ];

    $json = json_encode($patient_data, JSON_UNESCAPED_SLASHES);

    // Skicka POST till ERPNext
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

    $erp_response = curl_exec($ch);
    $erp_result = json_decode($erp_response, true);

    curl_close($ch);


    // =========================
    // 4. Kontrollera ERP-svar
    // =========================
    $http_code = null;
    if (function_exists('curl_getinfo')) {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

    // Godkända scenarion:
    $success = false;

    if ($http_code == 200 || $http_code == 201) {
        $success = true;
    }
    elseif (isset($erp_result["data"])) {
        $success = true;
    }
    elseif (isset($erp_result["message"])) {
        $success = true;
    }

    // Om misslyckat – visa fel
    if (!$success) {
        echo "<h2>Fel: ERPNext kunde inte skapa patienten.</h2>";
        echo "<h3>JSON som skickades:</h3><pre>$json</pre>";
        echo "<h3>ERPNext svar:</h3><pre>" . print_r($erp_result, true) . "</pre>";
        exit;
    }

    // =========================
    // 5. Registreringen lyckades
    // =========================
    header("Location: index.php");
    exit;
}
?>
</body>
</html>