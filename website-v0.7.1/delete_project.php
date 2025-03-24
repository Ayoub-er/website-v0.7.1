<?php
session_start();
include_once("connexion.php");

if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id_projet = $data['id_projet'] ?? 0;

try {
    // Commencer une transaction
    $cnx->beginTransaction();

    // Supprimer les tâches associées au projet
    $query = "DELETE FROM taches WHERE id_projet = :id_projet";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    // Supprimer les associations dans la table profils_projets
    $query = "DELETE FROM profils_projets WHERE id_projet = :id_projet";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    // Supprimer le projet
    $query = "DELETE FROM projets WHERE id_projet = :id_projet";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    // Valider la transaction
    $cnx->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    $cnx->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}