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
$id_cliente = $_GET['id_cliente'] ?? null;

if(!$id) {
    header("Location: clienti.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM ABBONAMENTO WHERE id_abbonamento = ?");
$stmt->execute([$id]);
$abbonamento = $stmt->fetch();

if(!$abbonamento) {
    header("Location: clienti.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE ABBONAMENTO 
            SET data_inizio=?, data_fine=?, costo=?, tipo=?, stato=?, descrizione=?,
                rinnovo_automatico=?, bonus_mensile=?, bonus_annuale=?, pagamento_rateizzato=?
            WHERE id_abbonamento=?
        ");
        $stmt->execute([
            $_POST['data_inizio'],
            $_POST['data_fine'],
            $_POST['costo'],
            $_POST['tipo'],
            $_POST['stato'],
            $_POST['descrizione'],
            isset($_POST['rinnovo_automatico']) ? 1 : 0,
            $_POST['bonus_mensile'] ?: null,
            $_POST['bonus_annuale'] ?: null,
            isset($_POST['pagamento_rateizzato']) ? 1 : 0,
            $id
        ]);
        $successo = "Abbonamento aggiornato con successo!";

        $stmt = $pdo->prepare("SELECT * FROM ABBONAMENTO WHERE id_abbonamento = ?");
        $stmt->execute([$id]);
        $abbonamento = $stmt->fetch();

    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Abbonamento</title>
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
        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus { border-color: #4a90e2; }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #333;
        }
        .checkbox-row input { width: auto; }
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
        .codice-box {
            background: #f0f2f5;
            border-radius: 8px;
            padding: 10px 16px;
            font-family: monospace;
            font-size: 13px;
            color: #555;
            margin-bottom: 20px;
        }
        .opzioni-mensile, .opzioni-annuale { display: none; }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div style="display:flex; gap:12px;">
        <a href="abbonamenti.php?id_cliente=<?= $id_cliente ?>">← Abbonamenti</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>✏️ Modifica Abbonamento</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <div class="codice-box">📋 Codice: <?= htmlspecialchars($abbonamento['codice']) ?></div>

        <form method="POST">
            <div class="sezione">Tipo e costo</div>
            <div class="riga">
                <div class="campo">
                    <label>Tipo *</label>
                    <select name="tipo" required id="tipo" onchange="mostraOpzioni()">
                        <option value="mensile" <?= $abbonamento['tipo']=='mensile'?'selected':'' ?>>Mensile</option>
                        <option value="annuale" <?= $abbonamento['tipo']=='annuale'?'selected':'' ?>>Annuale</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Costo (€) *</label>
                    <input type="number" name="costo" required min="0" step="0.01" value="<?= $abbonamento['costo'] ?>">
                </div>
            </div>

            <div class="sezione">Date</div>
            <div class="riga">
                <div class="campo">
                    <label>Data inizio *</label>
                    <input type="date" name="data_inizio" required value="<?= $abbonamento['data_inizio'] ?>">
                </div>
                <div class="campo">
                    <label>Data fine *</label>
                    <input type="date" name="data_fine" required value="<?= $abbonamento['data_fine'] ?>">
                </div>
            </div>

            <div class="campo">
                <label>Stato *</label>
                <select name="stato" required>
                    <option value="attivo" <?= $abbonamento['stato']=='attivo'?'selected':'' ?>>Attivo</option>
                    <option value="scaduto" <?= $abbonamento['stato']=='scaduto'?'selected':'' ?>>Scaduto</option>
                    <option value="sospeso" <?= $abbonamento['stato']=='sospeso'?'selected':'' ?>>Sospeso</option>
                    <option value="annullato" <?= $abbonamento['stato']=='annullato'?'selected':'' ?>>Annullato</option>
                </select>
            </div>

            <div class="campo">
                <label>Descrizione</label>
                <textarea name="descrizione" rows="2"><?= htmlspecialchars($abbonamento['descrizione'] ?? '') ?></textarea>
            </div>

            <div class="sezione opzioni-mensile" id="sez-mensile">Opzioni mensile</div>
            <div class="opzioni-mensile" id="div-mensile">
                <div class="campo">
                    <div class="checkbox-row">
                        <input type="checkbox" name="rinnovo_automatico" id="rinnovo" <?= $abbonamento['rinnovo_automatico'] ? 'checked' : '' ?>>
                        <label for="rinnovo">Rinnovo automatico</label>
                    </div>
                </div>
                <div class="campo">
                    <label>Bonus mensile</label>
                    <input type="text" name="bonus_mensile" value="<?= htmlspecialchars($abbonamento['bonus_mensile'] ?? '') ?>">
                </div>
            </div>

            <div class="sezione opzioni-annuale" id="sez-annuale">Opzioni annuale</div>
            <div class="opzioni-annuale" id="div-annuale">
                <div class="campo">
                    <div class="checkbox-row">
                        <input type="checkbox" name="pagamento_rateizzato" id="rateizzato" <?= $abbonamento['pagamento_rateizzato'] ? 'checked' : '' ?>>
                        <label for="rateizzato">Pagamento rateizzato</label>
                    </div>
                </div>
                <div class="campo">
                    <label>Bonus annuale</label>
                    <input type="text" name="bonus_annuale" value="<?= htmlspecialchars($abbonamento['bonus_annuale'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn">Salva Modifiche</button>
        </form>
    </div>
</div>

<script>
function mostraOpzioni() {
    var tipo = document.getElementById('tipo').value;
    var mensile = document.querySelectorAll('.opzioni-mensile');
    var annuale = document.querySelectorAll('.opzioni-annuale');
    mensile.forEach(function(el) { el.style.display = tipo === 'mensile' ? 'block' : 'none'; });
    annuale.forEach(function(el) { el.style.display = tipo === 'annuale' ? 'block' : 'none'; });
}
mostraOpzioni();
</script>

</body>
</html>