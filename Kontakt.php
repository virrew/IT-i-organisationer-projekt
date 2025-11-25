
<!doctype html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kontaktsida</title>
</head>
<body>


<h1>Formulär för bemötand<h1>

<form method="post" action="kontakt.php">

  <input type="number" id="age" name="age">
            <label for="age">Hur gammal är du?</label><br>
            
            <input type="text" id="gender" name="gender">
            <label for="gender">Kön...</label><br>


   <fieldset>
            <legend>Allmänt om ditt besök på vårdcentralen</legend>
            
            
            <p>Fick du möjlighet att ställa frågorna du önskade?</p>
            <input type="radio" id="Yes" name="Quest" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="Quest" value="No">
            <label for="No">Nej</label><br>
            


            <p>Var det enkelt att ta till sig informationen under vårdmötet?</p>
            <input type="radio" id="Yes" name="info" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="info" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Är du nöjd med det sätt du kan komma i kontakt med vårdcentralen?</p>
            <input type="radio" id="Yes" name="nojd" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="nojd" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Fick du besöka vårdcentralen inom en rimlig tid?</p>
            <input type="radio" id="Yes" name="rimlig" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="rimlig" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Var väntan i väntrummet längre än 20 min?</p>
            <input type="radio" id="Yes" name="rum" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="rum" value="No">
            <label for="No">Nej</label><br>
        
        </fieldset>

         <fieldset>
            <legend>Information och kunskap</legend>
            
            
            <p>Fick du tillräckligt med information om din behandling och eventuella bieffekter?</p>
            <input type="radio" id="Yes" name="be" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="be" value="No">
            <label for="No">Nej</label><br>
            

            <p>Om du ställde frågor till vårdpersonalen fick du svar som du förstod?</p>
            <input type="radio" id="Yes" name="stod" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="stod" value="No">
            <label for="No">Nej</label><br>



            <p>Förklarade läkaren/sjuksköterskan/annan vårdpersonal behandlingen på ett sätt som du förstod?</p>
            <input type="radio" id="Yes" name="klar" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="klar" value="No">
            <label for="No">Nej</label><br>
            

            
            <p>Blev du informerade om ett kommande världsförlopp?</p>
            <input type="radio" id="Yes" name="kommande" value="Yes">
            <label for="Yes">Ja</label><br>
            
            <input type="radio" id="No" name="kommande" value="No">
            <label for="No">Nej</label><br>
        
        </fieldset>
        <label for="extra">Är det något från de ovannämnda frågorna som du specifikt vill utveckla? </label><br>
   <textarea maxlength="500" id="extra"></textarea>

  <input type="submit" value="Skicka in">

</body>
</html>