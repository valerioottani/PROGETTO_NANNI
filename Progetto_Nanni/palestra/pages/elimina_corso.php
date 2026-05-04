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
        $stmt = $pdo->prepare("DELETE FROM CORSO WHERE id_corso = ?");
        $stmt->execute([$id]);
    } catch(Exception $e) {
        echo "Errore: " . $e->getMessage();
        exit;
    }
}

header("Location: corsi.php");
exit;
?>