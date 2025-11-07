<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
</head>
<body>
    <h1>Welcome</h1>
<?php

    if(isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
         
            if(isset($username))  {
                if ($allowedUsers[$username]['password'] == $password) {
             /*   if(isset($allowedUser)[$user]['password'] == $password) {
                    echo "Användaren finns i listan!";
                } */
                    $_SESSION['username'] = $username;
                    $redirectpage = $allowedUsers[$username]['redirect'];
                    header("Location: $redirectpage");
                    exit;
                    }else {
                        echo "Invalid username or password";
            }
    }
}     
?>
<?php
// inlog.php - Welcoming page for a health clinic with login / create account
// GitHub Copilot

session_start();

// Simple SQLite connection (file will be created in the same folder)
$dbFile = __DIR__ . '/clinic_users.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create users table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Helper
function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$msg = '';
$err = '';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . basename(__FILE__));
    exit;
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $err = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Invalid email address.';
    } elseif ($password !== $password2) {
        $err = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([':name'=>$name, ':email'=>$email, ':password'=>$hash]);
            $msg = 'Account created. You can now log in.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $err = 'An account with that email already exists.';
            } else {
                $err = 'Database error.';
            }
        }
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $err = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];
            header('Location: ' . basename(__FILE__));
            exit;
        } else {
            $err = 'Invalid email or password.';
        }
    }
}

// If logged in, get user
$loggedIn = isset($_SESSION['user']);
$user = $loggedIn ? $_SESSION['user'] : null;
?>
<style>
    :root{--accent:#2a9d8f;--muted:#6c757d;--bg:#f8fafb;--card:#ffffff;}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; background:var(--bg); margin:0; color:#111;}
    .container{max-width:960px;margin:40px auto;padding:20px;}
    .header{display:flex;align-items:center;gap:16px;}
    .logo{width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,var(--accent),#264653);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;}
    .title{font-size:20px;font-weight:700;}
    .subtitle{color:var(--muted);font-size:14px;}
    .grid{display:grid;grid-template-columns:1fr 320px;gap:20px;margin-top:24px;}
    .card{background:var(--card);padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(20,20,20,0.06);}
    .clinic-info h2{margin:0 0 8px 0;}
    .clinic-info p{color:var(--muted);margin:0 0 12px 0;}
    .actions{display:flex;gap:8px;margin-top:12px;}
    .btn{display:inline-block;padding:10px 14px;border-radius:8px;background:var(--accent);color:#fff;text-decoration:none;border:none;cursor:pointer;}
    .btn.ghost{background:transparent;color:var(--accent);border:1px solid rgba(42,157,143,0.16);}
    form{display:flex;flex-direction:column;gap:8px;}
    input[type="text"],input[type="email"],input[type="password"]{padding:10px;border:1px solid #e6e9ee;border-radius:8px;}
    .muted{color:var(--muted);font-size:13px;}
    .msg{padding:10px;border-radius:8px;background:#e6fffa;color:#034d44;margin-bottom:10px;}
    .err{padding:10px;border-radius:8px;background:#fff1f2;color:#58111a;margin-bottom:10px;}
    .welcome{display:flex;align-items:center;justify-content:space-between;gap:12px;}
    .logout{background:#ef476f;color:#fff;border:none;padding:8px 10px;border-radius:8px;cursor:pointer;}
    @media (max-width:820px){ .grid{grid-template-columns:1fr; } }
</style>
<body>
<div class="container">
    <div class="header">
        <div class="logo">SH</div>
        <div>
            <div class="title">Sunrise Health Clinic</div>
            <div class="subtitle">Compassionate care. Convenient access.</div>
        </div>
    </div>

    <?php if ($loggedIn): ?>
        <div style="margin-top:18px;" class="card welcome">
            <div>
                <h2 style="margin:0">Welcome back, <?= h($user['name']) ?>!</h2>
                <div class="muted">Signed in as <?= h($user['email']) ?></div>
            </div>
            <div style="text-align:right">
                <div class="muted" style="font-size:13px;margin-bottom:8px">Book appointments, view records, and more.</div>
                <a class="btn" href="#">Book an appointment</a>
                <a class="logout" href="?action=logout">Log out</a>
            </div>
        </div>

        <div class="grid" style="margin-top:16px">
            <div class="card clinic-info">
                <h2>What we offer</h2>
                <p>General practice, preventive care, vaccinations, and telehealth visits. Our clinicians focus on personalized care for every patient.</p>
                <ul class="muted">
                    <li>Same-week appointments</li>
                    <li>Secure patient portal</li>
                    <li>Insurance & payment options</li>
                </ul>
            </div>
            <div class="card">
                <h3 style="margin-top:0">Quick actions</h3>
                <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
                    <a class="btn" href="#">View records</a>
                    <a class="btn ghost" href="#">Message your clinician</a>
                    <a class="btn ghost" href="#">Billing</a>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="grid">
            <div class="card">
                <h2>Welcome to Sunrise Health Clinic</h2>
                <p class="muted">Log in to manage appointments, view your health records, and communicate with your care team.</p>

                <?php if ($msg): ?><div class="msg"><?= h($msg) ?></div><?php endif; ?>
                <?php if ($err): ?><div class="err"><?= h($err) ?></div><?php endif; ?>

                <form method="post" style="margin-top:12px">
                    <label class="muted">Email</label>
                    <input type="email" name="email" required>
                    <label class="muted">Password</label>
                    <input type="password" name="password" required>
                    <button class="btn" name="login" type="submit" value="1">Log In</button>
                </form>

                <div style="margin-top:12px" class="muted">New to Sunrise? Create an account on the right.</div>
            </div>

            <div class="card">
                <h3 style="margin-top:0">Create an account</h3>
                <p class="muted" style="margin-bottom:12px">Quick sign up to access appointments and records.</p>
                <form method="post">
                    <label class="muted">Full name</label>
                    <input type="text" name="name" required>
                    <label class="muted">Email</label>
                    <input type="email" name="email" required>
                    <label class="muted">Password</label>
                    <input type="password" name="password" required>
                    <label class="muted">Confirm password</label>
                    <input type="password" name="password2" required>
                    <button class="btn" name="register" type="submit" value="1">Create account</button>
                </form>
                <div class="muted" style="margin-top:10px;font-size:13px">We store passwords securely. By creating an account you agree to our privacy policy.</div>
            </div>
        </div>
    <?php endif; ?>

    <footer style="margin-top:22px;text-align:center;color:var(--muted);font-size:13px">
        © <?= date('Y') ?> Sunrise Health Clinic — 123 Wellness Ave, YourCity
    </footer>
</div>
</body>
</html>