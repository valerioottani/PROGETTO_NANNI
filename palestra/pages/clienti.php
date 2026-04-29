<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$clienti = $pdo->query("
    SELECT P.id_persona, P.nome, P.cognome, P.email, P.telefono,
           C.stato_iscrizione, C.livello, C.certificato_medico_scadenza
    FROM PERSONA P
    JOIN CLIENTE C ON P.id_persona = C.id_persona
    ORDER BY P.cognome, P.nome
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Clienti</title>
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
            max-width: 1200px;
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
        .badge.attivo { background: #e8f5e9; color: #2e7d32; }
        .badge.sospeso { background: #fff3e0; color: #e65100; }
        .badge.scaduto { background: #ffebee; color: #c62828; }
        .nessun-cliente {
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
        .btn-abbonamento { background: #2e7d32; }
        .btn-elimina { background: #c62828; }
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
        <h2>👤 Clienti</h2>
        <a class="btn" href="nuovo_cliente.php">+ Nuovo Cliente</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Livello</th>
                <th>Stato</th>
                <th>Cert. Medico</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($clienti)): ?>
            <tr>
                <td colspan="7" class="nessun-cliente">Nessun cliente ancora!</td>
            </tr>
            <?php else: ?>
            <?php foreach($clienti as $c): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($c['cognome'].' '.$c['nome']) ?></strong>
                    <br><small style="color:#888"><?= htmlspecialchars($c['telefono']) ?></small>
                </td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['livello']) ?></td>
                <td>
                    <span class="badge <?= $c['stato_iscrizione'] ?>">
                        <?= $c['stato_iscrizione'] ?>
                    </span>
                </td>
                <td><?= $c['certificato_medico_scadenza'] ? date('d/m/Y', strtotime($c['certificato_medico_scadenza'])) : '—' ?></td>
                <td>
                    <a class="btn-azione btn-modifica" href="modifica_cliente.php?id=<?= $c['id_persona'] ?>">✏️ Modifica</a>
                    <a class="btn-azione btn-abbonamento" href="abbonamenti.php?id_cliente=<?= $c['id_persona'] ?>">💳 Abbonamenti</a>
                    <a class="btn-azione btn-elimina" href="elimina_cliente.php?id=<?= $c['id_persona'] ?>" onclick="return confirm('Sei sicuro?')">🗑️ Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>