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
            <th>Identitet</th>
            <th>Vårdorsak</th>
            <th>Diagnoser<th>
            <th>Undersökningar</th>
            <th>Behandlingar</th>
            <th>Information och beslut</th>
            <th>Avböjd vård (Ja/Nej)</th>
            <th>Antecknad av</th>
        </tr>
        <tr>
            <td>2025-11-20</td>
            <td>Mölndals Vårdcentral</td>
            <td>Doris Dorisson (2015-08-17)</td>
            <td>Ont i halsen</td>
            <td>Viral halsinfektion</td>
            <td>Informerad om behandling: beslut om egenvård</td>
            <td>Nej</td>
            <td>Dr. Karl Svensson</td>
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
