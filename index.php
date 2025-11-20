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
} 
?> 
<!-- http://193.93.250.83:8080/api/resource/Patient%20Appointment?fields=[%22*%22]&filters=[[%22patient%22,%20%22=%22,%20%22G5Torkeli%20Knipa%22]] -->
</body> 
</html>
