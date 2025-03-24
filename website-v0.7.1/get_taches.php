<?php
session_start();
include_once("connexion.php");

if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

$id_projet = $_GET['id_projet'] ?? 0;

try {
    $query = "
        SELECT id_tache, id_projet, etat, descripton, tache_priorite
        FROM taches
        WHERE id_projet = :id_projet
    ";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($taches);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}