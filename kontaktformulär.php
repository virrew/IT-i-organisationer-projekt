<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Boka tid</title>
</head>
<body>

<h1>Boka tid hos oss ssk</h1>
<!-- Todo: Gör kontroll på maxord -->
<form method="post" action="boka.php">
  <input type="hidden" name="patientname" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
  <label for="field1">Ge en kort beskrivning av dina besvär <i> Max 150 ord</i></label><br>
  <input type="text" id="field1" name="field1" required><br><br>
  
  <label for="field2">Hur länge har du haft besvären?<i> Max 50 ord</i></label><br>
  <input type="text" id="field2" name="field2" required><br><br>
  
  <label for="field3">Har du sökt vård för detta tidigare? <i>Ja/nej, om ja vart?</i></label><br>
  <input type="text" id="field3" name="field3" required><br><br>
  <input type="submit" value="Boka tid">
</form>



</body>
</html>