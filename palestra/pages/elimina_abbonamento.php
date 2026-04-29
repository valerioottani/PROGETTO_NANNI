<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

$id_cliente = $_GET['id_cliente'] ?? null;

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM ABBONAMENTO WHERE id_abbonamento = ?");
        $stmt->execute([$_GET['id']]);
    } catch(Exception $e) {
        echo "Errore: " . $e->getMessage();
        exit;
    }
}

header("Location: abbonamenti.php?id_cliente=" . $id_cliente);
exit;
?>