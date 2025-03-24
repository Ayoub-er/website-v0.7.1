<?php
include_once("connexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $question = $_POST['question'] ?? '';
    $email = $_POST['email'] ?? '';
    $type_profil = $_POST['type'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'] ?? '';

    // Validate input
    if (empty($nom) || empty($prenom) || empty($question) || empty($email) || empty($type_profil) || empty($mot_de_passe)) {
        echo "All fields are required.";
        exit;
    }
    if ($mot_de_passe !== $confirm_mot_de_passe) {
        echo "Passwords do not match.";
        exit;
    }

    // Check if email already exists
    try {
        $stmt = $cnx->prepare("SELECT email FROM profils WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Error: Email already exists.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    // Hash the password
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insert into database
    try {
        $stmt = $cnx->prepare("INSERT INTO profils (nom, prenom, email, mot_de_passe, question, type_profil) 
                               VALUES (:nom, :prenom, :email, :mot_de_passe, :question, :type_profil);");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':type_profil', $type_profil);
        $stmt->execute();

        // Redirect to login page
        header("Location: index.html");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 1062) { // Duplicate entry error code in MySQL
            echo "Error: Email already exists.";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Invalid request method.";
}
?>