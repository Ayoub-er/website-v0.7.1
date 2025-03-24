<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion
    exit();
}

$id_profil = $_SESSION['id_profil'];

// Inclure le fichier de connexion à la base de données
include_once("connexion.php");

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    // Valider les données (vous pouvez ajouter des validations supplémentaires ici)
    if (empty($nom) || empty($prenom) || empty($email)) {
        die("Tous les champs sont obligatoires.");
    }

    // Vérifier si l'email existe déjà pour un autre utilisateur
    try {
        $query_check_email = "
            SELECT id_profil
            FROM profils
            WHERE email = :email AND id_profil != :id_profil
        ";
        $stmt_check_email = $cnx->prepare($query_check_email);
        $stmt_check_email->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_check_email->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        $stmt_check_email->execute();

        if ($stmt_check_email->fetch(PDO::FETCH_ASSOC)) {
            // L'email existe déjà pour un autre utilisateur
            $_SESSION['error'] = "Cet email est déjà utilisé par un autre utilisateur.";
            header("Location: profile.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Erreur lors de la vérification de l'email : " . $e->getMessage());
    }

    // Mettre à jour les informations du profil dans la base de données
    try {
        $query_update = "
            UPDATE profils
            SET nom = :nom, prenom = :prenom, email = :email
            WHERE id_profil = :id_profil
        ";
        $stmt_update = $cnx->prepare($query_update);
        $stmt_update->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt_update->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt_update->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_update->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        $stmt_update->execute();

        // Rediriger vers la page de profil avec un message de succès
        $_SESSION['message'] = "Profil mis à jour avec succès !";
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour du profil : " . $e->getMessage());
    }
} else {
    // Si le formulaire n'a pas été soumis, rediriger vers la page de profil
    header("Location: profile.php");
    exit();
}