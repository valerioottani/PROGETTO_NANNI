<?php
session_start();
if(isset($_SESSION['utente'])) {
    if($_SESSION['utente']['ruolo'] === 'admin') {
        header("Location: pages/dashboard.php");
    } else {
        header("Location: cliente/dashboard_cliente.php");
    }
    exit;
}

$errore = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "config/db.php";
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM PERSONA WHERE username = ?");
    $stmt->execute([$username]);
    $utente = $stmt->fetch();

    if($utente && password_verify($password, $utente['password'])) {
        $_SESSION['utente'] = $utente;
        if($utente['ruolo'] === 'admin') {
            header("Location: pages/dashboard.php");
        } else {
            header("Location: cliente/dashboard_cliente.php");
        }
        exit;
    } else {
        $errore = "Username o password errati";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>GymManager — Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            width: 360px;
        }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo img { height: 80px; }
        .sottotitolo {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 28px;
        }
        label {
            font-size: 13px;
            color: #555;
            display: block;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 18px;
            outline: none;
        }
        input:focus { border-color: #4a90e2; }
        button {
            width: 100%;
            padding: 12px;
            background: #1a1a2e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover { background: #2d2d44; }
        .errore {
            background: #ffe0e0;
            color: #c0392b;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="box">
    <div class="logo">
        <img src="assets/logo.jpg">
    </div>
    <p class="sottotitolo">Accedi al gestionale</p>
    <?php if($errore): ?>
        <div class="errore"><?= $errore ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Accedi</button>
    </form>
</div>
</body>
</html>