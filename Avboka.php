<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; 
$baseurl = 'http://193.93.250.83:8080/';

// LOGIN
try {
    $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24dilab@student.his.se", "pwd":"Dilyara123"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$response = json_decode($response, true);
curl_close($ch);

// Hämta användarnamn
if (isset($response['full_name'])) {
    $logged_in_user = $response['full_name'];
} elseif (isset($response['user'])) {
    $logged_in_user = $response['user'];
} elseif (isset($response['message'])) {
    $logged_in_user = $response['message'];
} else {
    $logged_in_user = '';
}

// GET-param för bokningens ID
$appointment_id = $_GET['id'] ?? '';

// DELETE (Avboka)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancel_id"])) {
    $cancel_id = $_POST["cancel_id"];

    $ch = curl_init($baseurl . "api/resource/Patient%20Appointment/" . $cancel_id);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    $success_msg = "Bokningen <strong>$cancel_id</strong> har tagits bort.";
}

// Hämta endast den specifika bokningen
$data = [];
if ($appointment_id) {
    $fields = [
        "name",
        "appointment_type",
        "appointment_date",
        "appointment_time",
        "status",
        "practitioner"
    ];

    $filters = [
        ["name", "=", $appointment_id]
    ];

    $url = $baseurl . '/api/resource/Patient%20Appointment?' .
        'fields=' . urlencode(json_encode($fields)) . '&' .
        'filters=' . urlencode(json_encode($filters));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);
}
?>

<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Avboka tid</title>
  <style>
    :root {
        --primary-blue: #1F6F78;
        --primary-blue-light: #C2EBE8;
        --mint-green: #E7FFF3;
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

    header {
        background: var(--primary-blue);
        padding: 20px;
        color: var(--white);
        text-align: center;
        font-size: 1.6rem;
        font-weight: bold;
    }

    .container {
        max-width: 850px;
        margin: 30px auto;
        background: var(--white);
        padding: 25px 35px;
        border-radius: 12px;
    }

    h2 {
        color: var(--primary-blue);
        border-bottom: 2px solid var(--primary-blue-light);
        padding-bottom: 8px;
        margin-bottom: 20px;
    }

    .booking {
        background: var(--mint-green);
        padding: 15px;
        margin-bottom: 12px;
        border-left: 6px solid var(--primary-blue);
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cancel-btn {
        background: var(--warning-red);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: bold;
    }

    .success-box {
        background: #8FD9C5;
        border-left: 6px solid var(--primary-blue);
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: bold;
    }

    .warning-text {
        color: var(--warning-red);
        font-weight: bold;
        margin-bottom: 20px;
    }
  </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-brand">
            <a href="index.php" style="color: white; text-decoration: none;">
                Mölndals Vårdcentral
            </a>
        </div>

        <div class="nav-links">

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>

            <a href="journal.php">Min journal</a>
            <a href="recept.php">Mina recept</a>
            <a href="kontaktformulär.php">Boka tid här</a>
            <a href="Kontakt.php">Kontakt</a>

            <!-- Höger sida – användarnamn + logga ut -->
            <span class="nav-user"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logga ut</a>

        <?php else: ?>

            <a href="login.php">Logga in</a>

        <?php endif; ?>

        </div>
    </nav>
    
<header>Mina bokade tider</header>

<div class="container">

<?php
if (!empty($success_msg)) {
    echo '<div class="success-box">' . $success_msg . '</div>';
}
?>

<p class="warning-text">
    ⚠️ Avbokning inom 24 timmar – avgift kan tillkomma.
</p>

<h2>Bokning att avboka</h2>

<?php if (!empty($data['data'])): ?>
    <?php $app = $data['data'][0]; ?>
    <div class="booking">
        <div>
            <?= htmlspecialchars($app['appointment_date']) ?> – <?= htmlspecialchars($app['appointment_type']) ?>
        </div>
        <form method="post">
            <input type="hidden" name="cancel_id" value="<?= htmlspecialchars($app['name']) ?>">
            <button type="submit" class="cancel-btn">Avboka</button>
        </form>
    </div>
<?php else: ?>
    <p>Hittade ingen bokning.</p>
<?php endif; ?>

</div>
</body>
</html>
