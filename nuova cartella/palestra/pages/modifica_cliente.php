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
    header("Location: clienti.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT P.*, C.stato_iscrizione, C.data_iscrizione, 
           C.certificato_medico_scadenza, C.obiettivo, C.livello
    FROM PERSONA P
    JOIN CLIENTE C ON P.id_persona = C.id_persona
    WHERE P.id_persona = ?
");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if(!$cliente) {
    header("Location: clienti.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Controlla username duplicato
        $stmt = $pdo->prepare("SELECT id_persona FROM PERSONA WHERE username = ? AND id_persona != ?");
        $stmt->execute([$_POST['username'], $id]);
        if($stmt->fetch()) {
            throw new Exception("Username già in uso da un altro utente!");
        }

        if(!empty($_POST['nuova_password'])) {
            $hash = password_hash($_POST['nuova_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE PERSONA 
                SET username=?, nome=?, cognome=?, email=?, telefono=?, password=?
                WHERE id_persona=?
            ");
            $stmt->execute([
                $_POST['username'],
                $_POST['nome'],
                $_POST['cognome'],
                $_POST['email'],
                $_POST['telefono'],
                $hash,
                $id
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE PERSONA 
                SET username=?, nome=?, cognome=?, email=?, telefono=?
                WHERE id_persona=?
            ");
            $stmt->execute([
                $_POST['username'],
                $_POST['nome'],
                $_POST['cognome'],
                $_POST['email'],
                $_POST['telefono'],
                $id
            ]);
        }

        $stmt2 = $pdo->prepare("
            UPDATE CLIENTE 
            SET stato_iscrizione=?, certificato_medico_scadenza=?, obiettivo=?, livello=?
            WHERE id_persona=?
        ");
        $stmt2->execute([
            $_POST['stato_iscrizione'],
            $_POST['certificato_medico_scadenza'] ?: null,
            $_POST['obiettivo'],
            $_POST['livello'],
            $id
        ]);

        $pdo->commit();
        $successo = "Cliente aggiornato con successo!";

        $stmt = $pdo->prepare("
            SELECT P.*, C.stato_iscrizione, C.data_iscrizione,
                   C.certificato_medico_scadenza, C.obiettivo, C.livello
            FROM PERSONA P
            JOIN CLIENTE C ON P.id_persona = C.id_persona
            WHERE P.id_persona = ?
        ");
        $stmt->execute([$id]);
        $cliente = $stmt->fetch();

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
    <title>Modifica Cliente</title>
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
        <a href="clienti.php">← Clienti</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>✏️ Modifica Cliente</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Credenziali accesso</div>
            <div class="riga">
                <div class="campo">
                    <label>Username *</label>
                    <input type="text" name="username" required value="<?= htmlspecialchars($cliente['username']) ?>">
                </div>
                <div class="campo">
                    <label>Nuova password <small style="color:#aaa">(lascia vuoto per non cambiarla)</small></label>
                    <input type="password" name="nuova_password" placeholder="Nuova password...">
                </div>
            </div>

            <div class="sezione">Dati personali</div>
            <div class="riga">
                <div class="campo">
                    <label>Nome *</label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($cliente['nome']) ?>">
                </div>
                <div class="campo">
                    <label>Cognome *</label>
                    <input type="text" name="cognome" required value="<?= htmlspecialchars($cliente['cognome']) ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($cliente['email']) ?>">
                </div>
                <div class="campo">
                    <label>Telefono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($cliente['telefono']) ?>">
                </div>
            </div>

            <div class="sezione">Dati iscrizione</div>
            <div class="riga">
                <div class="campo">
                    <label>Stato iscrizione *</label>
                    <select name="stato_iscrizione" required>
                        <option value="attivo" <?= $cliente['stato_iscrizione']=='attivo'?'selected':'' ?>>Attivo</option>
                        <option value="sospeso" <?= $cliente['stato_iscrizione']=='sospeso'?'selected':'' ?>>Sospeso</option>
                        <option value="scaduto" <?= $cliente['stato_iscrizione']=='scaduto'?'selected':'' ?>>Scaduto</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Scadenza certificato medico</label>
                    <input type="date" name="certificato_medico_scadenza" value="<?= $cliente['certificato_medico_scadenza'] ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Livello *</label>
                    <select name="livello" required>
                        <option value="principiante" <?= $cliente['livello']=='principiante'?'selected':'' ?>>Principiante</option>
                        <option value="intermedio" <?= $cliente['livello']=='intermedio'?'selected':'' ?>>Intermedio</option>
                        <option value="avanzato" <?= $cliente['livello']=='avanzato'?'selected':'' ?>>Avanzato</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Obiettivo</label>
                    <input type="text" name="obiettivo" value="<?= htmlspecialchars($cliente['obiettivo'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn">Salva Modifiche</button>
        </form>
    </div>
</div>

</body>
</html>