<?php 
session_start();
// if (!isset($_SESSION['patient_namn'])) {
    // Om ingen är inloggad, skicka användaren till login
//    header("Location: login.php");
//    exit;
//} 
?> 
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Min journal</title>
</head>
<body>

<!-- Visar vem som är inloggad 
    <div>
        Inloggad som: <strong><//?php  echo $_SESSION['patient_namn']; ?></strong>
        | <a href="logout.php">Logga ut</a>
    </div>
    <hr>
-->
    <h1>Min journal</h1>

    <!-- Journal-tabell -->
    <h2>Journalanteckningar</h2>
    <table border="1">
        <tr>
            <th>Datum</th>
            <th>Vårdgivare</th>
            <th>Sammanfattning</th>
        </tr>
        <tr>
            <td>2025-11-20</td>
            <td>Mölndals Vårdcentral</td>
            <td>Kort anteckning om besöket...</td>
        </tr>
    </table>

    <!-- Provsvar-tabell -->
    <h2>Provsvar</h2>
    <table border="1">
        <tr>
            <th>Provnamn</th>
            <th>Datum</th>
            <th>Resultat</th>
            <th>Referensintervall</th>
        </tr>
        <tr>
            <td>Hemoglobin</td>
            <td>2025-11-18</td>
            <td>132 g/L</td>
            <td>120–155 g/L</td>
        </tr>
    </table>
