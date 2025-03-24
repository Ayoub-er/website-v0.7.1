<?php
session_start(); // Démarrer la session
include_once("connexion.php"); // Inclure le fichier de connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

// Récupérer les données du formulaire
$description_tache = $_POST['description_tache'] ?? '';
$tache_priorite = $_POST['tache_priorite'] ?? '';
$id_projet = $_POST['id_projet'] ?? ''; // Vous devez passer l'ID du projet depuis le formulaire

// Valider les données
if (empty($description_tache) || empty($tache_priorite) || empty($id_projet)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
    exit();
}

// Insérer la tâche dans la table `tache`
try {
    $query = "
        INSERT INTO taches (id_projet, etat, descripton, tache_priorite)
        VALUES (:id_projet, 'todo', :description_tache, :tache_priorite)
    ";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->bindParam(':description_tache', $description_tache);
    $stmt->bindParam(':tache_priorite', $tache_priorite);
    $stmt->execute();

    // Rediriger vers la page d'accueil ou afficher un message de succès
    header("Location: home.php");
    exit();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la tâche : ' . $e->getMessage()]);
    exit();
}
?>