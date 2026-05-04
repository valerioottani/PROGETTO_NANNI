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

if(isset($_GET['annulla'])) {
    try {
        $stmt = $pdo->prepare("
            UPDATE PRENOTAZIONE SET stato='annullata'
            WHERE id_prenotazione = ? AND id_cliente = ?
        ");
        $stmt->execute([$_GET['annulla'], $utente['id_persona']]);
    } catch(Exception $e) {}
    header("Location: prenotazioni_cliente.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT PR.id_prenotazione, PR.stato, PR.presenza, PR.data_prenotazione,
           C.nome AS corso, L.data, L.ora_inizio, L.ora_fine, S.nome AS sala
    FROM PRENOTAZIONE PR
    JOIN LEZIONE L ON PR.id_lezione = L.id_lezione
    JOIN CORSO C ON L.id_corso = C.id_corso
    JOIN SALA S ON L.id_sala = S.id_sala
    WHERE PR.id_cliente = ?
    ORDER BY L.data DESC, L.ora_inizio DESC
");
$stmt->execute([$utente['id_persona']]);
$prenotazioni = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le mie prenotazioni</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .intestazione h2 { font-size: 22px; color: #1a1a2e; }
        table {
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            border-collapse: collapse;
            overflow: hidden;
        }
        th {
            background: #1a1a2e;
            color: white;
            padding: 14px 16px;
            text-align: left;
            font-size: 13px;
        }
        td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid #f0f2f5;
            color: #333;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9f9f9; }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge.confermata { background: #e8f5e9; color: #2e7d32; }
        .badge.in_attesa { background: #fff3e0; color: #e65100; }
        .badge.annullata { background: #ffebee; color: #c62828; }
        .badge.completata { background: #f3e5f5; color: #6a1b9a; }
        .presenza-si { color: #2e7d32; font-weight: bold; }
        .presenza-no { color: #888; }
        .btn-annulla {
            background: #c62828;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
        }
        .nessun-record {
            text-align: center;
            padding: 40px;
            color: #888;
            font-size: 15px;
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
        <h2>📋 Le mie prenotazioni</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Corso</th>
                <th>Data</th>
                <th>Orario</th>
                <th>Sala</th>
                <th>Presenza</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($prenotazioni)): ?>
            <tr>
                <td colspan="7" class="nessun-record">Nessuna prenotazione ancora!</td>
            </tr>
            <?php else: ?>
            <?php foreach($prenotazioni as $p): ?>
            <tr>
                <td><strong><?= htmlspecialchars($p['corso']) ?></strong></td>
                <td><?= date('d/m/Y', strtotime($p['data'])) ?></td>
                <td><?= substr($p['ora_inizio'],0,5) ?> - <?= substr($p['ora_fine'],0,5) ?></td>
                <td><?= htmlspecialchars($p['sala']) ?></td>
                <td>
                    <?php if($p['presenza']): ?>
                        <span class="presenza-si">✓ Presente</span>
                    <?php else: ?>
                        <span class="presenza-no">— Da confermare</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge <?= $p['stato'] ?>"><?= $p['stato'] ?></span>
                </td>
                <td>
                    <?php if($p['stato'] === 'confermata' && strtotime($p['data']) >= strtotime(date('Y-m-d'))): ?>
                    <a class="btn-annulla" href="prenotazioni_cliente.php?annulla=<?= $p['id_prenotazione'] ?>" onclick="return confirm('Vuoi annullare questa prenotazione?')">❌ Annulla</a>
                    <?php else: ?>
                    <span style="color:#ccc;font-size:12px;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>