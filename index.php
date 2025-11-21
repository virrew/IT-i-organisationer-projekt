<?php 
session_start(); 
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
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
    <h1>Welcome</h1> 

<!-- La till en navigationsbar för att testa att session fungerar till min receptsida-->
<nav style="background-color:#004466; color:white; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; font-family:sans-serif;">
    <div style="font-size:1.2rem; font-weight:bold;">
         Mölndals Vårdcentral
    </div>

    <div style="display:flex; align-items:center; gap:20px;">
        <a href="index.php" style="color:white; text-decoration:none;">Hem</a>
        <a href="recept.php" style="color:white; text-decoration:none;">Mina recept</a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <span>Välkommen <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" style="color:white; text-decoration:none; font-weight:bold;">Logga ut</a>
        <?php else: ?>
            <a href="login.php" style="color:white; text-decoration:none; font-weight:bold;">Logga in</a>
        <?php endif; ?>
    </div>
</nav>

<!-- http://193.93.250.83:8080/api/resource/Patient%20Appointment?fields=[%22*%22]&filters=[[%22patient%22,%20%22=%22,%20%22G5Torkeli%20Knipa%22]] -->
</body> 
</html>
