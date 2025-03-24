<?php
session_start();
include_once("connexion.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    $_SESSION['error_message'] = "Utilisateur non connecté.";
    header("Location: home.php");
    exit();
}

// Récupérer les données du formulaire
$id_profil = $_SESSION['id_profil'];
$id_projet = $_POST['id_projet'] ?? 0;

try {
    // Vérifier si le projet existe
    $stmt = $cnx->prepare("SELECT id_projet FROM projets WHERE id_projet = :id_projet");
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $_SESSION['error_message'] = "Le projet n'existe pas.";
        header("Location: home.php");
        exit();
    }

    // Vérifier si le projet est déjà associé à l'utilisateur
    $query = "SELECT id_profil, id_projet FROM profils_projets WHERE id_profil = :id_profil AND id_projet = :id_projet";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error_message'] = "Ce projet est déjà dans votre liste.";
        header("Location: home.php");
        exit();
    }

    // Associer le projet au profil dans la table `profils_projets`
    $query = "INSERT INTO profils_projets (id_profil, id_projet) VALUES (:id_profil, :id_projet)";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
    $stmt->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['success_message'] = "Le projet a été ajouté avec succès.";
    header("Location: home.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de l'ajout du projet : " . $e->getMessage();
    header("Location: home.php");
    exit();
}
?>