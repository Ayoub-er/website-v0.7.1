<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
  header("Location: login.php"); // Rediriger vers la page de connexion
  exit();
}
$id_profil = $_SESSION['id_profil'];
include_once("connexion.php");
try {
  $query = "
      SELECT nom, prenom, email
      FROM profils
      WHERE id_profil = :id_profil
  ";
  $stmt = $cnx->prepare($query);
  $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
  $stmt->execute();

  $profil = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer les résultats
} catch (PDOException $e) {
  die("Erreur lors de la récupération de profil : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon profil - Gestionnaire De T&acirc;ches</title>
  <link rel="stylesheet" href="style_profile.css"> <!-- Link to the CSS file -->
</head>
<body>
  <header>
    <h1>Gestionnaire De T&acirc;ches</h1>
    <nav>
      <a href="home.php">Accueil</a>
      <a href="profile.php">Mon profil</a>
      <a href="logout.php">D&eacute;connexion</a>
    </nav>
  </header>

  <div class="container">
    <h2>Mon Profil</h2>

    <?php
    if (isset($_SESSION['error'])) {
      echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
      unset($_SESSION['error']); // Supprimer le message après l'affichage
    }
    if (isset($_SESSION['message'])) {
      echo "<p style='color: green;'>" . $_SESSION['message'] . "</p>";
      unset($_SESSION['message']); // Supprimer le message après l'affichage
    }
    
    ?>



    <form action="update_profile.php" method="POST">
      <label for="nom">Nom:</label>
      <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($profil['nom']); ?>" required>

      <label for="prenom">Prénom:</label>
      <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($profil['prenom']); ?>" required>

      <label for="email">E-mail:</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profil['email']); ?>" required>

      <button type="submit">Mettre &agrave; jour</button>
    </form>
  </div>
</body>
</html>