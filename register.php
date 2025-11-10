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
    <input type="submit" value="Register">
</form>

<?php
    $pdo = new PDO('mysql:dbname=grupp6;host=localhost', 'sqllab', 'Armadillo#2025');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $querystring = "INSERT INTO logindetails (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(':username', $_POST['username']);
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();
    echo "User registered successfully.";
}

?>
</body>
</html>