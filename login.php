<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){ // Kollar om formuläret är postat, alltså om användaren försöker logga in. Annrs går koden direkt till else satsen och visar "Invalid username or password"

if(isset($_POST['username']) && isset($_POST['password'])) { 
    $pdo = new PDO('mysql:dbname=grupp6;host=localhost', 'sqllab', 'Armadillo#2025'); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $stmt = $pdo->prepare('SELECT username, password FROM logindetails WHERE username = :username AND password = :password'); 
    $stmt->execute([ 
        ':username' => $_POST['username'], 
        ':password' => $_POST['password']
    ]); 
    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
    if ($user) { 
    $_SESSION['username'] = $user['username']; 
    $_SESSION['patient_name'] = $user['username']; // Todo: Lägging er egna ERP variabel för patientnamn
    $_SESSION['logged_in'] = true; 

    header('Location: index.php'); //Skicka användaren till index.php efter inloggning
    exit;
} 
     else { 
        echo "Invalid username or password.<br>"; 
        echo '<a href="login.php">Back to Login</a>'; 
        } 
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
        /* :root är högsta nivån i CSS och används för att definiera globala variabler */
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
        <form action="login.php" method="POST"><!--Postar inloggningsformuläret till sig själv för att kunna skapa sessionen, headerlocation sköter förlyttningen till index.php-->
            <label class="form-label" for="username">Användarnamn:</label>
            <input type="text" id="username" name="username" class="form-input" required>

            <label class="form-label" for="password">Lösenord:</label>
            <input type="password" id="password" name="password" class="form-input" required>

            <button type="submit" class="btn-primary" onclick="return confirm('Starta BankID');">
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