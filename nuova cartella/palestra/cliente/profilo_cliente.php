<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
if($_SESSION['utente']['ruolo'] === 'admin') {
    header("Location: ../pages/dashboard.php");
    exit;
}
require_once "../config/db.php";

$utente = $_SESSION['utente'];
$errore = "";
$successo = "";

$stmt = $pdo->prepare("
    SELECT P.*, C.livello, C.obiettivo, C.certificato_medico_scadenza, C.stato_iscrizione
    FROM PERSONA P
    JOIN CLIENTE C ON P.id_persona = C.id_persona
    WHERE P.id_persona = ?
");
$stmt->execute([$utente['id_persona']]);
$profilo = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Controlla se username è già usato da qualcun altro
        $stmt = $pdo->prepare("SELECT id_persona FROM PERSONA WHERE username = ? AND id_persona != ?");
        $stmt->execute([$_POST['username'], $utente['id_persona']]);
        if($stmt->fetch()) {
            throw new Exception("Username già in uso da un altro utente!");
        }

        if(!empty($_POST['nuova_password'])) {
            $hash = password_hash($_POST['nuova_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE PERSONA SET username=?, nome=?, cognome=?, email=?, telefono=?, password=? WHERE id_persona=?");
            $stmt->execute([$_POST['username'], $_POST['nome'], $_POST['cognome'], $_POST['email'], $_POST['telefono'], $hash, $utente['id_persona']]);
        } else {
            $stmt = $pdo->prepare("UPDATE PERSONA SET username=?, nome=?, cognome=?, email=?, telefono=? WHERE id_persona=?");
            $stmt->execute([$_POST['username'], $_POST['nome'], $_POST['cognome'], $_POST['email'], $_POST['telefono'], $utente['id_persona']]);
        }

        $stmt = $pdo->prepare("UPDATE CLIENTE SET obiettivo=? WHERE id_persona=?");
        $stmt->execute([$_POST['obiettivo'], $utente['id_persona']]);

        $pdo->commit();
        $successo = "Profilo aggiornato con successo!";

        $stmt = $pdo->prepare("
            SELECT P.*, C.livello, C.obiettivo, C.certificato_medico_scadenza, C.stato_iscrizione
            FROM PERSONA P
            JOIN CLIENTE C ON P.id_persona = C.id_persona
            WHERE P.id_persona = ?
        ");
        $stmt->execute([$utente['id_persona']]);
        $profilo = $stmt->fetch();
        $_SESSION['utente'] = array_merge($_SESSION['utente'], $profilo);

    } catch(Exception $e) {
        $pdo->rollBack();
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il mio profilo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .navbar {
            background: #1a1a2e;
            color: white;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            background: rgba(255,255,255,0.1);
            padding: 6px 16px;
            border-radius: 6px;
        }
        .navbar-links { display: flex; gap: 10px; }
        .contenuto {
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
        }
        h2 { font-size: 22px; color: #1a1a2e; margin-bottom: 24px; }
        .form-box {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 24px;
        }
        .info-box {
            background: #f0f2f5;
            border-radius: 8px;
            padding: 16px;
            font-size: 13px;
            color: #555;
            line-height: 2;
            margin-bottom: 24px;
        }
        .riga { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .campo { margin-bottom: 18px; }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; }
        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        input:focus { border-color: #4a90e2; }
        .btn {
            background: #1a1a2e;
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover { background: #2d2d44; }
        .errore { background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .successo { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .sezione { font-size: 13px; font-weight: bold; color: #1a1a2e; margin: 20px 0 12px; padding-bottom: 6px; border-bottom: 1px solid #f0f2f5; }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div class="navbar-links">
        <a href="dashboard_cliente.php">🏠 Dashboard</a>
        <a href="prenotazioni_cliente.php">📋 Prenotazioni</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>👤 Il mio profilo</h2>

    <div class="info-box">
        <strong>Livello:</strong> <?= htmlspecialchars($profilo['livello']) ?><br>
        <strong>Stato iscrizione:</strong> <?= htmlspecialchars($profilo['stato_iscrizione']) ?><br>
        <strong>Certificato medico scadenza:</strong> <?= $profilo['certificato_medico_scadenza'] ? date('d/m/Y', strtotime($profilo['certificato_medico_scadenza'])) : '—' ?>
    </div>

    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Credenziali accesso</div>
            <div class="campo">
                <label>Username *</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($profilo['username']) ?>">
            </div>

            <div class="sezione">Dati personali</div>
            <div class="riga">
                <div class="campo">
                    <label>Nome *</label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($profilo['nome']) ?>">
                </div>
                <div class="campo">
                    <label>Cognome *</label>
                    <input type="text" name="cognome" required value="<?= htmlspecialchars($profilo['cognome']) ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($profilo['email']) ?>">
                </div>
                <div class="campo">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($profilo['telefono']) ?>">
                </div>
            </div>
            <div class="campo">
                <label>Obiettivo</label>
                <input type="text" name="obiettivo" value="<?= htmlspecialchars($profilo['obiettivo'] ?? '') ?>" placeholder="es. dimagrire, tonificare...">
            </div>

            <div class="sezione">Cambia password</div>
            <div class="campo">
                <label>Nuova password <small style="color:#aaa">(lascia vuoto per non cambiarla)</small></label>
                <input type="password" name="nuova_password" placeholder="Nuova password...">
            </div>

            <button type="submit" class="btn">💾 Salva Modifiche</button>
        </form>
    </div>
</div>

</body>
</html>