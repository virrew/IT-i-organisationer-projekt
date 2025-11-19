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
    $stmt = $pdo->prepare('SELECT username, password FROM logindetails WHERE username = :username AND password = :password'); 
    $stmt->execute([ 
        ':username' => $_POST['username'], 
        ':password' => $_POST['password']
    ]); 
    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
    if ($user) { 
        $_SESSION['username'] = $user['username']; 
        echo "Login successful! Welcome, " . htmlspecialchars($user['username']) . ".<br>"; 
    } else { 
        echo "Invalid username or password.<br>"; 
        echo '<a href="login.php">Back to Login</a>'; 
    } 
    print_r($users); 
    $username = $_POST['username']; 
    $password = $_POST['password']; 
    $dbUsername = $username; 
    $dbPassword = $password; 
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
