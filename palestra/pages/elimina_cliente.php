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
        
        $pdo->beginTransaction();
        
        // Elimina prima i pagamenti degli abbonamenti
        $stmt = $pdo->prepare("
            DELETE PA FROM PAGAMENTO PA
            JOIN ABBONAMENTO A ON PA.id_abbonamento = A.id_abbonamento
            WHERE A.id_cliente = ?
        ");
        $stmt->execute([$id]);
        
        // Elimina le prenotazioni del cliente
        $stmt = $pdo->prepare("DELETE FROM PRENOTAZIONE WHERE id_cliente = ?");
        $stmt->execute([$id]);
        
        // Elimina le iscrizioni del cliente
        $stmt = $pdo->prepare("DELETE FROM ISCRIZIONE WHERE id_cliente = ?");
        $stmt->execute([$id]);
        
        // Elimina gli abbonamenti del cliente
        $stmt = $pdo->prepare("DELETE FROM ABBONAMENTO WHERE id_cliente = ?");
        $stmt->execute([$id]);
        
        // Elimina il cliente e la persona
        $stmt = $pdo->prepare("DELETE FROM PERSONA WHERE id_persona = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        
    } catch(Exception $e) {
        $pdo->rollBack();
        echo "Errore: " . $e->getMessage();
        exit;
    }
}

header("Location: clienti.php");
exit;
?>