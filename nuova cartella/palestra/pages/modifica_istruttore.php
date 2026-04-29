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
    header("Location: istruttori.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT P.*, I.tipo_contratto, I.stipendio, I.data_assunzione
    FROM PERSONA P
    JOIN ISTRUTTORE I ON P.id_persona = I.id_persona
    WHERE P.id_persona = ?
");
$stmt->execute([$id]);
$istruttore = $stmt->fetch();

if(!$istruttore) {
    header("Location: istruttori.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE PERSONA SET nome=?, cognome=?, email=?, telefono=? WHERE id_persona=?");
        $stmt->execute([$_POST['nome'], $_POST['cognome'], $_POST['email'], $_POST['telefono'], $id]);

        $stmt2 = $pdo->prepare("UPDATE ISTRUTTORE SET tipo_contratto=?, stipendio=?, data_assunzione=? WHERE id_persona=?");
        $stmt2->execute([$_POST['tipo_contratto'], $_POST['stipendio'], $_POST['data_assunzione'], $id]);

        $pdo->commit();
        $successo = "Istruttore aggiornato con successo!";

        $stmt = $pdo->prepare("SELECT P.*, I.tipo_contratto, I.stipendio, I.data_assunzione FROM PERSONA P JOIN ISTRUTTORE I ON P.id_persona = I.id_persona WHERE P.id_persona = ?");
        $stmt->execute([$id]);
        $istruttore = $stmt->fetch();

    } catch(Exception $e) {
        $pdo->rollBack();
        $errore = "Errore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Istruttore</title>
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
        .riga { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .campo { margin-bottom: 18px; }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; }
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
        .errore { background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .successo { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .sezione { font-size: 13px; font-weight: bold; color: #1a1a2e; margin: 20px 0 12px; padding-bottom: 6px; border-bottom: 1px solid #f0f2f5; }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <a href="istruttori.php">← Istruttori</a>
</div>

<div class="contenuto">
    <h2>✏️ Modifica Istruttore</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Dati personali</div>
            <div class="riga">
                <div class="campo">
                    <label>Nome *</label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($istruttore['nome']) ?>">
                </div>
                <div class="campo">
                    <label>Cognome *</label>
                    <input type="text" name="cognome" required value="<?= htmlspecialchars($istruttore['cognome']) ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($istruttore['email']) ?>">
                </div>
                <div class="campo">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($istruttore['telefono']) ?>">
                </div>
            </div>

            <div class="sezione">Dati contrattuali</div>
            <div class="riga">
                <div class="campo">
                    <label>Tipo contratto *</label>
                    <select name="tipo_contratto" required>
                        <option value="dipendente" <?= $istruttore['tipo_contratto']=='dipendente'?'selected':'' ?>>Dipendente</option>
                        <option value="collaboratore" <?= $istruttore['tipo_contratto']=='collaboratore'?'selected':'' ?>>Collaboratore</option>
                        <option value="partita_iva" <?= $istruttore['tipo_contratto']=='partita_iva'?'selected':'' ?>>Partita IVA</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Stipendio (€) *</label>
                    <input type="number" name="stipendio" required min="0" step="0.01" value="<?= $istruttore['stipendio'] ?>">
                </div>
            </div>
            <div class="campo">
                <label>Data assunzione *</label>
                <input type="date" name="data_assunzione" required value="<?= $istruttore['data_assunzione'] ?>">
            </div>

            <button type="submit" class="btn">Salva Modifiche</button>
        </form>
    </div>
</div>

</body>
</html>