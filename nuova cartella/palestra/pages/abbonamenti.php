<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$id_cliente = $_GET['id_cliente'] ?? null;
if(!$id_cliente) {
    header("Location: clienti.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT P.nome, P.cognome 
    FROM PERSONA P WHERE P.id_persona = ?
");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();

$abbonamenti = $pdo->prepare("
    SELECT * FROM ABBONAMENTO 
    WHERE id_cliente = ?
    ORDER BY data_inizio DESC
");
$abbonamenti->execute([$id_cliente]);
$abbonamenti = $abbonamenti->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Abbonamenti</title>
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
        .cliente-box {
            background: white;
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 24px;
            font-size: 15px;
            color: #1a1a2e;
        }
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
        .badge.attivo { background: #e8f5e9; color: #2e7d32; }
        .badge.scaduto { background: #ffebee; color: #c62828; }
        .badge.sospeso { background: #fff3e0; color: #e65100; }
        .badge.annullato { background: #f5f5f5; color: #999; }
        .badge.mensile { background: #e3f2fd; color: #1565c0; }
        .badge.annuale { background: #f3e5f5; color: #6a1b9a; }
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
        .codice { font-family: monospace; font-size: 12px; color: #555; }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div style="display:flex; gap:12px;">
        <a href="clienti.php">← Clienti</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <div class="intestazione">
        <h2>💳 Abbonamenti</h2>
        <a class="btn" href="nuovo_abbonamento.php?id_cliente=<?= $id_cliente ?>">+ Nuovo Abbonamento</a>
    </div>

    <div class="cliente-box">
        👤 <strong><?= htmlspecialchars($cliente['cognome'].' '.$cliente['nome']) ?></strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>Codice</th>
                <th>Tipo</th>
                <th>Inizio</th>
                <th>Fine</th>
                <th>Costo</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($abbonamenti)): ?>
            <tr>
                <td colspan="7" class="nessun-record">Nessun abbonamento ancora — aggiungine uno!</td>
            </tr>
            <?php else: ?>
            <?php foreach($abbonamenti as $a): ?>
            <tr>
                <td class="codice"><?= htmlspecialchars($a['codice']) ?></td>
                <td><span class="badge <?= $a['tipo'] ?>"><?= $a['tipo'] ?></span></td>
                <td><?= date('d/m/Y', strtotime($a['data_inizio'])) ?></td>
                <td><?= date('d/m/Y', strtotime($a['data_fine'])) ?></td>
                <td>€ <?= number_format($a['costo'], 2, ',', '.') ?></td>
                <td><span class="badge <?= $a['stato'] ?>"><?= $a['stato'] ?></span></td>
                <td>
                    <a class="btn-azione btn-modifica" href="modifica_abbonamento.php?id=<?= $a['id_abbonamento'] ?>&id_cliente=<?= $id_cliente ?>">✏️ Modifica</a>
                    <a class="btn-azione btn-elimina" href="elimina_abbonamento.php?id=<?= $a['id_abbonamento'] ?>&id_cliente=<?= $id_cliente ?>" onclick="return confirm('Sei sicuro?')">🗑️ Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>