<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
$utente = $_SESSION['utente'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
        .navbar a:hover { background: rgba(255,255,255,0.2); }
        .contenuto {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .benvenuto {
            font-size: 22px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .sottotitolo {
            color: #888;
            font-size: 14px;
            margin-bottom: 32px;
        }
        .griglia {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 28px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s;
            display: block;
        }
        .card:hover { transform: translateY(-3px); }
        .card .icona { font-size: 32px; margin-bottom: 12px; }
        .card .titolo {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 6px;
        }
        .card .descrizione {
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../assets/logo.jpg" style="height:40px;">
    <span>Ciao, <?= htmlspecialchars($utente['nome']) ?>!</span>
    <a href="../logout.php">Esci</a>
</div>

<div class="contenuto">
    <div class="benvenuto">Benvenuto, <?= htmlspecialchars($utente['nome']) ?>!</div>
    <div class="sottotitolo">Cosa vuoi gestire oggi?</div>

    <div class="griglia">
        <a class="card" href="clienti.php">
            <div class="icona">👤</div>
            <div class="titolo">Clienti</div>
            <div class="descrizione">Gestisci i clienti e i loro abbonamenti</div>
        </a>
        <a class="card" href="corsi.php">
            <div class="icona">🏋️</div>
            <div class="titolo">Corsi</div>
            <div class="descrizione">Visualizza e gestisci i corsi attivi</div>
        </a>
        <a class="card" href="lezioni.php">
            <div class="icona">📅</div>
            <div class="titolo">Lezioni</div>
            <div class="descrizione">Programma e gestisci le lezioni</div>
        </a>
        <a class="card" href="prenotazioni.php">
            <div class="icona">📋</div>
            <div class="titolo">Prenotazioni</div>
            <div class="descrizione">Vedi tutte le prenotazioni attive</div>
        </a>
        <a class="card" href="sale.php">
            <div class="icona">🏠</div>
            <div class="titolo">Sale</div>
            <div class="descrizione">Gestisci le sale e le attrezzature</div>
        </a>
        <a class="card" href="pagamenti.php">
            <div class="icona">💳</div>
            <div class="titolo">Pagamenti</div>
            <div class="descrizione">Storico pagamenti e incassi</div>
        </a>
    </div>
</div>

</body>
</html>
