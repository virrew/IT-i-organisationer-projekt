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
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Hello from index.php<br>";

        // Verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['username'] = $username;                
                // Redirect to appropriate page
                header("Location: form.php");
                exit;
            } else {
                // Login failed
                echo "Invalid username or password";
            }
    }
?>
</body>
</html>