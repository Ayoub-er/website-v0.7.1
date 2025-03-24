<?php
session_start();
include_once("connexion.php");

if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id_tache = $data['id_tache'] ?? 0;
$new_status = $data['new_status'] ?? 'todo';

try {
    $query = "
        UPDATE taches
        SET etat = :new_status
        WHERE id_tache = :id_tache
    ";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':new_status', $new_status, PDO::PARAM_STR);
    $stmt->bindParam(':id_tache', $id_tache, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}