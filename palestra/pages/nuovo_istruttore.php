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
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO PERSONA (username, password, email, nome, cognome, telefono)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['username'],
            $password_hash,
            $_POST['email'],
            $_POST['nome'],
            $_POST['cognome'],
            $_POST['telefono']
        ]);
        
        $id = $pdo->lastInsertId();
        
        $stmt2 = $pdo->prepare("
            INSERT INTO ISTRUTTORE (id_persona, tipo_contratto, stipendio, data_assunzione)
            VALUES (?, ?, ?, ?)
        ");
        $stmt2->execute([
            $id,
            $_POST['tipo_contratto'],
            $_POST['stipendio'],
            $_POST['data_assunzione']
        ]);
        
        $pdo->commit();
        $successo = "Istruttore aggiunto con successo!";
        
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
    <title>Nuovo Istruttore</title>
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
        <a href="istruttori.php">← Istruttori</a>
        <a href="../logout.php">Esci</a>
    </div>
</div>

<div class="contenuto">
    <h2>+ Nuovo Istruttore</h2>
    <div class="form-box">
        <?php if($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>
        <?php if($successo): ?>
            <div class="successo"><?= $successo ?> <a href="istruttori.php">Torna agli istruttori</a></div>
        <?php endif; ?>

        <form method="POST">
            <div class="sezione">Dati personali</div>
            <div class="riga">
                <div class="campo">
                    <label>Nome *</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="campo">
                    <label>Cognome *</label>
                    <input type="text" name="cognome" required>
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="campo">
                    <label>Telefono</label>
                    <input type="text" name="telefono">
                </div>
            </div>

            <div class="sezione">Credenziali accesso</div>
            <div class="riga">
                <div class="campo">
                    <label>Username *</label>
                    <input type="text" name="username" required>
                </div>
                <div class="campo">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
            </div>

            <div class="sezione">Dati contrattuali</div>
            <div class="riga">
                <div class="campo">
                    <label>Tipo contratto *</label>
                    <select name="tipo_contratto" required>
                        <option value="dipendente">Dipendente</option>
                        <option value="collaboratore">Collaboratore</option>
                        <option value="partita_iva">Partita IVA</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Stipendio (€) *</label>
                    <input type="number" name="stipendio" required min="0" step="0.01" placeholder="es. 1500.00">
                </div>
            </div>
            <div class="campo">
                <label>Data assunzione *</label>
                <input type="date" name="data_assunzione" required value="<?= date('Y-m-d') ?>">
            </div>

            <button type="submit" class="btn">Salva Istruttore</button>
        </form>
    </div>
</div>

</body>
</html>