<?php
session_start();

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
    $_SESSION['patient_name'] = $user['username'];
    $_SESSION['logged_in'] = true; 

    header('Location: index.php'); //Skicka användaren till index.php efter inloggning
    exit;
} 
    } else { 
        echo "Invalid username or password.<br>"; 
        echo '<a href="login.php">Back to Login</a>'; 
    } 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <form action="login.php" method="POST"> <!--Postar inloggningsformuläret till sig själv för att kunna skapa sessionen, headerlocation sköter förlyttningen till index.php-->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>

    <form action="register.php" method="POST">
        <label for="create">New member?</label>
        <input type="submit" value="Register here">
    </form>

</body>
</html>