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
    header("Location: lezioni.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM LEZIONE WHERE id_lezione = ?");
$stmt->execute([$id]);
$lezione = $stmt->fetch();

if(!$lezione) {
    header("Location: lezioni.php");
    exit;
}

$corsi = $pdo->query("SELECT id_corso, nome FROM CORSO WHERE stato = 'attivo' ORDER BY nome")->fetchAll();
$sale = $pdo->query("SELECT id_sala, nome FROM SALA WHERE stato = 'disponibile' ORDER BY nome")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE LEZIONE 
            SET id_corso=?, id_sala=?, data=?, ora_inizio=?, ora_fine=?, tipo_lezione=?, note=?, stato=?
            WHERE id_lezione=?
        ");
        $stmt->execute([
            $_POST['id_corso'],
            $_POST['id_sala'],
            $_POST['data'],
            $_POST['ora_inizio'],
            $_POST['ora_fine'],
            $_POST['tipo_lezione'],
            $_POST['note'],
            $_POST['stato'],
            $id
        ]);
        $successo = "Lezione aggiornata con successo!";

        $stmt = $pdo->prepare("SELECT * FROM LEZIONE WHERE id_lezione = ?");
        $stmt->execute([$id]);
        $lezione = $stmt->fetch();

    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Lezione</title>
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
        input:focus, select:focus, textarea:focus { border-color: #4a90e2; }
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
        <a href="lezioni.php">← Lezioni</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>✏️ Modifica Lezione</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Dettagli lezione</div>
            <div class="riga">
                <div class="campo">
                    <label>Corso *</label>
                    <select name="id_corso" required>
                        <?php foreach($corsi as $c): ?>
                        <option value="<?= $c['id_corso'] ?>" <?= $lezione['id_corso']==$c['id_corso']?'selected':'' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Sala *</label>
                    <select name="id_sala" required>
                        <?php foreach($sale as $s): ?>
                        <option value="<?= $s['id_sala'] ?>" <?= $lezione['id_sala']==$s['id_sala']?'selected':'' ?>>
                            <?= htmlspecialchars($s['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Data *</label>
                    <input type="date" name="data" required value="<?= $lezione['data'] ?>">
                </div>
                <div class="campo">
                    <label>Tipo lezione</label>
                    <input type="text" name="tipo_lezione" value="<?= htmlspecialchars($lezione['tipo_lezione']) ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Ora inizio *</label>
                    <input type="time" name="ora_inizio" required value="<?= substr($lezione['ora_inizio'],0,5) ?>">
                </div>
                <div class="campo">
                    <label>Ora fine *</label>
                    <input type="time" name="ora_fine" required value="<?= substr($lezione['ora_fine'],0,5) ?>">
                </div>
            </div>
            <div class="campo">
                <label>Stato *</label>
                <select name="stato" required>
                    <option value="programmata" <?= $lezione['stato']=='programmata'?'selected':'' ?>>Programmata</option>
                    <option value="in_corso" <?= $lezione['stato']=='in_corso'?'selected':'' ?>>In corso</option>
                    <option value="completata" <?= $lezione['stato']=='completata'?'selected':'' ?>>Completata</option>
                    <option value="annullata" <?= $lezione['stato']=='annullata'?'selected':'' ?>>Annullata</option>
                </select>
            </div>
            <div class="campo">
                <label>Note</label>
                <textarea name="note" rows="3"><?= htmlspecialchars($lezione['note']) ?></textarea>
            </div>

            <button type="submit" class="btn">Salva Modifiche</button>
        </form>
    </div>
</div>

</body>
</html>