<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_profil'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion
    exit();
}

$id_profil = $_SESSION['id_profil']; // Récupérer l'ID du profil connecté
?>

<?php
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-message positionm">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Supprimer le message après l'affichage
}

if (isset($_SESSION['success_message'])) {
    echo '<div class="success-message positionm">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Supprimer le message après l'affichage
}
?>

<?php
include_once("connexion.php"); // Inclure le fichier de connexion à la base de données

// Récupérer les projets associés à l'utilisateur
try {
    $query = "
        SELECT p.id_projet, p.nom, p.description, p.temps_création
        FROM projets p
        JOIN profils_projets pp ON p.id_projet = pp.id_projet
        WHERE pp.id_profil = :id_profil
    ";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_profil', $id_profil, PDO::PARAM_INT);
    $stmt->execute();

    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats
} catch (PDOException $e) {
    die("Erreur lors de la récupération des projets : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestionnaire De T&acirc;ches</title>
  <link rel="stylesheet" href="style.css"> <!-- Link to the CSS file -->
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Projets</h2>
    <ul id="projects-list">
    <?php foreach ($projets as $projet): ?>
    <li>
    <a href="#" onclick="selectProject(event, <?= $projet['id_projet'] ?>)">
        <strong><?= htmlspecialchars($projet['nom']) ?></strong><br>
        <small><?= htmlspecialchars($projet['description']) ?></small>
      </a> <br> <br>
      <button onclick="deleteProject(event, <?= $projet['id_projet'] ?>)">Supprimer</button>
      <button onclick="leaveProject(event, <?= $projet['id_projet'] ?>)">Sortir du projet</button> 
      
  <?php endforeach; ?>
    </ul>

    <form id="add-project-form" method="POST" action="add_project.php">
  <input type="text" name="nom" placeholder="Nom du projet" required>
  <textarea name="description" placeholder="Description du projet"></textarea>
  <button type="submit">Cr&eacute;er un projet</button>
</form>


<form id="add-project-form" method="POST" action="add_existant_project.php">
  <input type="text" name="id_projet" placeholder="donner l'identifient du projet" required>
  <button type="submit">Ajouter un projet existant </button>
</form>
  </div>

  <!-- Dashboard -->
  <div class="dashboard">
    <header>
      <h1>Gestionnaire De T&acirc;ches</h1>
      <nav>
        <a href="home.php">Accueil</a>
        <a href="profile.php">Mon profil</a>
        <a href="logout.php">D&eacute;connexion</a>
      </nav>
    </header>

    <div class="container">
      <h2 id="selected-project">My Boards</h2>

      <!-- Form to add a new board -->
      <div class="add-board-form">
        <form action="add_tache.php" method="post">
        <input type="text" id="board-title" name="description_tache" placeholder="Entrer description de t&acirc;che" require>
        <select id="board-priority" name="tache_priorite">
          <option value="Important_et_urgent">Important et urgent</option>
          <option value="Urgent_mais_non_important">Urgent mais non important</option>
          <option value="Important_mais_non_urgent">Important mais non urgent</option>
          <option value="Non_urgent_et_non_important">Non urgent et non important</option>
        </select>
        <input type="hidden" id="id_projet" name="id_projet" value="">
        <button type="submit">Ajouter T&acirc;che</button>
        </form>
      </div>

      <!-- Boards Container -->
      <div class="boards-container">
        <!-- Tâches non faites -->
        <div class="board-section" id="todo-section">
          <h3>Tâches non faites</h3>
          <div class="boards" id="todo-boards">
            <!-- Boards will be dynamically inserted here by JavaScript -->
          </div>
        </div>

        <!-- En train de faire -->
        <div class="board-section" id="in-progress-section">
          <h3>En train de faire</h3>
          <div class="boards" id="in-progress-boards">
            <!-- Boards will be dynamically inserted here by JavaScript -->
          </div>
        </div>

        <!-- Tâches faites -->
        <div class="board-section" id="done-section">
          <h3>Tâches faites</h3>
          <div class="boards" id="done-boards">
            <!-- Boards will be dynamically inserted here by JavaScript -->
          </div>
        </div>
      </div>

      <!-- Button to Change Content Color -->
      <button class="color-change-button" id="change-color">Change Content Color</button>

      <!-- Comments Section -->
      <div class="comments-section">
        <h3>Ajouter un commentaire</h3>
        <textarea id="comment-input" placeholder="&Eacute;crire votre commentaire ici..."></textarea>
        <button id="submit-comment">Soumettre un commentaire</button>

        <!-- Display Comments -->
        <div id="comments-list">
          <!-- Comments will be dynamically inserted here by JavaScript -->
        </div>
      </div>
      <div class="Inviter_profils">
        <form action="invite_user.php" method="post">
          <label for="id_projet">Donner l'identifient du projet</label>
          <input type="text" name="id_projet" require>
          <label for="email">Donner l'e-mail du profil que vous voulez ajouter a votre projet</label>
          <input type="email" name="email" require>
          <button type="submit">Confirmer</button>
          <button type="reset">Annuler</button>
        </form>
      </div>
    </div>
  </div>

  
  <script src="home_script.js"></script>
</body>
</html>