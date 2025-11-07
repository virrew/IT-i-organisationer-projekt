<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
    </form>
    <form action="register.php" method="POST">
        <label for="create">Create Account</label>
        <input type="submit" value="Register">
    </form>
<?php
    $pdo = new PDO('mysql:dbname=grupp6;host=localhost', 'wwwit-utv', 'Pangolin!24');
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    
    if(isset($_POST['username'])){
        $querystring = "INSERT INTO logindetails (username, password) VALUES (:username, :password)";
        $stmt = $pdo->prepare($querystring);
        $stmt->bindParam(':username', $_POST['username']);
        $stmt->bindParam(':password', ($_POST['password']));
        $stmt->execute();
        echo "User registered successfully.";
    }
?>
</body>
</html>