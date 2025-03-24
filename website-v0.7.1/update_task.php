<?php
session_start();
include_once("connexion.php");

if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id_tache = $data['id_tache'] ?? 0;
$description = $data['description'] ?? '';
$priority = $data['priority'] ?? 'red';

try {
    $query = "
        UPDATE taches
        SET descripton = :description, tache_priorite = :priority
        WHERE id_tache = :id_tache
    ";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
    $stmt->bindParam(':id_tache', $id_tache, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}