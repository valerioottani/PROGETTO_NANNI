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
    header("Location: corsi.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM CORSO WHERE id_corso = ?");
$stmt->execute([$id]);
$corso = $stmt->fetch();

if(!$corso) {
    header("Location: corsi.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE CORSO 
            SET nome=?, descrizione=?, livello=?, durata_minuti=?, max_partecipanti=?, stato=?
            WHERE id_corso=?
        ");
        $stmt->execute([
            $_POST['nome'],
            $_POST['descrizione'],
            $_POST['livello'],
            $_POST['durata_minuti'],
            $_POST['max_partecipanti'],
            $_POST['stato'],
            $id
        ]);
        $successo = "Corso aggiornato con successo!";

        $stmt = $pdo->prepare("SELECT * FROM CORSO WHERE id_corso = ?");
        $stmt->execute([$id]);
        $corso = $stmt->fetch();

    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Corso</title>
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
        <a href="corsi.php">← Corsi</a>
        
    </div>
</div>

<div class="contenuto">
    <h2>✏️ Modifica Corso</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Dati corso</div>
            <div class="campo">
                <label>Nome corso *</label>
                <input type="text" name="nome" required value="<?= htmlspecialchars($corso['nome']) ?>">
            </div>
            <div class="campo">
                <label>Descrizione</label>
                <textarea name="descrizione" rows="3"><?= htmlspecialchars($corso['descrizione']) ?></textarea>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Livello *</label>
                    <select name="livello" required>
                        <option value="principiante" <?= $corso['livello']=='principiante'?'selected':'' ?>>Principiante</option>
                        <option value="intermedio" <?= $corso['livello']=='intermedio'?'selected':'' ?>>Intermedio</option>
                        <option value="avanzato" <?= $corso['livello']=='avanzato'?'selected':'' ?>>Avanzato</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato" required>
                        <option value="attivo" <?= $corso['stato']=='attivo'?'selected':'' ?>>Attivo</option>
                        <option value="sospeso" <?= $corso['stato']=='sospeso'?'selected':'' ?>>Sospeso</option>
                        <option value="terminato" <?= $corso['stato']=='terminato'?'selected':'' ?>>Terminato</option>
                    </select>
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Durata (minuti) *</label>
                    <input type="number" name="durata_minuti" required min="1" value="<?= $corso['durata_minuti'] ?>">
                </div>
                <div class="campo">
                    <label>Max partecipanti *</label>
                    <input type="number" name="max_partecipanti" required min="1" value="<?= $corso['max_partecipanti'] ?>">
                </div>
            </div>

            <button type="submit" class="btn">Salva Modifiche</button>
        </form>
    </div>
</div>

</body>
</html>