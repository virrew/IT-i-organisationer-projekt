<?php 
session_start();
// if (!isset($_SESSION['patient_namn'])) {
    // Om ingen är inloggad, skicka användaren till login
//    header("Location: login.php");
//    exit;
//} 

// Tillfällig data 
$journaler = [
    [
        "datum" => "2025-11-20",
        "vardgivare" => "Mölndals Vårdcentral",
        "identitet" => "Doris Dorisson (2015-08-17)",
        "vardorsak" => "Ont i halsen",
        "diangoser" => "Viral halsinfektion",
        "undersökning" => "Halsundersökning",
        "behandling" => "Egenvård",
        "info_beslut" => "Informerad om behandling: beslut om egenvård",
        "avbojd_vard" => "Nej",
        "antecknad_av" => "Dr. Karl Svensson"
    ]
]
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
            <th>Diagnoser</th>
            <th>Undersökningar</th>
            <th>Behandlingar</th>
            <th>Information och beslut</th>
            <th>Avböjd vård (Ja/Nej)</th>
            <th>Antecknad av</th>
        </tr>
        <?php foreach ($journaler as $journal): ?>
            <tr>
                <td><?php echo $journal['datum']; ?></td>
                <td><?php echo $journal['vardgivare']; ?></td>
                <td><?php echo $journal['identitet']; ?></td>
                <td><?php echo $journal['vardorsak']; ?></td>
                <td><?php echo $journal['diagnoser']; ?></td>
                <td><?php echo $journal['undersokning']; ?></td>
                <td><?php echo $journal['behandling']; ?></td>
                <td><?php echo $journal['info_beslut']; ?></td>
                <td><?php echo $journal['avbojd_vard']; ?></td>
                <td><?php echo $journal['antecknad_av']; ?></td>
            </tr>
        <?php endforeach; ?>
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
