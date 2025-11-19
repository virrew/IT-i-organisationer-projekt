<?php
session_start();

//   FETCH('http://193.93.250.83:8080/api/resource/Healthcare%20Practitioner?fields=[%22first_name%22,%20%22name%22]&filters=[[%22first_name%22,%22LIKE%22,%22%G6%%22]]', {
//     headers: {
//         'Authorization': '49faecfb2c53bd2:7fe935b2a6dbd0b'
//     }
//     })
//     .then(r => r.json())
//     .then(r => {
//     console.log(r);
//})


?>
<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Boka tid</title>
</head>
<body>

<h1>Bokningsformulär</h1>

<form method="post" action="process_booking.php">
  <label for="name">Namn:</label><br>
  <input type="text" id="name" name="name" required><br><br>
  
  <label for="email">E-post:</label><br>
  <input type="email" id="email" name="email" required><br><br>
  
  <label for="date">Datum för bokning:</label><br>
  <input type="date" id="date" name="date" required><br><br>
  
  <label for="time">Tid för bokning:</label><br>
  <input type="time" id="time" name="time" required><br><br>
  
  <input type="submit" value="Boka tid">


</body>
</html>