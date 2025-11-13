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
    <h1>Welcome</h1>
<?php
 if(isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $dbUsername = $username;
        $dbPassword = $password;

        echo "Attempting to log in user: " . htmlspecialchars($username) . "<br>";
        $pdo = new PDO('mysql:dbname=grupp6;host=localhost', 'sqllab', 'Armadillo#2025');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Hello from index.php";
    }
?>
</body>
</html>