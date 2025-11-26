<?php 
session_start(); 
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) { // För att skicka ut användare som inte har ett konto
    header('Location: login.php');
    exit;
}
?> 
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mölndalsvårdcentral – Mina sidor</title>

    <style>
        /* Globala färgvariabler */
        :root {
            --primary-blue: #1F6F78;
            --primary-blue-light: #C2EBE8;

            --mint-green: #E7FFF3;
            --accent-orange: #FCA06A;
            --info-blue: #0A5360;
            --warning-red: #D9534F;

            --white: #FFFFFF;
            --gray-light: #F5F5F5;
            --text-dark: #0E2A2C;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--gray-light);
            font-family: Arial, sans-serif;
            color: var(--text-dark);
        }

        /* NAVBAR */
        .navbar {
            background: var(--primary-blue);
            color: var(--white);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .nav-brand {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .nav-user {
            font-size: 0.95rem;
        }

        /* PAGE LAYOUT */
        h1 {
            text-align: center;
            margin-top: 40px;
            color: var(--primary-blue);
        }

        .page-container {
            max-width: 800px;
            margin: 20px auto 0;
            padding: 0 16px;
        }

        .welcome-card {
            margin-top: 20px;
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .welcome-card h2 {
            margin-top: 0;
            color: var(--text-dark);
        }

        .welcome-card p {
            margin-bottom: 16px;
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 18px;
            background: var(--primary-blue);
            color: var(--white);
            border-radius: 999px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            transition: 0.1s ease;
        }

        .btn-primary:hover {
            background: var(--info-blue);
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar">
    <div class="nav-brand">Mölndals Vårdcentral</div>
    <div class="nav-links">
      <a href="index.php">Hem</a>
      <a href="recept.php">Mina recept</a>
      <a href="bokningar.php">Mina bokningar</a>
      <a href="journal.php">Min journal</a>
      <a href="Kontakt.php">Kontakt</a>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
      <?= htmlspecialchars($_SESSION['username']) ?>
      <a href="logout.php">Logga ut</a>
      <?php else: ?>
      <a href="login.php">Logga in</a>
      <?php endif; ?>
    </div>
    </nav>

    <div class="page-container">
        <h1>Välkommen <?= htmlspecialchars($_SESSION['username']) ?></h1>

        <div class="welcome-card">
            <h2>Din översikt</h2>
            <p>
                Här kan du som patient se och hantera dina digitala tjänster hos
                Mölndals vårdcentral.
            </p>
        </div>
    </div>

<!-- http://193.93.250.83:8080/api/resource/Patient%20Appointment?fields=[%22*%22]&filters=[[%22patient%22,%20%22=%22,%20%22G5Torkeli%20Knipa%22]] -->
</body> 
</html>
