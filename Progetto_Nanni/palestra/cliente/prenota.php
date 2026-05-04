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
$id_lezione = $_GET['id'] ?? null;

if(!$id_lezione) {
    header("Location: dashboard_cliente.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT L.*, C.nome AS corso, S.nome AS sala, S.capienza_max,
           COUNT(PR.id_prenotazione) AS prenotati
    FROM LEZIONE L
    JOIN CORSO C ON L.id_corso = C.id_corso
    JOIN SALA S ON L.id_sala = S.id_sala
    LEFT JOIN PRENOTAZIONE PR ON PR.id_lezione = L.id_lezione
        AND PR.stato IN ('confermata','in_attesa')
    WHERE L.id_lezione = ?
    GROUP BY L.id_lezione
");
$stmt->execute([$id_lezione]);
$lezione = $stmt->fetch();

if(!$lezione) {
    header("Location: dashboard_cliente.php");
    exit;
}

$errore = "";
$successo = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Controlla abbonamento attivo
        $stmt = $pdo->prepare("
            SELECT id_abbonamento FROM ABBONAMENTO
            WHERE id_cliente = ? AND stato = 'attivo' AND data_fine >= CURDATE()
            LIMIT 1
        ");
        $stmt->execute([$utente['id_persona']]);
        $abb = $stmt->fetch();

        if(!$abb) {
            $errore = "Non hai un abbonamento attivo! Contatta la reception.";
        } else {
            // Controlla se esiste già una prenotazione annullata
            $stmt = $pdo->prepare("
                SELECT id_prenotazione FROM PRENOTAZIONE
                WHERE id_cliente = ? AND id_lezione = ? AND stato = 'annullata'
            ");
            $stmt->execute([$utente['id_persona'], $id_lezione]);
            $esistente = $stmt->fetch();

            if($esistente) {
                // Riattiva la prenotazione annullata
                $stmt = $pdo->prepare("
                    UPDATE PRENOTAZIONE SET stato='confermata', data_prenotazione=NOW()
                    WHERE id_prenotazione = ?
                ");
                $stmt->execute([$esistente['id_prenotazione']]);
            } else {
                // Crea nuova prenotazione
                $stmt = $pdo->prepare("
                    INSERT INTO PRENOTAZIONE (id_cliente, id_lezione, stato)
                    VALUES (?, ?, 'confermata')
                ");
                $stmt->execute([$utente['id_persona'], $id_lezione]);
            }
            $successo = "Prenotazione confermata!";
        }
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Prenota Lezione</title>
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
        .contenuto {
            max-width: 600px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 36px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-align: center;
        }
        .icona { font-size: 48px; margin-bottom: 16px; }
        .corso { font-size: 24px; font-weight: bold; color: #1a1a2e; margin-bottom: 20px; }
        .info-box {
            background: #f0f2f5;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
            font-size: 14px;
            color: #333;
            line-height: 2;
        }
        .posti { font-size: 14px; color: #2e7d32; font-weight: bold; margin-bottom: 24px; }
        .btn {
            background: #1a1a2e;
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover { background: #2d2d44; }
        .btn-torna {
            display: block;
            text-align: center;
            margin-top: 14px;
            color: #888;
            font-size: 13px;
            text-decoration: none;
        }
        .errore {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .successo {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <a href="dashboard_cliente.php">← Torna alla dashboard</a>
</div>

<div class="contenuto">
    <div class="card">
        <div class="icona">🏋️</div>
        <div class="corso"><?= htmlspecialchars($lezione['corso']) ?></div>

        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo">✅ <?= $successo ?></div>
            <a class="btn-torna" href="dashboard_cliente.php">Torna alla dashboard</a>
        <?php else: ?>
        <div class="info-box">
            📅 <strong>Data:</strong> <?= date('d/m/Y', strtotime($lezione['data'])) ?><br>
            🕐 <strong>Orario:</strong> <?= substr($lezione['ora_inizio'],0,5) ?> - <?= substr($lezione['ora_fine'],0,5) ?><br>
            🏠 <strong>Sala:</strong> <?= htmlspecialchars($lezione['sala']) ?>
        </div>
        <div class="posti">✅ <?= $lezione['capienza_max'] - $lezione['prenotati'] ?> posti disponibili</div>
        <form method="POST">
            <button type="submit" class="btn">Conferma Prenotazione</button>
        </form>
        <a class="btn-torna" href="dashboard_cliente.php">Annulla</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>