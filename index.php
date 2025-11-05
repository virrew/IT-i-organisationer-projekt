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
         
            if(isset($username))  {
                if ($allowedUsers[$username]['password'] == $password) {
             /*   if(isset($allowedUser)[$user]['password'] == $password) {
                    echo "Användaren finns i listan!";
                } */
                    $_SESSION['username'] = $username;
                    $redirectpage = $allowedUsers[$username]['redirect'];
                    header("Location: $redirectpage");
                    exit;
                    }else {
                        echo "Invalid username or password";
            }
    }
}     
?>
</body>
</html>