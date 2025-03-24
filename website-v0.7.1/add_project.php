<?php
session_start();
include_once("connexion.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

// Récupérer les données du formulaire
$nom = $_POST['nom'] ?? '';
$description = $_POST['description'] ?? '';
$id_profil = $_SESSION['id_profil'];

// Insérer le nouveau projet dans la table `projets`
try {
    $query = "INSERT INTO projets (nom, description, temps_création) VALUES (:nom, :description, NOW())";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    $id_projet = $cnx->lastInsertId(); // Récupérer l'ID du projet inséré

    // Associer le projet au profil dans la table `profils_projets`
    $query = "INSERT INTO profils_projets (id_profil, id_projet) VALUES (:id_profil, :id_projet)";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_profil', $id_profil);
    $stmt->bindParam(':id_projet', $id_projet);
    $stmt->execute();

    // Rediriger vers la page d'accueil
    header("Location: home.php");
    exit();
} catch (PDOException $e) {
    die("Erreur lors de l'ajout du projet : " . $e->getMessage());
}
?>