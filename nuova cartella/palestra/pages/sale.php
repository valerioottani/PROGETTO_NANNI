<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$sale = $pdo->query("
    SELECT id_sala, nome, tipologia, capienza_max, 
           stato, data_ultima_manutenzione
    FROM SALA
    ORDER BY nome
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Sale</title>
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
        .badge.disponibile { background: #e8f5e9; color: #2e7d32; }
        .badge.in_manutenzione { background: #fff3e0; color: #e65100; }
        .badge.chiusa { background: #ffebee; color: #c62828; }
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
        <h2>🏠 Sale</h2>
        <a class="btn" href="nuova_sala.php">+ Nuova Sala</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipologia</th>
                <th>Capienza Max</th>
                <th>Ultima Manutenzione</th>
                <th>Stato</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($sale)): ?>
            <tr>
                <td colspan="5" class="nessun-record">Nessuna sala ancora — aggiungine una!</td>
            </tr>
            <?php else: ?>
            <?php foreach($sale as $s): ?>
            <tr>
                <td><strong><?= htmlspecialchars($s['nome']) ?></strong></td>
                <td><?= htmlspecialchars($s['tipologia']) ?></td>
                <td><?= $s['capienza_max'] ?> persone</td>
                <td><?= $s['data_ultima_manutenzione'] ?? '—' ?></td>
                <td>
                    <span class="badge <?= $s['stato'] ?>">
                        <?= $s['stato'] ?>
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
