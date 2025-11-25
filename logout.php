<?php
session_start();
session_unset();   
session_destroy(); // För att bryta sessionen 
header("Location: login.php"); 
exit;
?>

