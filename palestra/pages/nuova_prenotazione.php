<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$errore = "";
$successo = "";

$clienti = $pdo->query("
    SELECT C.id_persona, P.nome, P.cognome 
    FROM CLIENTE C
    JOIN PERSONA P ON C.id_persona = P.id_persona
    ORDER BY P.cognome, P.nome
")->fetchAll();

$lezioni = $pdo->query("
    SELECT L.id_lezione, C.nome AS corso, L.data, L.ora_inizio, L.ora_fine
    FROM LEZIONE L
    JOIN CORSO C ON L.id_corso = C.id_corso
    WHERE L.data >= CURDATE() AND L.stato = 'programmata'
    ORDER BY L.data, L.ora_inizio
")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO PRENOTAZIONE (id_cliente, id_lezione, stato)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $_POST['id_cliente'],
            $_POST['id_lezione'],
            $_POST['stato']
        ]);
        $successo = "Prenotazione aggiunta con successo!";
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuova Prenotazione</title>
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
        .navbar h1 { font-size: 20px; }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            background: rgba(255,255,255,0.1);
            padding: 6px 16px;
            border-radius: 6px;
        }
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
        }
        .campo { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 6px;
        }
        select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        select:focus { border-color: #4a90e2; }
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
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .sezione {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a2e;
            margin: 20px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f0f2f5;
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div style="display:flex; gap:12px;">
        <a href="prenotazioni.php">← Prenotazioni</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>+ Nuova Prenotazione</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?> <a href="prenotazioni.php">Torna alle prenotazioni</a></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Dati prenotazione</div>
            <div class="campo">
                <label>Cliente *</label>
                <select name="id_cliente" required>
                    <option value="">-- Seleziona cliente --</option>
                    <?php foreach($clienti as $c): ?>
                    <option value="<?= $c['id_persona'] ?>">
                        <?= htmlspecialchars($c['cognome'].' '.$c['nome']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label>Lezione *</label>
                <select name="id_lezione" required>
                    <option value="">-- Seleziona lezione --</option>
                    <?php foreach($lezioni as $l): ?>
                    <option value="<?= $l['id_lezione'] ?>">
                        <?= htmlspecialchars($l['corso']) ?> — 
                        <?= date('d/m/Y', strtotime($l['data'])) ?> 
                        <?= substr($l['ora_inizio'],0,5) ?>-<?= substr($l['ora_fine'],0,5) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label>Stato *</label>
                <select name="stato" required>
                    <option value="confermata">Confermata</option>
                    <option value="in_attesa">In attesa</option>
                    <option value="annullata">Annullata</option>
                </select>
            </div>

            <button type="submit" class="btn">Salva Prenotazione</button>
        </form>
    </div>
</div>

</body>
</html>
