<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$errore = "";
$successo = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO SALA (nome, tipologia, capienza_max, stato, data_ultima_manutenzione)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['nome'],
            $_POST['tipologia'],
            $_POST['capienza_max'],
            $_POST['stato'],
            $_POST['data_ultima_manutenzione'] ?: null
        ]);
        $successo = "Sala aggiunta con successo!";
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuova Sala</title>
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
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <div style="display:flex; gap:12px;">
        <a href="sale.php">← Sale</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>+ Nuova Sala</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?> <a href="sale.php">Torna alle sale</a></div>
        <?php endif; ?>

        <form method="POST">
            <div class="campo">
                <label>Nome sala *</label>
                <input type="text" name="nome" required placeholder="es. Sala A, Sala Yoga...">
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Tipologia</label>
                    <input type="text" name="tipologia" placeholder="es. Pesi, Cardio, Danza...">
                </div>
                <div class="campo">
                    <label>Capienza massima *</label>
                    <input type="number" name="capienza_max" required min="1" placeholder="es. 20">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato" required>
                        <option value="disponibile">Disponibile</option>
                        <option value="in_manutenzione">In manutenzione</option>
                        <option value="chiusa">Chiusa</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Ultima manutenzione</label>
                    <input type="date" name="data_ultima_manutenzione">
                </div>
            </div>

            <button type="submit" class="btn">Salva Sala</button>
        </form>
    </div>
</div>

</body>
</html>
