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
        $pdo = new PDO('mysql:dbname=grupp6;host=localhost', 'sqllab', 'Armadillo#2025');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare('SELECT * FROM logindetails WHERE username = :username');
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // print_r($users);
        if (isset($_POST['username'] == $user['username'] && $_POST['password'] == $user['password'])) {
            $dbUsername = $user['username'];
            $dbPassword = $user['password'];
        }
        // $username = $_POST['username'];
        // $password = $_POST['password'];
    }
    if ($username === $dbUsername && $password === $dbPassword) {
        $_SESSION['username'] = $username;
        echo "Login successful! Welcome, " . htmlspecialchars($username) . ".<br>";
    } else {
        echo "Invalid username or password.<br>";
        echo '<a href="login.php">Back to Login</a>';
    }
?>
</body>
</html>