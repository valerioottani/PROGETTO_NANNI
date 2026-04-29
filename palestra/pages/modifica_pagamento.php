<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$errore = "";
$successo = "";

$id = $_GET['id'] ?? null;
if(!$id) {
    header("Location: pagamenti.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM PAGAMENTO WHERE id_pagamento = ?");
$stmt->execute([$id]);
$pagamento = $stmt->fetch();

if(!$pagamento) {
    header("Location: pagamenti.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT P.nome, P.cognome, A.codice, A.tipo
    FROM PAGAMENTO PA
    JOIN ABBONAMENTO A ON PA.id_abbonamento = A.id_abbonamento
    JOIN CLIENTE C ON A.id_cliente = C.id_persona
    JOIN PERSONA P ON C.id_persona = P.id_persona
    WHERE PA.id_pagamento = ?
");
$stmt->execute([$id]);
$dettagli = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE PAGAMENTO 
            SET importo=?, data_pagamento=?, metodo_pagamento=?, stato=?, codice_transazione=?
            WHERE id_pagamento=?
        ");
        $stmt->execute([
            $_POST['importo'],
            $_POST['data_pagamento'],
            $_POST['metodo_pagamento'],
            $_POST['stato'],
            $_POST['codice_transazione'] ?: null,
            $id
        ]);
        $successo = "Pagamento aggiornato con successo!";

        $stmt = $pdo->prepare("SELECT * FROM PAGAMENTO WHERE id_pagamento = ?");
        $stmt->execute([$id]);
        $pagamento = $stmt->fetch();

    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Pagamento</title>
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
        .info-box {
            background: #f0f2f5;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #333;
            line-height: 1.8;
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
    <h2>✏️ Modifica Pagamento</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <div class="info-box">
            <strong>Cliente:</strong> <?= htmlspecialchars($dettagli['cognome'].' '.$dettagli['nome']) ?><br>
            <strong>Abbonamento:</strong> <?= htmlspecialchars($dettagli['codice']) ?> (<?= $dettagli['tipo'] ?>)
        </div>

        <form method="POST">
            <div class="sezione">Dati pagamento</div>
            <div class="riga">
                <div class="campo">
                    <label>Importo (€) *</label>
                    <input type="number" name="importo" required min="0" step="0.01" value="<?= $pagamento['importo'] ?>">
                </div>
                <div class="campo">
                    <label>Data pagamento *</label>
                    <input type="date" name="data_pagamento" required value="<?= $pagamento['data_pagamento'] ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Metodo pagamento *</label>
                    <select name="metodo_pagamento" required>
                        <option value="contanti" <?= $pagamento['metodo_pagamento']=='contanti'?'selected':'' ?>>Contanti</option>
                        <option value="carta" <?= $pagamento['metodo_pagamento']=='carta'?'selected':'' ?>>Carta</option>
                        <option value="bonifico" <?= $pagamento['metodo_pagamento']=='bonifico'?'selected':'' ?>>Bonifico</option>
                        <option value="paypal" <?= $pagamento['metodo_pagamento']=='paypal'?'selected':'' ?>>PayPal</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato" required>
                        <option value="completato" <?= $pagamento['stato']=='completato'?'selected':'' ?>>Completato</option>
                        <option value="in_attesa" <?= $pagamento['stato']=='in_attesa'?'selected':'' ?>>In attesa</option>
                        <option value="fallito" <?= $pagamento['stato']=='fallito'?'selected':'' ?>>Fallito</option>
                        <option value="rimborsato" <?= $pagamento['stato']=='rimborsato'?'selected':'' ?>>Rimborsato</option>
                    </select>
                </div>
            </div>
            <div class="campo">
                <label>Codice transazione</label>
                <input type="text" name="codice_transazione" value="<?= htmlspecialchars($pagamento['codice_transazione'] ?? '') ?>">
            </div>

            <button type="submit" class="btn">Salva Modifiche</button>
        </form>
    </div>
</div>

</body>
</html>