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
$utente = $_SESSION['utente'];

require_once "../config/db.php";

// Abbonamento attivo
$stmt = $pdo->prepare("
    SELECT A.* FROM ABBONAMENTO A
    JOIN CLIENTE C ON A.id_cliente = C.id_persona
    WHERE C.id_persona = ?
    AND A.data_fine >= CURDATE()
    ORDER BY A.data_fine DESC LIMIT 1
");
$stmt->execute([$utente['id_persona']]);
$abbonamento = $stmt->fetch();

// Prossime prenotazioni
$stmt = $pdo->prepare("
    SELECT PR.*, C.nome AS corso, L.data, L.ora_inizio, L.ora_fine, S.nome AS sala
    FROM PRENOTAZIONE PR
    JOIN LEZIONE L ON PR.id_lezione = L.id_lezione
    JOIN CORSO C ON L.id_corso = C.id_corso
    JOIN SALA S ON L.id_sala = S.id_sala
    WHERE PR.id_cliente = ?
    AND L.data >= CURDATE()
    AND PR.stato = 'confermata'
    ORDER BY L.data, L.ora_inizio
    LIMIT 5
");
$stmt->execute([$utente['id_persona']]);
$prenotazioni = $stmt->fetchAll();

// Lezioni disponibili
$stmt = $pdo->prepare("
    SELECT L.id_lezione, C.nome AS corso, L.data, L.ora_inizio, L.ora_fine,
           S.nome AS sala, S.capienza_max,
           COUNT(PR.id_prenotazione) AS prenotati
    FROM LEZIONE L
    JOIN CORSO C ON L.id_corso = C.id_corso
    JOIN SALA S ON L.id_sala = S.id_sala
    LEFT JOIN PRENOTAZIONE PR ON PR.id_lezione = L.id_lezione
        AND PR.stato IN ('confermata','in_attesa')
    WHERE L.data >= CURDATE()
    AND L.stato = 'programmata'
    AND L.id_lezione NOT IN (
        SELECT id_lezione FROM PRENOTAZIONE 
        WHERE id_cliente = ? AND stato != 'annullata'
    )
    GROUP BY L.id_lezione
    HAVING prenotati < S.capienza_max
    ORDER BY L.data, L.ora_inizio
    LIMIT 6
");
$stmt->execute([$utente['id_persona']]);
$lezioni = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>GymManager — Area Cliente</title>
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
        .navbar a:hover { background: rgba(255,255,255,0.2); }
        .navbar-links { display: flex; gap: 10px; }
        .contenuto {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .benvenuto {
            font-size: 22px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 28px;
        }
        .griglia {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .card h3 {
            font-size: 16px;
            color: #1a1a2e;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f2f5;
        }
        .abb-box {
            background: #e8f5e9;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        .abb-box .tipo {
            font-size: 13px;
            color: #2e7d32;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .abb-box .codice {
            font-family: monospace;
            font-size: 12px;
            color: #555;
            margin-bottom: 8px;
        }
        .abb-box .scadenza { font-size: 13px; color: #333; }
        .nessun-abb {
            background: #ffebee;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            color: #c62828;
            font-size: 13px;
        }
        .prenotazione-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f2f5;
            font-size: 13px;
        }
        .prenotazione-item:last-child { border-bottom: none; }
        .prenotazione-item .corso-nome { font-weight: bold; color: #1a1a2e; }
        .prenotazione-item .dettagli { color: #888; font-size: 12px; margin-top: 2px; }
        .lezioni-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .lezione-card {
            background: white;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .lezione-card .corso {
            font-size: 15px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 6px;
        }
        .lezione-card .info { font-size: 12px; color: #888; margin-bottom: 4px; }
        .lezione-card .posti { font-size: 12px; color: #2e7d32; margin-bottom: 12px; }
        .btn-prenota {
            display: block;
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 8px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-prenota:hover { background: #2d2d44; }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 16px;
        }
        .nessun-record {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div class="navbar-links">
        <a href="corsi_cliente.php">🏋️ Corsi</a>
        <a href="prenotazioni_cliente.php">📋 Le mie prenotazioni</a>
        <a href="profilo_cliente.php">👤 Profilo</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <div class="benvenuto">Ciao, <?= htmlspecialchars($utente['nome']) ?>! 👋</div>

    <div class="griglia">
        <!-- ABBONAMENTO -->
        <div class="card">
            <h3>💳 Il mio abbonamento</h3>
            <?php if($abbonamento): ?>
            <div class="abb-box">
                <div class="tipo"><?= $abbonamento['tipo'] ?></div>
                <div class="codice"><?= htmlspecialchars($abbonamento['codice']) ?></div>
                <div class="scadenza">Scade il: <strong><?= date('d/m/Y', strtotime($abbonamento['data_fine'])) ?></strong></div>
            </div>
            <?php else: ?>
            <div class="nessun-abb">Nessun abbonamento attivo.<br>Contatta la reception!</div>
            <?php endif; ?>
        </div>

        <!-- PROSSIME PRENOTAZIONI -->
        <div class="card">
            <h3>📅 Prossime lezioni</h3>
            <?php if(empty($prenotazioni)): ?>
            <div class="nessun-record">Nessuna prenotazione — prenota una lezione!</div>
            <?php else: ?>
            <?php foreach($prenotazioni as $p): ?>
            <div class="prenotazione-item">
                <div class="corso-nome"><?= htmlspecialchars($p['corso']) ?></div>
                <div class="dettagli">
                    📅 <?= date('d/m/Y', strtotime($p['data'])) ?>
                    🕐 <?= substr($p['ora_inizio'],0,5) ?> - <?= substr($p['ora_fine'],0,5) ?>
                    🏠 <?= htmlspecialchars($p['sala']) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- LEZIONI DISPONIBILI -->
    <div class="section-title">🏋️ Lezioni disponibili — prenota ora!</div>
    <?php if(empty($lezioni)): ?>
    <div style="background:white;border-radius:12px;padding:30px;text-align:center;color:#888;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
        Nessuna lezione disponibile al momento.
    </div>
    <?php else: ?>
    <div class="lezioni-grid">
        <?php foreach($lezioni as $l): ?>
        <div class="lezione-card">
            <div class="corso"><?= htmlspecialchars($l['corso']) ?></div>
            <div class="info">📅 <?= date('d/m/Y', strtotime($l['data'])) ?></div>
            <div class="info">🕐 <?= substr($l['ora_inizio'],0,5) ?> - <?= substr($l['ora_fine'],0,5) ?></div>
            <div class="info">🏠 <?= htmlspecialchars($l['sala']) ?></div>
            <div class="posti">✅ <?= $l['capienza_max'] - $l['prenotati'] ?> posti liberi</div>
            <a class="btn-prenota" href="prenota.php?id=<?= $l['id_lezione'] ?>">Prenota</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

</body>
</html>