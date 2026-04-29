<?php
session_start();
if(!isset($_SESSION['utente'])) {
    header("Location: ../index.php");
    exit;
}
require_once "../config/db.php";

if(isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM PERSONA WHERE id_persona = ?");
        $stmt->execute([$id]);
    } catch(Exception $e) {
        echo "Errore: " . $e->getMessage();
        exit;
    }
}

header("Location: istruttori.php");
exit;
?>