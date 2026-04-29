<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$pagamenti = $pdo->query("
    SELECT PA.id_pagamento, PA.importo, PA.data_pagamento, 
           PA.metodo_pagamento, PA.stato, PA.codice_transazione,
           P.nome, P.cognome,
           A.codice AS codice_abbonamento, A.tipo
    FROM PAGAMENTO PA
    JOIN ABBONAMENTO A ON PA.id_abbonamento = A.id_abbonamento
    JOIN CLIENTE C ON A.id_cliente = C.id_persona
    JOIN PERSONA P ON C.id_persona = P.id_persona
    ORDER BY PA.data_pagamento DESC
")->fetchAll();

$totale = array_sum(array_column($pagamenti, 'importo'));
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamenti</title>
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
        .riepilogo {
            background: white;
            border-radius: 12px;
            padding: 20px 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .riepilogo .icona { font-size: 32px; }
        .riepilogo .label { font-size: 13px; color: #888; }
        .riepilogo .valore { font-size: 28px; font-weight: bold; color: #1a1a2e; }
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
        .badge.completato { background: #e8f5e9; color: #2e7d32; }
        .badge.in_attesa { background: #fff3e0; color: #e65100; }
        .badge.fallito { background: #ffebee; color: #c62828; }
        .badge.rimborsato { background: #f3e5f5; color: #6a1b9a; }
        .importo { font-weight: bold; color: #2e7d32; }
        .nessun-record {
            text-align: center;
            padding: 40px;
            color: #888;
            font-size: 15px;
        }
        .btn-modifica {
            background: #1a1a2e;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-elimina {
            background: #c62828;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            margin-left: 4px;
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
        <h2>💳 Pagamenti</h2>
        <a class="btn" href="nuovo_pagamento.php">+ Nuovo Pagamento</a>
    </div>

    <div class="riepilogo">
        <div class="icona">💰</div>
        <div>
            <div class="label">Totale incassato</div>
            <div class="valore">€ <?= number_format($totale, 2, ',', '.') ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Abbonamento</th>
                <th>Importo</th>
                <th>Data</th>
                <th>Metodo</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($pagamenti)): ?>
            <tr>
                <td colspan="7" class="nessun-record">Nessun pagamento ancora!</td>
            </tr>
            <?php else: ?>
            <?php foreach($pagamenti as $p): ?>
            <tr>
                <td><strong><?= htmlspecialchars($p['cognome'].' '.$p['nome']) ?></strong></td>
                <td><?= htmlspecialchars($p['codice_abbonamento']) ?> (<?= $p['tipo'] ?>)</td>
                <td class="importo">€ <?= number_format($p['importo'], 2, ',', '.') ?></td>
                <td><?= date('d/m/Y', strtotime($p['data_pagamento'])) ?></td>
                <td><?= htmlspecialchars($p['metodo_pagamento']) ?></td>
                <td>
                    <span class="badge <?= $p['stato'] ?>">
                        <?= $p['stato'] ?>
                    </span>
                </td>
                <td>
                    <a class="btn-modifica" href="modifica_pagamento.php?id=<?= $p['id_pagamento'] ?>">✏️ Modifica</a>
                    <a class="btn-elimina" href="elimina_pagamento.php?id=<?= $p['id_pagamento'] ?>" onclick="return confirm('Sei sicuro di voler eliminare questo pagamento?')">🗑️ Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>