<?php
session_start();
include_once("connexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'] ?? '';
    $question = $_POST['question'] ?? '';

    // Validate input
    if (empty($email) || empty($question)) {
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
        $stmt = $cnx->prepare("SELECT id_profil,question FROM profils WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $question_originale = $row['question'];

            // Vérifier si la question correspond
            if ($question == $question_originale) {
                $_SESSION['id_profil'] = $row['id_profil'];
                header("Location: home.php");
                exit;
            } else {
                echo "Invalid email or question.";
                exit;
            }
        } else {
            echo "Invalid email or question.";
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