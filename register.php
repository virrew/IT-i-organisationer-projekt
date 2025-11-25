<?php
session_start();
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
            <label for="ssn" class="form-label">Social security number:</label>
            <input type="text" id="ssn" name="ssn" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <button type="submit" class="btn-primary">Register</button>
    </form>
</div>

<?php
    $pdo = new PDO('mysql:dbname=grupp6;host=localhost', 'sqllab', 'Armadillo#2025');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!empty($_POST['ssn']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $querystring = "INSERT INTO logindetails (ssn, username, password) VALUES (:ssn, :username, :password)";
    $stmt = $pdo->prepare($querystring);
    // bindParam
    $ssn = $_POST['ssn'];
    $username = $_POST['username'];
    $hashedPassword = $_POST['password'];
    $stmt->bindParam(':ssn', $ssn);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();
    header("Location: index.php");
    echo "User registered successfully.";
}

?>
</body>
</html>