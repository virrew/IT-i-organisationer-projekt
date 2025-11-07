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

        // Database connection
        $host = "localhost";  // or your database host
        $dbUsername = "root"; // your database username
        $dbPassword = "";     // your database password
        $dbName = "your_database_name"; // your database name

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbName", $dbUsername, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id'];
                
                // Redirect to appropriate page
                header("Location: welcome.php");
                exit;
            } else {
                // Login failed
                echo "Invalid username or password";
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $conn = null;
    }     
?>
</body>
</html>