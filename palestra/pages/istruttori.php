<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$istruttori = $pdo->query("
    SELECT P.id_persona, P.nome, P.cognome, P.email, P.telefono,
           I.tipo_contratto, I.stipendio, I.data_assunzione
    FROM PERSONA P
    JOIN ISTRUTTORE I ON P.id_persona = I.id_persona
    ORDER BY P.cognome, P.nome
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Istruttori</title>
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
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9f9f9; }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge.dipendente { background: #e8f5e9; color: #2e7d32; }
        .badge.collaboratore { background: #fff3e0; color: #e65100; }
        .badge.partita_iva { background: #e3f2fd; color: #1565c0; }
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
        <h2>🏃 Istruttori</h2>
        <a class="btn" href="nuovo_istruttore.php">+ Nuovo Istruttore</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Contratto</th>
                <th>Stipendio</th>
                <th>Data Assunzione</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($istruttori)): ?>
            <tr>
                <td colspan="7" class="nessun-record">Nessun istruttore ancora!</td>
            </tr>
            <?php else: ?>
            <?php foreach($istruttori as $i): ?>
            <tr>
                <td><strong><?= htmlspecialchars($i['cognome'].' '.$i['nome']) ?></strong></td>
                <td><?= htmlspecialchars($i['email']) ?></td>
                <td><?= htmlspecialchars($i['telefono']) ?></td>
                <td>
                    <span class="badge <?= $i['tipo_contratto'] ?>">
                        <?= $i['tipo_contratto'] ?>
                    </span>
                </td>
                <td>€ <?= number_format($i['stipendio'], 2, ',', '.') ?></td>
                <td><?= date('d/m/Y', strtotime($i['data_assunzione'])) ?></td>
                <td>
                    <a class="btn-modifica" href="modifica_istruttore.php?id=<?= $i['id_persona'] ?>">✏️ Modifica</a>
                    <a class="btn-elimina" href="elimina_istruttore.php?id=<?= $i['id_persona'] ?>" onclick="return confirm('Sei sicuro di voler eliminare questo istruttore?')">🗑️ Elimina</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>