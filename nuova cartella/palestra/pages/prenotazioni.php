<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$prenotazioni = $pdo->query("
    SELECT PR.id_prenotazione, PR.data_prenotazione, PR.stato, PR.presenza,
           P.nome, P.cognome,
           C.nome AS corso,
           L.data AS data_lezione, L.ora_inizio, L.ora_fine
    FROM PRENOTAZIONE PR
    JOIN CLIENTE CL ON PR.id_cliente = CL.id_persona
    JOIN PERSONA P ON CL.id_persona = P.id_persona
    JOIN LEZIONE L ON PR.id_lezione = L.id_lezione
    JOIN CORSO C ON L.id_corso = C.id_corso
    ORDER BY L.data DESC, L.ora_inizio
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Prenotazioni</title>
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
        .presenza-no { color: #c62828; }
        .nessun-record {
            text-align: center;
            padding: 40px;
            color: #888;
            font-size: 15px;
        }
        .btn-azione {
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
            margin: 2px 2px 2px 0;
        }
        .btn-modifica { background: #1a1a2e; }
        .btn-elimina { background: #c62828; }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div style="display:flex; gap:12px;">
        <a href="dashboard.php">← Dashboard</a>
        
    </div>
</div>

<div class="contenuto">
    <div class="intestazione">
        <h2>📋 Prenotazioni</h2>
        <a class="btn" href="nuova_prenotazione.php">+ Nuova Prenotazione</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Corso</th>
                <th>Data Lezione</th>
                <th>Orario</th>
                <th>Prenotato il</th>
                <th>Presenza</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($prenotazioni)): ?>
            <tr>
                <td colspan="8" class="nessun-record">Nessuna prenotazione ancora!</td>
            </tr>
            <?php else: ?>
            <?php foreach($prenotazioni as $p): ?>
            <tr>
                <td><strong><?= htmlspecialchars($p['cognome'].' '.$p['nome']) ?></strong></td>
                <td><?= htmlspecialchars($p['corso']) ?></td>
                <td><?= date('d/m/Y', strtotime($p['data_lezione'])) ?></td>
                <td><?= substr($p['ora_inizio'],0,5) ?> - <?= substr($p['ora_fine'],0,5) ?></td>
                <td><?= date('d/m/Y', strtotime($p['data_prenotazione'])) ?></td>
                <td>
                    <?php if($p['presenza']): ?>
                        <span class="presenza-si">✓ Presente</span>
                    <?php else: ?>
                        <span class="presenza-no">✗ Da confermare</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge <?= $p['stato'] ?>">
                        <?= $p['stato'] ?>
                    </span>
                </td>
                <td>
                    <a class="btn-azione btn-modifica" href="modifica_prenotazione.php?id=<?= $p['id_prenotazione'] ?>">✏️ Modifica</a>
                    <a class="btn-azione btn-elimina" href="elimina_prenotazione.php?id=<?= $p['id_prenotazione'] ?>" onclick="return confirm('Sei sicuro?')">🗑️ Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:16px; background:white; border-radius:12px; padding:16px 24px; box-shadow:0 2px 8px rgba(0,0,0,0.07); font-size:13px; color:#555;">
        💡 <strong>Come funziona la presenza:</strong> Quando una lezione è completata, clicca <strong>✏️ Modifica</strong> sulla prenotazione e spunta la casella <strong>"Cliente presente"</strong> per registrare la presenza.
    </div>
</div>

</body>
</html>