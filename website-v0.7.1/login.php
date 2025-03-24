<?php
session_start(); // Démarrer la session
include_once("connexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Validate input
    if (empty($email) || empty($mot_de_passe)) {
        echo "All fields are required.";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Check if email exists and verify password
    try {
        // Récupérer le mot de passe haché de l'utilisateur
        $stmt = $cnx->prepare("SELECT id_profil,mot_de_passe FROM profils WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $mot_de_passe_hash = $row['mot_de_passe'];

            // Vérifier si le mot de passe correspond
            if (password_verify($mot_de_passe, $mot_de_passe_hash)) {
                $_SESSION['id_profil'] = $row['id_profil'];
                header("Location: home.php");
                exit;
            } else {
                echo "Invalid email or password.";
                exit;
            }
        } else {
            echo "Invalid email or password.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid request method.";
}
?>