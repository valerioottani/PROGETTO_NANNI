<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$lezioni = $pdo->query("
    SELECT L.id_lezione, L.data, L.ora_inizio, L.ora_fine,
           L.tipo_lezione, L.stato, L.note,
           C.nome AS corso, S.nome AS sala
    FROM LEZIONE L
    JOIN CORSO C ON L.id_corso = C.id_corso
    JOIN SALA S ON L.id_sala = S.id_sala
    ORDER BY L.data DESC, L.ora_inizio
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lezioni</title>
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
        .navbar a:hover { background: rgba(255,255,255,0.2); }
        .contenuto {
            max-width: 1100px;
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
        .btn {
            background: #1a1a2e;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn:hover { background: #2d2d44; }
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
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9f9f9; }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge.programmata { background: #e3f2fd; color: #1565c0; }
        .badge.in_corso { background: #e8f5e9; color: #2e7d32; }
        .badge.completata { background: #f3e5f5; color: #6a1b9a; }
        .badge.annullata { background: #ffebee; color: #c62828; }
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
    <div style="display:flex; gap:12px;">
        <a href="dashboard.php">← Dashboard</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <div class="intestazione">
        <h2>📅 Lezioni</h2>
        <a class="btn" href="nuova_lezione.php">+ Nuova Lezione</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Corso</th>
                <th>Sala</th>
                <th>Data</th>
                <th>Orario</th>
                <th>Tipo</th>
                <th>Stato</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($lezioni)): ?>
            <tr>
                <td colspan="6" class="nessun-record">Nessuna lezione ancora — aggiungine una!</td>
            </tr>
            <?php else: ?>
            <?php foreach($lezioni as $l): ?>
            <tr>
                <td><strong><?= htmlspecialchars($l['corso']) ?></strong></td>
                <td><?= htmlspecialchars($l['sala']) ?></td>
                <td><?= date('d/m/Y', strtotime($l['data'])) ?></td>
                <td><?= substr($l['ora_inizio'],0,5) ?> - <?= substr($l['ora_fine'],0,5) ?></td>
                <td><?= htmlspecialchars($l['tipo_lezione'] ?? '—') ?></td>
                <td>
                    <span class="badge <?= $l['stato'] ?>">
                        <?= $l['stato'] ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
