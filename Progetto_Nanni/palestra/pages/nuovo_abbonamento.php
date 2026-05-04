<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$errore = "";
$successo = "";

$clienti = $pdo->query("
    SELECT C.id_persona, P.nome, P.cognome
    FROM CLIENTE C
    JOIN PERSONA P ON C.id_persona = P.id_persona
    ORDER BY P.cognome, P.nome
")->fetchAll();

$id_cliente_preselezionato = $_GET['id_cliente'] ?? null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $codice = 'ABB-' . strtoupper(uniqid());
        
        $stmt = $pdo->prepare("
            INSERT INTO ABBONAMENTO (id_cliente, codice, data_inizio, data_fine, costo, tipo, stato, descrizione, rinnovo_automatico, bonus_mensile, bonus_annuale, pagamento_rateizzato)
            VALUES (?, ?, ?, ?, ?, ?, 'attivo', ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['id_cliente'],
            $codice,
            $_POST['data_inizio'],
            $_POST['data_fine'],
            $_POST['costo'],
            $_POST['tipo'],
            $_POST['descrizione'],
            isset($_POST['rinnovo_automatico']) ? 1 : 0,
            $_POST['bonus_mensile'] ?: null,
            $_POST['bonus_annuale'] ?: null,
            isset($_POST['pagamento_rateizzato']) ? 1 : 0
        ]);
        $successo = "Abbonamento creato con successo! Codice: $codice";
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Abbonamento</title>
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
        .opzioni-mensile, .opzioni-annuale { display: none; }
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
    <h2>+ Nuovo Abbonamento</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?> <a href="clienti.php">Torna ai clienti</a></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Cliente e tipo</div>
            <div class="campo">
                <label>Cliente *</label>
                <select name="id_cliente" required>
                    <option value="">-- Seleziona cliente --</option>
                    <?php foreach($clienti as $c): ?>
                    <option value="<?= $c['id_persona'] ?>" <?= $id_cliente_preselezionato == $c['id_persona'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['cognome'].' '.$c['nome']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Tipo abbonamento *</label>
                    <select name="tipo" required id="tipo" onchange="mostraOpzioni()">
                        <option value="mensile">Mensile</option>
                        <option value="annuale">Annuale</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Costo (€) *</label>
                    <input type="number" name="costo" required min="0" step="0.01" placeholder="es. 50.00">
                </div>
            </div>

            <div class="sezione">Date</div>
            <div class="riga">
                <div class="campo">
                    <label>Data inizio *</label>
                    <input type="date" name="data_inizio" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="campo">
                    <label>Data fine *</label>
                    <input type="date" name="data_fine" required>
                </div>
            </div>

            <div class="campo">
                <label>Descrizione</label>
                <textarea name="descrizione" rows="2" placeholder="Note aggiuntive..."></textarea>
            </div>

            <div class="sezione opzioni-mensile" id="opzioni-mensile">Opzioni mensile</div>
            <div class="opzioni-mensile" id="div-mensile">
                <div class="campo">
                    <div class="checkbox-row">
                        <input type="checkbox" name="rinnovo_automatico" id="rinnovo">
                        <label for="rinnovo">Rinnovo automatico</label>
                    </div>
                </div>
                <div class="campo">
                    <label>Bonus mensile</label>
                    <input type="text" name="bonus_mensile" placeholder="es. Lezione gratuita...">
                </div>
            </div>

            <div class="sezione opzioni-annuale" id="opzioni-annuale">Opzioni annuale</div>
            <div class="opzioni-annuale" id="div-annuale">
                <div class="campo">
                    <div class="checkbox-row">
                        <input type="checkbox" name="pagamento_rateizzato" id="rateizzato">
                        <label for="rateizzato">Pagamento rateizzato</label>
                    </div>
                </div>
                <div class="campo">
                    <label>Bonus annuale</label>
                    <input type="text" name="bonus_annuale" placeholder="es. 2 mesi gratis...">
                </div>
            </div>

            <button type="submit" class="btn">Salva Abbonamento</button>
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