<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion
    exit();
}

$id_profil_actuel = $_SESSION['id_profil']; // ID de l'utilisateur actuel

// Inclure le fichier de connexion à la base de données
include_once("connexion.php");

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id_projet = $_POST['id_projet'];
    $email = $_POST['email'];

    // Valider les données
    if (empty($id_projet) || empty($email)) {
        header("Location: home.php");
        exit();
    }

    // Vérifier si l'utilisateur actuel est associé au projet
    try {
        $query_check_projet = "
            SELECT id_projet
            FROM profils_projets
            WHERE id_projet = :id_projet AND id_profil = :id_profil
        ";
        $stmt_check_projet = $cnx->prepare($query_check_projet);
        $stmt_check_projet->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_check_projet->bindParam(':id_profil', $id_profil_actuel, PDO::PARAM_INT);
        $stmt_check_projet->execute();

        if (!$stmt_check_projet->fetch(PDO::FETCH_ASSOC)) {
            // L'utilisateur actuel n'est pas associé à ce projet
            header("Location: home.php");
            exit();
        }

        // Vérifier si l'email existe dans la table des profils
        $query_check_email = "
            SELECT id_profil
            FROM profils
            WHERE email = :email
        ";
        $stmt_check_email = $cnx->prepare($query_check_email);
        $stmt_check_email->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_check_email->execute();

        $profil = $stmt_check_email->fetch(PDO::FETCH_ASSOC);

        if (!$profil) {
            header("Location: home.php");
            exit();
        }

        $id_profil_invite = $profil['id_profil'];

        // Vérifier si l'utilisateur est déjà associé à ce projet
        $query_check_invitation = "
            SELECT id_projet, id_profil
            FROM profils_projets
            WHERE id_projet = :id_projet AND id_profil = :id_profil
        ";
        $stmt_check_invitation = $cnx->prepare($query_check_invitation);
        $stmt_check_invitation->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_check_invitation->bindParam(':id_profil', $id_profil_invite, PDO::PARAM_INT);
        $stmt_check_invitation->execute();

        if ($stmt_check_invitation->fetch(PDO::FETCH_ASSOC)) {
            header("Location: home.php");
            exit();
        }

        // Ajouter l'utilisateur au projet
        $query_add_user = "
            INSERT INTO profils_projets (id_projet, id_profil)
            VALUES (:id_projet, :id_profil)
        ";
        $stmt_add_user = $cnx->prepare($query_add_user);
        $stmt_add_user->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_add_user->bindParam(':id_profil', $id_profil_invite, PDO::PARAM_INT);
        $stmt_add_user->execute();

        // Rediriger avec un message de succès
        $_SESSION['message'] = "Utilisateur invité avec succès !";
        header("Location: home.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de l'invitation de l'utilisateur : " . $e->getMessage());
    }
} else {
    // Si le formulaire n'a pas été soumis, rediriger vers la page de profil
    header("Location: home.php");
    exit();
}