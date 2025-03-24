// Fonction pour sélectionner un projet
  /*
  function selectProject(projectId) {
    // Mettre à jour l'affichage du projet sélectionné
    const selectedProjectElement = document.getElementById('selected-project');
    selectedProjectElement.textContent = `Boards for Project ${projectId}`;

    // Vous pouvez ajouter ici la logique pour charger les tableaux du projet sélectionné
  }*/

  // Fonction pour sélectionner un projet
/*function selectProject(projectId) {
  // Mettre à jour la valeur du champ caché
  document.getElementById('id_projet').value = projectId;

  // Mettre à jour l'affichage du projet sélectionné (optionnel)
  document.getElementById('selected-project').textContent = `Boards for Project ${projectId}`;

  // Charger les tâches du projet sélectionné
  loadTasks(projectId);
}*/

function selectProject(event, projectId) {
    event.preventDefault(); // Empêcher le comportement par défaut du lien
  
    // Mettre à jour l'URL sans recharger la page
    history.pushState(null, '', `?id_projet=${projectId}`);
  
    // Mettre à jour la valeur du champ caché
    document.getElementById('id_projet').value = projectId;
  
    // Mettre à jour l'affichage du projet sélectionné (optionnel)
    document.getElementById('selected-project').textContent = `Boards for Project ${projectId}`;
  
    // Charger les tâches du projet sélectionné
    loadTasks(projectId);

    // Charger les commentaires
    loadComments(projectId);
  }
  
  document.getElementById('submit-comment').addEventListener('click', function() {
    const commentInput = document.getElementById('comment-input');
    const commentText = commentInput.value.trim();
    const projectId = document.getElementById('id_projet').value;

    if (commentText && projectId) {
        fetch('add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_projet: projectId, comment_text: commentText }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                commentInput.value = ''; // Clear the input
                loadComments(projectId); // Reload comments
            } else {
                alert('Erreur lors de l\'ajout du commentaire : ' + data.message);
            }
        })
        .catch(error => console.error('Erreur :', error));
    } else {
        alert('Veuillez entrer un commentaire et sélectionner un projet.');
    }
});

    // Fonction pour ajouter un nouveau tableau
    document.getElementById('add-board').addEventListener('click', () => {
      const boardTitle = document.getElementById('board-title').value.trim();
      const boardPriority = document.getElementById('board-priority').value;
  
      if (boardTitle) {
        // Ajouter le nouveau tableau à la liste
        const newBoard = {
          id: Date.now(), // Utiliser un timestamp comme ID temporaire
          title: boardTitle,
          status: 'todo',
          priority: boardPriority,
        };
  
        // Ajouter le tableau au projet sélectionné
        selectedProject.boards.push(newBoard);
  
        // Re-afficher les tableaux
        renderBoards();
      } else {
        alert('Veuillez entrer un titre pour le tableau.');
      }
    });
  
    // Fonction pour afficher les tableaux
    function renderBoards() {
      const todoBoardsContainer = document.getElementById('todo-boards');
      const inProgressBoardsContainer = document.getElementById('in-progress-boards');
      const doneBoardsContainer = document.getElementById('done-boards');
  
      // Effacer les tableaux existants
      todoBoardsContainer.innerHTML = '';
      inProgressBoardsContainer.innerHTML = '';
      doneBoardsContainer.innerHTML = '';
  
      // Afficher les tableaux en fonction de leur statut
      selectedProject.boards.forEach(board => {
        const boardCard = document.createElement('div');
        boardCard.className = `board-card ${board.priority}`;
        boardCard.draggable = true;
        boardCard.dataset.id = board.id;
        boardCard.innerHTML = `
          <h3>${board.title}</h3>
          <p>Status: ${board.status === 'todo' ? 'Non fait' : board.status === 'in-progress' ? 'En train de faire' : 'Fait'}</p>
          <div class="board-actions">
            <button onclick="editBoard(${board.id})">Modifier</button>
            <button onclick="deleteBoard(${board.id})">Supprimer</button>
          </div>
        `;
  
        // Ajouter le tableau à la section appropriée
        if (board.status === 'todo') {
          todoBoardsContainer.appendChild(boardCard);
        } else if (board.status === 'in-progress') {
          inProgressBoardsContainer.appendChild(boardCard);
        } else {
          doneBoardsContainer.appendChild(boardCard);
        }
      });
    }
  
    function loadTasks(projectId) {
      console.log('Chargement des tâches pour le projet :', projectId); // Log pour déboguer
      fetch(`get_taches.php?id_projet=${projectId}`)
          .then(response => {
              console.log('Réponse du serveur :', response); // Log pour déboguer
              return response.json();
          })
          .then(data => {
              console.log('Tâches reçues :', data); // Log pour déboguer
              renderTasks(data);
          })
          .catch(error => console.error('Erreur :', error));
  }
  
  /*function renderTasks(tasks) {
      const todoBoards = document.getElementById('todo-boards');
      const inProgressBoards = document.getElementById('in-progress-boards');
      const doneBoards = document.getElementById('done-boards');
  
      // Effacer les tâches existantes
      todoBoards.innerHTML = '';
      inProgressBoards.innerHTML = '';
      doneBoards.innerHTML = '';
  
      // Afficher les tâches en fonction de leur état
      tasks.forEach(task => {
          const taskCard = document.createElement('div');
          taskCard.className = `board-card ${task.tache_priorite}`;
          taskCard.innerHTML = `
              <h3>${task.descripton}</h3>
              <p>Priorité : ${task.tache_priorite}</p>
              <div class="board-actions">
                  <button onclick="editTask(${task.id_tache})">Modifier</button>
                  <button onclick="deleteTask(${task.id_tache})">Supprimer</button>
              </div>
          `;
  
          // Ajouter la tâche à la section appropriée
          if (task.etat === 'todo') {
              todoBoards.appendChild(taskCard);
          } else if (task.etat === 'in-progress') {
              inProgressBoards.appendChild(taskCard);
          } else if (task.etat === 'done') {
              doneBoards.appendChild(taskCard);
          }
      });
  }
  */
  
  /*function renderTasks(tasks) {
      const todoBoards = document.getElementById('todo-boards');
      const inProgressBoards = document.getElementById('in-progress-boards');
      const doneBoards = document.getElementById('done-boards');
  
      // Effacer les tâches existantes
      todoBoards.innerHTML = '';
      inProgressBoards.innerHTML = '';
      doneBoards.innerHTML = '';
  
      // Afficher les tâches en fonction de leur état
      tasks.forEach(task => {
          const taskCard = document.createElement('div');
          taskCard.className = `board-card ${task.tache_priorite}`;
          taskCard.innerHTML = `
              <h3>${task.descripton}</h3>
              <p>Priorité : ${task.tache_priorite}</p>
              <div class="board-actions">
                  <button onclick="editTask(${task.id_tache})">Modifier</button>
                  <button onclick="deleteTask(${task.id_tache})">Supprimer</button>
              </div>
          `;
  
          // Ajouter la tâche à la section appropriée
          if (task.etat === 'todo') {
              todoBoards.appendChild(taskCard);
          } else if (task.etat === 'in-progress') {
              inProgressBoards.appendChild(taskCard);
          } else if (task.etat === 'done') {
              doneBoards.appendChild(taskCard);
          }
      });
      enableDragAndDrop();
  }*/
  
  function renderTasks(tasks) {
      const todoBoards = document.getElementById('todo-boards');
      const inProgressBoards = document.getElementById('in-progress-boards');
      const doneBoards = document.getElementById('done-boards');
  
      // Effacer les tâches existantes
      todoBoards.innerHTML = '';
      inProgressBoards.innerHTML = '';
      doneBoards.innerHTML = '';
  
      // Afficher les tâches en fonction de leur état
      tasks.forEach(task => {
          const taskCard = document.createElement('div');
          taskCard.className = `board-card ${task.tache_priorite}`;
          taskCard.dataset.id = task.id_tache; // Ajouter l'ID de la tâche
          taskCard.draggable = true; // Rendre la tâche draggable
          taskCard.innerHTML = `
              <h3>${task.descripton}</h3>
              <p>Priorité : ${task.tache_priorite}</p>
              <div class="board-actions">
                  <button onclick="editTask(${task.id_tache})">Modifier</button>
                  <button onclick="deleteTask(${task.id_tache})">Supprimer</button>
              </div>
          `;
  
          // Ajouter la tâche à la section appropriée
          if (task.etat === 'todo') {
              todoBoards.appendChild(taskCard);
          } else if (task.etat === 'in-progress') {
              inProgressBoards.appendChild(taskCard);
          } else if (task.etat === 'done') {
              doneBoards.appendChild(taskCard);
          }
      });
  
      // Activer le drag-and-drop après le rendu des tâches
      enableDragAndDrop();
  }
  
  
  /*function moveTask(taskId, newStatus) {
    fetch(`update_task_status.php?id_tache=${taskId}&new_status=${newStatus}`, {
      method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadTasks(document.getElementById('id_projet').value);
      }
    })
    .catch(error => console.error('Erreur :', error));
  }*/
  
  // Activer le drag-and-drop sur les tâches
  function enableDragAndDrop() {
      const tasks = document.querySelectorAll('.board-card');
      const sections = document.querySelectorAll('.board-section');
  
      // Événement de début de glissement
      tasks.forEach(task => {
          task.draggable = true;
          task.addEventListener('dragstart', (event) => {
              event.dataTransfer.setData('text/plain', task.dataset.id); // Stocker l'ID de la tâche
          });
      });
  
      // Événement de survol d'une section
      sections.forEach(section => {
          section.addEventListener('dragover', (event) => {
              event.preventDefault(); // Autoriser le dépôt
          });
  
          // Événement de dépôt
          section.addEventListener('drop', (event) => {
              event.preventDefault();
              const taskId = event.dataTransfer.getData('text/plain'); // Récupérer l'ID de la tâche
              const newStatus = section.id.replace('-section', ''); // Récupérer le nouvel état (todo, in-progress, done)
  
              // Mettre à jour l'état de la tâche dans la base de données
              updateTaskStatus(taskId, newStatus);
          });
      });
  }
  
  // Mettre à jour l'état d'une tâche dans la base de données
  function updateTaskStatus(taskId, newStatus) {
      fetch('update_task_status.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id_tache: taskId, new_status: newStatus }),
      })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Recharger les tâches après la mise à jour
                  const projectId = document.getElementById('id_projet').value;
                  loadTasks(projectId);
              } else {
                  console.error('Erreur lors de la mise à jour de la tâche :', data.error);
              }
          })
          .catch(error => console.error('Erreur :', error));
  }
  
  // Activer le drag-and-drop au chargement de la page
  document.addEventListener('DOMContentLoaded', enableDragAndDrop);
  
  function updateTask(taskId, description, priority) {
      fetch('update_task.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id_tache: taskId, description, priority }),
      })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Recharger les tâches après la mise à jour
                  const projectId = document.getElementById('id_projet').value;
                  loadTasks(projectId);
              } else {
                  console.error('Erreur lors de la mise à jour de la tâche :', data.error);
              }
          })
          .catch(error => console.error('Erreur :', error));
  }
  
  function editTask(taskId) {
      const taskCard = document.querySelector(`.board-card[data-id="${taskId}"]`);
      const description = taskCard.querySelector('h3').textContent;
      const priority = taskCard.className.replace('board-card ', '');
  
      // Afficher un formulaire de modification
      const form = `
          <form id="edit-task-form">
              <input type="text" name="description" value="${description}" required>
              <select name="priority">
                  <option value="red" ${priority === 'red' ? 'selected' : ''}>Important et urgent</option>
                  <option value="orange" ${priority === 'orange' ? 'selected' : ''}>Urgent mais non important</option>
                  <option value="yellow" ${priority === 'yellow' ? 'selected' : ''}>Important mais non urgent</option>
                  <option value="green" ${priority === 'green' ? 'selected' : ''}>Non urgent et non important</option>
              </select>
              <button type="submit">Enregistrer</button>
              <button type="button" onclick="cancelEdit(${taskId})">Annuler</button>
          </form>
      `;
  
      taskCard.innerHTML = form;
  
      // Gérer la soumission du formulaire
      document.getElementById('edit-task-form').addEventListener('submit', (event) => {
          event.preventDefault();
          const newDescription = event.target.description.value;
          const newPriority = event.target.priority.value;
  
          // Mettre à jour la tâche dans la base de données
          updateTask(taskId, newDescription, newPriority);
      });
  }
  
  // Annuler la modification
  function cancelEdit(taskId) {
      const projectId = document.getElementById('id_projet').value;
      loadTasks(projectId); // Recharger les tâches pour annuler la modification
  }
  
  function deleteTask(taskId) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
          fetch('delete_task.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify({ id_tache: taskId }),
          })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Recharger les tâches après la suppression
                      const projectId = document.getElementById('id_projet').value;
                      loadTasks(projectId);
                  } else {
                      console.error('Erreur lors de la suppression de la tâche :', data.error);
                  }
              })
              .catch(error => console.error('Erreur :', error));
      }
  }
  
  
  function deleteProject(event, projectId) {
      event.preventDefault(); // Empêcher le comportement par défaut du bouton
      event.stopPropagation(); // Empêcher la propagation de l'événement
  
      if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ? Toutes les tâches associées seront également supprimées.')) {
          fetch('delete_project.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify({ id_projet: projectId }),
          })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Recharger la page pour mettre à jour la liste des projets
                      window.location.reload();
                  } else {
                      console.error('Erreur lors de la suppression du projet :', data.error);
                  }
              })
              .catch(error => console.error('Erreur :', error));
      }
  }

  


  

    // Initial render
    renderBoards();
  



    function leaveProject(event, id_projet) {
        event.preventDefault(); // Empêcher le comportement par défaut du bouton
    
        // Demander une confirmation avant de sortir du projet
        if (!confirm("Êtes-vous sûr de vouloir quitter ce projet ?")) {
            return;
        }
    
        // Envoyer une requête AJAX au serveur
        fetch('leave_project.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_projet: id_projet }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Supprimer le projet de la liste visuellement
                const projectElement = event.target.closest('li');
                projectElement.remove();
                alert(data.message); // Afficher un message de succès
            } else {
                alert(data.message); // Afficher un message d'erreur
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Une erreur s'est produite lors de la sortie du projet.");
        });
    }

    function loadComments(projectId) {
        fetch(`get_comments.php?id_projet=${projectId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderComments(data.comments);
                } else {
                    console.error('Erreur lors de la récupération des commentaires :', data.message);
                }
            })
            .catch(error => console.error('Erreur :', error));
    }
    
    function renderComments(comments) {
        const commentsList = document.getElementById('comments-list');
        commentsList.innerHTML = ''; // Clear existing comments
        comments.forEach(comment => {
            const commentElement = document.createElement('div');
            commentElement.className = 'comment';
            commentElement.innerHTML = `
                <p>${comment.texte_commentaire}</p>
                <small>Par ${comment.prenom} le ${comment.cree_a}</small>
            `;
            commentsList.appendChild(commentElement);
        });
    }