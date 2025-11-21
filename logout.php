<?php
session_start();
session_unset();   
session_destroy(); // För att logga bryta sessionen
header("Location: login.php"); 
exit;
?>

