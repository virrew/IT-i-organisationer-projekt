<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {

        $pdo = new PDO(
            'mysql:dbname=grupp6;host=localhost;charset=utf8mb4',
            'sqllab',
            'Armadillo#2025',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $sql = "SELECT ssn, erpid, username, password 
                FROM logindetails 
                WHERE username = :username";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $_POST['username']]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            $erpid = $user['erpid'];
            $_SESSION['patient_id'] = $erpid;

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
            background: #F7FAFA;
            font-family: Arial, sans-serif;
            color: var(--text-dark);
        }

        .hero {
            width: 100%;
            background: url('https://images.unsplash.com/photo-1583912372067-1f1d07d6d7f5?auto=format&fit=crop&w=1950&q=80') center/cover no-repeat;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            text-shadow: 0px 3px 10px rgba(0,0,0,0.5);
        }
        .hero h1 {
            font-size: 2.6rem;
            font-weight: bold;
        }

        h2 {
            text-align: center;
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
            margin-bottom: 6px;
            font-weight: bold;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid var(--primary-blue-light);
            background: var(--mint-green);
            font-size: 1rem;
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
        }
        .btn-primary:hover { background: var(--info-blue); }

        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: var(--accent-orange);
            border: none;
            border-radius: 999px;
            color: var(--white);
            margin-top: 10px;
            font-size: 1rem;
            cursor: pointer;
        }

        .wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
    

        footer {
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

        .news-section {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 20px;
        }

        .news-card {
            background: var(--white);
            padding: 20px;
            border-left: 6px solid var(--primary-blue);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        .patient-options {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            align-items: stretch; /* gör boxarna lika höga */
            margin-bottom: 40px;
       }

        .option-box, .Registrera {
            flex: 1;
            min-width: 300px;
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
            text-align: center;
            display: flex;
            flex-direction: column;
        }
                    

            /* För att fixa rutorna mindre */
            .option-box h2, .Registrera h2 {
                margin-top: 0;
                margin-bottom: 1px;           /* mindre rubrik */
            }

            .option-box p, .Registrera p {
                margin: 1px 0;
                font-size: 0.9rem;
            }
            /* E tjänst rutorna */
            .info {
                max-width: 700px;
                margin: 40px auto;
                background: var(--white);
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 4px 18px rgba(0,0,0,0.08);
                text-align: center;
                    }

        .info h2 {
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-size: 1.4rem;
        }

        .infoh3 {
            color: var(--info-blue);
            margin-top: 20px;
            margin-bottom: 8px;
        }

        .info p {
            margin: 6px 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }


        .welcome {
            background: var(--primary-blue);
            color: white;
            padding: 25px 0;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            display: flex;
            justify-content: center;   /* centrerar horisontellt */
            align-items: center;       /* centrerar vertikalt */

        }
        .intro-box {
            max-width: 750px;
            margin: 25px auto 35px;
            padding: 10px 10px;
            color: var(--text-dark);
            text-align: center;
            font-size: 1.1rem;
            border-bottom: 1px solid #d0d0d0;
        }

        .Feedback{
            max-width: 700px;                 
            margin: 40px auto;
            background-color: white;
            padding: 20px;
            border-radius: 12px;             
            box-shadow: 0 4px 18px rgba(0,0,0,0.08); 
            text-align: center;
        }
        #Flink{
            width: 100%;
            padding: 12px;
            background: var(--primary-blue);
            border: none;
            border-radius: 999px;
            color: var(--white);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        #Flink:hover { background: var(--info-blue); }
    </style>
</head>

<body>

<div class="welcome">
    Välkommen till Mölndalsvårdcentral
</div>

<div class="intro-box">
   På Mölndals vårdcentral erbjuder vi råd, bedömning och behandling för både tillfälliga och långvariga hälsobesvär.
   Vi finns här för att hjälpa dig med allt från vardagliga besvär till frågor som rör livsstil, hälsa och förebyggande vård.
</div>

<div class="wrapper">

    <div class="patient-options">

        <div class="option-box">
            <h2>Är du redan patient?</h2>
            <p>Logga in med ditt konto för att boka tider, se recept, provsvar och mer.</p>

            <div class="form-container">
                <form action="login.php" method="POST">
                    <label class="form-label" for="username">Användarnamn</label>
                    <input type="text" id="username" name="username" class="form-input" required>

                    <label class="form-label" for="password">Lösenord</label>
                    <input type="password" id="password" name="password" class="form-input" required>

                    <button type="submit" class="btn-primary" onclick="return confirm('Starta BankID');">
                        Logga in
                    </button>
                </form>
            </div>
        </div>

        <div class="Registrera">
            <h2>Ny patient?</h2>
            <p>Är du ny hos oss? Registrera dig enkelt för att ta del av våra vårdtjänster.</p>
            <form action="register.php" method="POST">
                <button class="btn-secondary">📝 Registrera dig här</button>
            </form>
        </div>

    </div>

</div>


<div class="info">
    <h2>  Digital vård hos Mölndalsvårdcentral</h2>
    <p>
Hos oss kan du enkelt sköta många vårdärenden online, som att boka tid, se dina kommande besök, läsa journalanteckningar och förnya recept.
Logga in eller registrera dig för att få tillgång till alla våra digitala tjänster.
<p>
</div>


<div class="Feedback">
    <h2>Utvärdera ditt vårdbesök</h2>
    <p>
    Om du nyligen haft ett vårdmöte hos oss får du gärna fylla i vårt formulär om hur du upplevde bemötandet.
        <br>
        Din feedback betyder mycket för oss.
    </p>
    <a href="feedback.php" id="Flink">Klicka här</a>
</div>

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
