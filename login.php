<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {

        // DB-anslutning
        $pdo = new PDO(
            'mysql:dbname=grupp6;host=localhost;charset=utf8mb4',
            'sqllab',
            'Armadillo#2025',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Hämta användaren på username
        $sql = "SELECT ssn, erpid, username, password 
                FROM logindetails 
                WHERE username = :username";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $_POST['username']]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kontrollera om användaren finns
        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            $erpid = $user['erpid'];
            $_SESSION['patient_id'] = $erpid; // ex: HLC-PAT-2025-00018

            // Hämta patientens name från ERPNext

            $baseurl = "http://193.93.250.83:8080/";
            $cookiepath = "/tmp/cookies_login.txt";

            $login = curl_init($baseurl . "api/method/login");
            curl_setopt($login, CURLOPT_POST, true);
            curl_setopt($login, CURLOPT_POSTFIELDS, '{"usr":"a23leola@student.his.se","pwd":"HisLeo25!"}');
            curl_setopt($login, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($login, CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($login, CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
            curl_exec($login);
            curl_close($login);

            $ch = curl_init($baseurl . "api/resource/Patient/" . urlencode($erpid));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $patientResponse = curl_exec($ch);
            curl_close($ch);

            $patient = json_decode($patientResponse, true);

            // Spara patientens "name"
            if (isset($patient['data']['name'])) {
                $_SESSION['patient_name'] = $patient['data']['name'];
                $_SESSION['patient_sex']  = $patient['data']['sex'] ?? "";
                $_SESSION['patient_dob']  = $patient['data']['dob'] ?? "";
            } else {
                $_SESSION['patient_name'] = $_SESSION['username'];
            }

            header("Location: index.php");
            exit;
        }

        // Misslyckad inloggning
        echo "<p style='color:red; text-align:center;'>Fel användarnamn eller lösenord.</p>";
        echo '<p style="text-align:center;"><a href="login.php">Försök igen</a></p>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mölndalsvårdcentral - Login</title>
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
            margin-top: 40px;
            margin-bottom: 0;
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

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-input {
            display: block;
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
            border-radius: 999px;
            color: var(--white);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            transition: transform 0.1s ease, box-shadow 0.1s ease, background 0.1s ease;
        }

        .btn-primary:hover {
            background: var(--info-blue);
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: var(--accent-orange);
            border: none;
            border-radius: 999px;
            color: var(--white);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            transition: 0.1s ease;
        }

        .btn-secondary:hover {
            background: #e88d59;
        }
    </style>
</head>
<body>

    <h1>Välkommen till Mölndalsvårdcentral</h1>

    <div class="form-container">
        <h2>Logga in för att ta del av våra tjänster</h2>

        <!-- LOGIN FORM -->
        <form action="login.php" method="POST">
            <label class="form-label" for="username">Användarnamn:</label>
            <input type="text" id="username" name="username" class="form-input" required>

            <label class="form-label" for="password">Lösenord:</label>
            <input type="password" id="password" name="password" class="form-input" required>

            <button type="submit" class="btn-primary" onclick="return confirm('Starta BankID');"> <!-- Bank-ID simulering -->
                Logga in
            </button>
        </form>

        <!-- REGISTER BUTTON -->
        <form action="register.php" method="POST">
            <button type="submit" class="btn-secondary">Ny patient? Registrera här</button>  
        </form>
    </div>

</body>
</html>