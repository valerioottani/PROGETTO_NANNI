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

$corsi = $pdo->query("
    SELECT C.nome, C.descrizione, C.livello, C.durata_minuti, C.max_partecipanti, C.stato,
           COUNT(I.id_iscrizione) AS iscritti
    FROM CORSO C
    LEFT JOIN ISCRIZIONE I ON C.id_corso = I.id_corso AND I.stato = 'attiva'
    WHERE C.stato = 'attivo'
    GROUP BY C.id_corso
    ORDER BY C.nome
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Corsi disponibili</title>
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
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .intestazione {
            margin-bottom: 28px;
        }
        .intestazione h2 { font-size: 22px; color: #1a1a2e; }
        .intestazione p { font-size: 14px; color: #888; margin-top: 6px; }
        .griglia {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .card .nome {
            font-size: 17px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .card .descrizione {
            font-size: 13px;
            color: #888;
            margin-bottom: 16px;
            min-height: 36px;
        }
        .card .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: #555;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge.principiante { background: #e8f5e9; color: #2e7d32; }
        .badge.intermedio { background: #fff3e0; color: #e65100; }
        .badge.avanzato { background: #ffebee; color: #c62828; }
        .divider {
            border: none;
            border-top: 1px solid #f0f2f5;
            margin: 12px 0;
        }
        .stat {
            font-size: 12px;
            color: #aaa;
            text-align: center;
        }
        .nessun-record {
            text-align: center;
            padding: 60px;
            color: #888;
            font-size: 15px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div class="navbar-links">
        <a href="dashboard_cliente.php">🏠 Dashboard</a>
        
    </div>
</div>

<div class="contenuto">
    <div class="intestazione">
        <h2>🏋️ Corsi disponibili</h2>
        <p>Esplora tutti i corsi attivi della palestra</p>
    </div>

    <?php if(empty($corsi)): ?>
    <div class="nessun-record">Nessun corso disponibile al momento.</div>
    <?php else: ?>
    <div class="griglia">
        <?php foreach($corsi as $c): ?>
        <div class="card">
            <div class="nome"><?= htmlspecialchars($c['nome']) ?></div>
            <div class="descrizione"><?= htmlspecialchars($c['descrizione'] ?? '—') ?></div>
            <div class="info-row">
                <span>Livello</span>
                <span class="badge <?= $c['livello'] ?>"><?= $c['livello'] ?></span>
            </div>
            <div class="info-row">
                <span>⏱ Durata</span>
                <span><?= $c['durata_minuti'] ?> min</span>
            </div>
            <div class="info-row">
                <span>👥 Max partecipanti</span>
                <span><?= $c['max_partecipanti'] ?></span>
            </div>
            <hr class="divider">
            <div class="stat"><?= $c['iscritti'] ?> iscritti attivi</div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

</body>
</html>