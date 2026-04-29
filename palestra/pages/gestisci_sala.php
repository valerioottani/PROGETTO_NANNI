<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$id = $_GET['id'] ?? null;
if(!$id) {
    header("Location: sale.php");
    exit;
}

$errore = "";
$successo = "";

$stmt = $pdo->prepare("SELECT * FROM SALA WHERE id_sala = ?");
$stmt->execute([$id]);
$sala = $stmt->fetch();

if(!$sala) {
    header("Location: sale.php");
    exit;
}

// Aggiorna dati sala
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salva_sala'])) {
    try {
        $stmt = $pdo->prepare("
            UPDATE SALA 
            SET nome=?, tipologia=?, capienza_max=?, stato=?, data_ultima_manutenzione=?
            WHERE id_sala=?
        ");
        $stmt->execute([
            $_POST['nome'],
            $_POST['tipologia'],
            $_POST['capienza_max'],
            $_POST['stato'],
            $_POST['data_ultima_manutenzione'] ?: null,
            $id
        ]);
        $successo = "Sala aggiornata con successo!";
        $stmt = $pdo->prepare("SELECT * FROM SALA WHERE id_sala = ?");
        $stmt->execute([$id]);
        $sala = $stmt->fetch();
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}

// Aggiungi attrezzatura
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi_attrezzatura'])) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("
            INSERT INTO ATTREZZATURA (cod_inventario, nome, marca, modello, tipologia, data_acquisto, descrizione, stato)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['cod_inventario'],
            $_POST['nome_att'],
            $_POST['marca'],
            $_POST['modello'],
            $_POST['tipologia_att'],
            $_POST['data_acquisto'] ?: null,
            $_POST['descrizione_att'],
            $_POST['stato_att']
        ]);
        $id_att = $pdo->lastInsertId();
        $stmt2 = $pdo->prepare("INSERT INTO CONTIENE (id_sala, id_attrezzatura, quantita) VALUES (?, ?, ?)");
        $stmt2->execute([$id, $id_att, $_POST['quantita']]);
        $pdo->commit();
        $successo = "Attrezzatura aggiunta con successo!";
    } catch(Exception $e) {
        $pdo->rollBack();
        $errore = "Errore: " . $e->getMessage();
    }
}

// Modifica attrezzatura
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifica_attrezzatura'])) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("
            UPDATE ATTREZZATURA 
            SET nome=?, marca=?, modello=?, tipologia=?, data_acquisto=?, descrizione=?, stato=?
            WHERE id_attrezzatura=?
        ");
        $stmt->execute([
            $_POST['nome_att_mod'],
            $_POST['marca_mod'],
            $_POST['modello_mod'],
            $_POST['tipologia_att_mod'],
            $_POST['data_acquisto_mod'] ?: null,
            $_POST['descrizione_att_mod'],
            $_POST['stato_att_mod'],
            $_POST['id_attrezzatura']
        ]);
        $stmt2 = $pdo->prepare("UPDATE CONTIENE SET quantita=? WHERE id_sala=? AND id_attrezzatura=?");
        $stmt2->execute([$_POST['quantita_mod'], $id, $_POST['id_attrezzatura']]);
        $pdo->commit();
        $successo = "Attrezzatura modificata con successo!";
    } catch(Exception $e) {
        $pdo->rollBack();
        $errore = "Errore: " . $e->getMessage();
    }
}

// Elimina attrezzatura dalla sala
if(isset($_GET['elimina_att'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM CONTIENE WHERE id_sala=? AND id_attrezzatura=?");
        $stmt->execute([$id, $_GET['elimina_att']]);
        $stmt2 = $pdo->prepare("DELETE FROM ATTREZZATURA WHERE id_attrezzatura=?");
        $stmt2->execute([$_GET['elimina_att']]);
        $successo = "Attrezzatura eliminata!";
    } catch(Exception $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}

// Carica attrezzature della sala
$attrezzature = $pdo->prepare("
    SELECT A.*, C.quantita
    FROM ATTREZZATURA A
    JOIN CONTIENE C ON A.id_attrezzatura = C.id_attrezzatura
    WHERE C.id_sala = ?
    ORDER BY A.nome
");
$attrezzature->execute([$id]);
$attrezzature = $attrezzature->fetchAll();

// Attrezzatura da modificare
$att_modifica = null;
if(isset($_GET['modifica_att'])) {
    $stmt = $pdo->prepare("
        SELECT A.*, C.quantita 
        FROM ATTREZZATURA A
        JOIN CONTIENE C ON A.id_attrezzatura = C.id_attrezzatura
        WHERE A.id_attrezzatura = ? AND C.id_sala = ?
    ");
    $stmt->execute([$_GET['modifica_att'], $id]);
    $att_modifica = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestisci Sala</title>
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
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        h2 { font-size: 22px; color: #1a1a2e; margin-bottom: 24px; }
        h3 { font-size: 17px; color: #1a1a2e; margin-bottom: 16px; }
        .form-box {
            background: white;
            border-radius: 12px;
            padding: 28px 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 28px;
        }
        .riga { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .riga3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .campo { margin-bottom: 16px; }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus { border-color: #4a90e2; }
        .btn { background: #1a1a2e; color: white; padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; }
        .btn:hover { background: #2d2d44; }
        .btn-verde { background: #2e7d32; color: white; padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; width: 100%; margin-top: 8px; }
        .btn-verde:hover { background: #1b5e20; }
        .btn-arancio { background: #e65100; color: white; padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; width: 100%; margin-top: 8px; }
        .btn-arancio:hover { background: #bf360c; }
        .errore { background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .successo { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a1a2e; color: white; padding: 12px 14px; text-align: left; font-size: 13px; }
        td { padding: 10px 14px; font-size: 13px; border-bottom: 1px solid #f0f2f5; color: #333; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9f9f9; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge.funzionante { background: #e8f5e9; color: #2e7d32; }
        .badge.in_riparazione { background: #fff3e0; color: #e65100; }
        .badge.dismessa { background: #ffebee; color: #c62828; }
        .btn-azione { color: white; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; display: inline-block; margin: 2px 2px 2px 0; }
        .btn-modifica-att { background: #1a1a2e; }
        .btn-elimina-att { background: #c62828; }
        .nessun-record { text-align: center; padding: 30px; color: #888; font-size: 14px; }
        .modifica-box { background: #fff8e1; border: 1.5px solid #f9a825; border-radius: 12px; padding: 28px 32px; margin-bottom: 28px; }
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
    <h2>🔧 Gestisci Sala — <?= htmlspecialchars($sala['nome']) ?></h2>

    <?php if($errore): ?>
        <div class="errore"><?= $errore ?></div>
    <?php endif; ?>
    <?php if($successo): ?>
        <div class="successo"><?= $successo ?></div>
    <?php endif; ?>

    <!-- MODIFICA SALA -->
    <div class="form-box">
        <h3>📝 Dati sala</h3>
        <form method="POST">
            <div class="riga">
                <div class="campo">
                    <label>Nome sala *</label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($sala['nome']) ?>">
                </div>
                <div class="campo">
                    <label>Tipologia</label>
                    <input type="text" name="tipologia" value="<?= htmlspecialchars($sala['tipologia']) ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Capienza massima *</label>
                    <input type="number" name="capienza_max" required min="1" value="<?= $sala['capienza_max'] ?>">
                </div>
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato" required>
                        <option value="disponibile" <?= $sala['stato']=='disponibile'?'selected':'' ?>>Disponibile</option>
                        <option value="in_manutenzione" <?= $sala['stato']=='in_manutenzione'?'selected':'' ?>>In manutenzione</option>
                        <option value="chiusa" <?= $sala['stato']=='chiusa'?'selected':'' ?>>Chiusa</option>
                    </select>
                </div>
            </div>
            <div class="campo">
                <label>Ultima manutenzione</label>
                <input type="date" name="data_ultima_manutenzione" value="<?= $sala['data_ultima_manutenzione'] ?>">
            </div>
            <button type="submit" name="salva_sala" class="btn">💾 Salva Modifiche Sala</button>
        </form>
    </div>

    <!-- MODIFICA ATTREZZATURA (appare solo quando si clicca Modifica) -->
    <?php if($att_modifica): ?>
    <div class="modifica-box">
        <h3>✏️ Modifica Attrezzatura — <?= htmlspecialchars($att_modifica['nome']) ?></h3>
        <form method="POST">
            <input type="hidden" name="id_attrezzatura" value="<?= $att_modifica['id_attrezzatura'] ?>">
            <div class="riga">
                <div class="campo">
                    <label>Nome *</label>
                    <input type="text" name="nome_att_mod" required value="<?= htmlspecialchars($att_modifica['nome']) ?>">
                </div>
                <div class="campo">
                    <label>Codice inventario</label>
                    <input type="text" value="<?= htmlspecialchars($att_modifica['cod_inventario']) ?>" disabled style="background:#f5f5f5">
                </div>
            </div>
            <div class="riga3">
                <div class="campo">
                    <label>Marca</label>
                    <input type="text" name="marca_mod" value="<?= htmlspecialchars($att_modifica['marca']) ?>">
                </div>
                <div class="campo">
                    <label>Modello</label>
                    <input type="text" name="modello_mod" value="<?= htmlspecialchars($att_modifica['modello']) ?>">
                </div>
                <div class="campo">
                    <label>Tipologia</label>
                    <input type="text" name="tipologia_att_mod" value="<?= htmlspecialchars($att_modifica['tipologia']) ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Data acquisto</label>
                    <input type="date" name="data_acquisto_mod" value="<?= $att_modifica['data_acquisto'] ?>">
                </div>
                <div class="campo">
                    <label>Quantità *</label>
                    <input type="number" name="quantita_mod" required min="1" value="<?= $att_modifica['quantita'] ?>">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato_att_mod" required>
                        <option value="funzionante" <?= $att_modifica['stato']=='funzionante'?'selected':'' ?>>Funzionante</option>
                        <option value="in_riparazione" <?= $att_modifica['stato']=='in_riparazione'?'selected':'' ?>>In riparazione</option>
                        <option value="dismessa" <?= $att_modifica['stato']=='dismessa'?'selected':'' ?>>Dismessa</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Descrizione</label>
                    <input type="text" name="descrizione_att_mod" value="<?= htmlspecialchars($att_modifica['descrizione'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" name="modifica_attrezzatura" class="btn-arancio">💾 Salva Modifiche Attrezzatura</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- ELENCO ATTREZZATURE -->
    <div class="form-box">
        <h3>🏋️ Attrezzature presenti</h3>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Marca/Modello</th>
                    <th>Cod. Inventario</th>
                    <th>Quantità</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($attrezzature)): ?>
                <tr>
                    <td colspan="6" class="nessun-record">Nessuna attrezzatura — aggiungine una!</td>
                </tr>
                <?php else: ?>
                <?php foreach($attrezzature as $a): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($a['nome']) ?></strong><br><small style="color:#888"><?= htmlspecialchars($a['tipologia']) ?></small></td>
                    <td><?= htmlspecialchars($a['marca'].' '.$a['modello']) ?></td>
                    <td style="font-family:monospace"><?= htmlspecialchars($a['cod_inventario']) ?></td>
                    <td><?= $a['quantita'] ?></td>
                    <td><span class="badge <?= $a['stato'] ?>"><?= $a['stato'] ?></span></td>
                    <td>
                        <a class="btn-azione btn-modifica-att" href="gestisci_sala.php?id=<?= $id ?>&modifica_att=<?= $a['id_attrezzatura'] ?>">✏️ Modifica</a>
                        <a class="btn-azione btn-elimina-att" href="gestisci_sala.php?id=<?= $id ?>&elimina_att=<?= $a['id_attrezzatura'] ?>" onclick="return confirm('Eliminare questa attrezzatura?')">🗑️ Elimina</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- AGGIUNGI ATTREZZATURA -->
    <div class="form-box">
        <h3>➕ Aggiungi Attrezzatura</h3>
        <form method="POST">
            <div class="riga">
                <div class="campo">
                    <label>Nome *</label>
                    <input type="text" name="nome_att" required placeholder="es. Tapis Roulant">
                </div>
                <div class="campo">
                    <label>Codice inventario *</label>
                    <input type="text" name="cod_inventario" required placeholder="es. ATT-001">
                </div>
            </div>
            <div class="riga3">
                <div class="campo">
                    <label>Marca</label>
                    <input type="text" name="marca" placeholder="es. Technogym">
                </div>
                <div class="campo">
                    <label>Modello</label>
                    <input type="text" name="modello" placeholder="es. Run 700">
                </div>
                <div class="campo">
                    <label>Tipologia</label>
                    <input type="text" name="tipologia_att" placeholder="es. Cardio">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Data acquisto</label>
                    <input type="date" name="data_acquisto">
                </div>
                <div class="campo">
                    <label>Quantità *</label>
                    <input type="number" name="quantita" required min="1" value="1">
                </div>
            </div>
            <div class="riga">
                <div class="campo">
                    <label>Stato *</label>
                    <select name="stato_att" required>
                        <option value="funzionante">Funzionante</option>
                        <option value="in_riparazione">In riparazione</option>
                        <option value="dismessa">Dismessa</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Descrizione</label>
                    <input type="text" name="descrizione_att" placeholder="Note aggiuntive...">
                </div>
            </div>
            <button type="submit" name="aggiungi_attrezzatura" class="btn-verde">➕ Aggiungi Attrezzatura</button>
        </form>
    </div>
</div>

</body>
</html>