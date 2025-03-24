<?php
session_start();
include_once("connexion.php");

if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

$id_projet = $_GET['id_projet'] ?? 0;

if ($id_projet) {
    try {
        $query = "
            SELECT c.texte_commentaire, c.cree_a, p.prenom 
            FROM commentaire c 
            JOIN profils p ON c.id_profil = p.id_profil 
            WHERE c.id_projet = :id_projet 
            ORDER BY c.cree_a DESC
        ";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'comments' => $comments]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID projet manquant']);
}
?>