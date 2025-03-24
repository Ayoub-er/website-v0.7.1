<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour quitter un projet.']);
    exit();
}

$id_profil = $_SESSION['id_profil'];

// Récupérer l'ID du projet depuis la requête POST
$data = json_decode(file_get_contents('php://input'), true);
$id_projet = $data['id_projet'];

// Inclure le fichier de connexion à la base de données
include_once("connexion.php");

try {
    // Commencer une transaction
    $cnx->beginTransaction();

    // Vérifier si l'utilisateur est bien associé au projet
    $query_check = "SELECT id_projet, id_profil FROM profils_projets 
                   WHERE id_projet = :id_projet AND id_profil = :id_profil";
    $stmt_check = $cnx->prepare($query_check);
    $stmt_check->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt_check->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
    $stmt_check->execute();

    if (!$stmt_check->fetch(PDO::FETCH_ASSOC)) {
        $cnx->rollBack();
        echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas associé à ce projet.']);
        exit();
    }

    // Vérifier si le projet est associé à d'autres utilisateurs
    $query_check_other_users = "SELECT COUNT(*) AS count FROM profils_projets 
                              WHERE id_projet = :id_projet AND id_profil != :id_profil";
    $stmt_check_other_users = $cnx->prepare($query_check_other_users);
    $stmt_check_other_users->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
    $stmt_check_other_users->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
    $stmt_check_other_users->execute();
    $result = $stmt_check_other_users->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        // Cas 1: D'autres utilisateurs sont associés - juste supprimer l'association
        $query_delete_user = "DELETE FROM profils_projets 
                            WHERE id_projet = :id_projet AND id_profil = :id_profil";
        $stmt_delete_user = $cnx->prepare($query_delete_user);
        $stmt_delete_user->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_delete_user->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        $stmt_delete_user->execute();
        
        $cnx->commit();
        echo json_encode(['success' => true, 'message' => 'Vous avez quitté le projet avec succès.']);
    } else {
        // Cas 2: C'est le dernier utilisateur - supprimer tout le projet
        
        // 1. D'abord supprimer toutes les tâches associées
        $query_delete_tasks = "DELETE FROM taches WHERE id_projet = :id_projet";
        $stmt_delete_tasks = $cnx->prepare($query_delete_tasks);
        $stmt_delete_tasks->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_delete_tasks->execute();
        
        // 2. Ensuite supprimer l'association du profil
        $query_delete_user = "DELETE FROM profils_projets 
                            WHERE id_projet = :id_projet AND id_profil = :id_profil";
        $stmt_delete_user = $cnx->prepare($query_delete_user);
        $stmt_delete_user->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_delete_user->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
        $stmt_delete_user->execute();
        
        // 3. Enfin supprimer le projet lui-même
        $query_delete_project = "DELETE FROM projets WHERE id_projet = :id_projet";
        $stmt_delete_project = $cnx->prepare($query_delete_project);
        $stmt_delete_project->bindParam(':id_projet', $id_projet, PDO::PARAM_INT);
        $stmt_delete_project->execute();
        
        $cnx->commit();
        echo json_encode(['success' => true, 
                         'message' => 'Vous avez quitté le projet. Le projet et toutes ses tâches ont été supprimés.']);
    }
} catch (PDOException $e) {
    $cnx->rollBack();
    echo json_encode(['success' => false, 
                     'message' => 'Erreur lors de la sortie du projet : ' . $e->getMessage()]);
}
?>