<?php
session_start();
include_once("connexion.php");

if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

$id_profil = $_SESSION['id_profil'];
$data = json_decode(file_get_contents('php://input'), true);
$id_projet = $data['id_projet'] ?? 0;
$comment_text = $data['comment_text'] ?? '';

if ($id_projet && $comment_text) {
    try {
        $query = "INSERT INTO commentaire (id_projet, id_profil, texte_commentaire) VALUES (:id_projet, :id_profil, :texte_commentaire)";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        $stmt->bindParam(':texte_commentaire', $comment_text, PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
}
?>