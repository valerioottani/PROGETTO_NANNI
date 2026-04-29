<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$errore = "";
$successo = "";

$abbonamenti = $pdo->query("
    SELECT A.id_abbonamento, A.codice, A.tipo,
           P.nome, P.cognome
    FROM ABBONAMENTO A
    JOIN CLIENTE C ON A.id_cliente = C.id_persona
    JOIN PERSONA P ON C.id_persona = P.id_persona
    WHERE A.stato = 'attivo'
    ORDER BY P.cognome, P.nome
")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO PAGAMENTO (id_abbonamento, importo, data_pagamento, metodo_pagamento, stato, codice_transazione)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['id_abbonamento'],
            $_POST['importo'],
            $_POST['data_pagamento'],
            $_POST['metodo_pagamento'],
            $_POST['stato'],
            $_POST['codice_transazione'] ?: null
        ]);
        $successo = "Pagamento registrato con successo!";
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Pagamento</title>
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
        .contenuto {
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
        }
        h2 { font-size: 22px; color: #1a1a2e; margin-bottom: 24px; }
        .form-box {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .riga {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .campo { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 6px;
        }
        input, select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus { border-color: #4a90e2; }
        .btn {
            background: #1a1a2e;
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover { background: #2d2d44; }
        .errore {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .successo {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .sezione {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a2e;
            margin: 20px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f0f2f5;
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div style="display:flex; gap:12px;">
        <a href="pagamenti.php">← Pagamenti</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>+ Nuovo Pagamento</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?> <a href="pagamenti.php">Torna ai pagamenti</a></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Dati pagamento</div>
            <div class="campo">
                <label>Abbonamento *</label>
                <select name="id_abbonamento" required>
                    <option value="">-- Seleziona abbonamento --</option>
                    <?php foreach($abbonamenti as $a): ?>
                    <option value="<?= $a['id_abbonamento'] ?>">
                        <?= htmlspecialchars($a['cognome'].' '.$a['nome']) ?> — 
                        <?= htmlspecialchars($a['codice']) ?> (<?= $a['tipo'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Importo (€) *</label>
                    <input type="number" name="importo" required min="0" step="0.01" placeholder="es. 50.00">
                </div>
                <div class="campo">
                    <label>Data pagamento *</label>
                    <input type="date" name="data_pagamento" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Metodo pagamento *</label>
                    <select name="metodo_pagamento" required>
                        <option value="contanti">Contanti</option>
                        <option value="carta">Carta</option>
                        <option value="bonifico">Bonifico</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato" required>
                        <option value="completato">Completato</option>
                        <option value="in_attesa">In attesa</option>
                        <option value="fallito">Fallito</option>
                        <option value="rimborsato">Rimborsato</option>
                    </select>
                </div>
            </div>
            <div class="campo">
                <label>Codice transazione</label>
                <input type="text" name="codice_transazione" placeholder="es. TRX123456 (opzionale)">
            </div>

            <button type="submit" class="btn">Salva Pagamento</button>
        </form>
    </div>
</div>

</body>
</html>
