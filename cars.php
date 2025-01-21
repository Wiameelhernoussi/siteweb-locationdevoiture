<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

include 'connection.php'; // Inclure la classe de connexion

$connection = new Connection();
$conn = $connection->conn;

// Gestion des requêtes POST (Ajout/Suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_car'])) {
        // Ajouter une voiture
        $marque = $_POST['marque'];
        $modele = $_POST['modele'];
        $annee = $_POST['annee'];
        $plaque = $_POST['plaque'];
        $prix_par_jour = $_POST['prix_par_jour'];
        $statut = $_POST['statut'];

        // Vérifier si une image a été fournie
        if (!empty($_FILES['photo']['name'])) {
            // Si l'image est téléchargée, on l'enregistre dans le dossier 'uploads'
            $imageName = basename($_FILES['photo']['name']);
            $imagePath = 'uploads/' . $imageName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $imagePath)) {
                $image = $imagePath;
            } else {
                $message = "Erreur lors du téléchargement de l'image.";
            }
        } elseif (!empty($_POST['image_url'])) {
            // Si l'URL est fournie, on l'utilise comme image
            $image = $_POST['image_url'];
        } else {
            $image = NULL; // Aucun chemin ou URL d'image n'a été fourni
        }

        // Insérer la voiture dans la base de données
        $stmt = $conn->prepare("INSERT INTO Cars (marque, modele, annee, plaque, statut, prix_par_jour, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $marque, $modele, $annee, $plaque, $statut, $prix_par_jour, $image);

        if ($stmt->execute()) {
            $message = "Voiture ajoutée avec succès.";
        } else {
            $message = "Erreur lors de l'ajout de la voiture : " . $conn->error;
        }
        $stmt->close();
    }
    if (isset($_POST['delete_car'])) {
        // Supprimer une voiture
        $car_id = $_POST['car_id'];

        $stmt = $conn->prepare("DELETE FROM Cars WHERE car_id = ?");
        $stmt->bind_param("i", $car_id);

        if ($stmt->execute()) {
            $message = "Voiture supprimée avec succès.";
        } else {
            $message = "Erreur lors de la suppression : " . $conn->error;
        }
        $stmt->close();
    }

    if (isset($_POST['edit_car'])) {
        // Modifier une voiture
        $car_id = $_POST['car_id'];
        $marque = $_POST['marque'];
        $modele = $_POST['modele'];
        $annee = $_POST['annee'];
        $plaque = $_POST['plaque'];
        $prix_par_jour = $_POST['prix_par_jour'];
        $statut = $_POST['statut'];

        // Gestion de l'image
        if (!empty($_FILES['photo']['name'])) {
            $imageName = basename($_FILES['photo']['name']);
            $imagePath = 'uploads/' . $imageName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $imagePath)) {
                $image = $imagePath;
            }
        } elseif (!empty($_POST['image_url'])) {
            $image = $_POST['image_url'];
        } else {
            $image = $_POST['current_image']; // Utiliser l'image actuelle si aucune nouvelle image n'est fournie
        }

        // Mise à jour des données
        $stmt = $conn->prepare("UPDATE Cars SET marque = ?, modele = ?, annee = ?, plaque = ?, statut = ?, prix_par_jour = ?, image = ? WHERE car_id = ?");
        $stmt->bind_param("sssssis", $marque, $modele, $annee, $plaque, $statut, $prix_par_jour, $image, $car_id);

        if ($stmt->execute()) {
            $message = "Voiture mise à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour : " . $conn->error;
        }
        $stmt->close();
    }
}

// Récupérer la liste des voitures
$result = $conn->query("SELECT * FROM Cars");
$cars = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}
$connection->closeConnection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Voitures</title>
    <style>
        /* Styles généraux */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; padding: 20px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        form { margin: 20px 0; padding: 15px; background-color: #fff; border: 1px solid #ccc; border-radius: 5px; }
        form div { margin-bottom: 10px; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
        button { padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #218838; }
        .error { color: red; }
        .success { color: green; }
        /* Navbar Styles */
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="ajouter_maintenance.php">ajoter des maintenance</a>
        <a href="voir_maintenances.php">Maintenance</a>
        <a href="admin_view_client.php">Clients</a>
        <a href="admin_view_reservations.php">Réservation</a>
    </div>

    <h1>Gestion des Voitures</h1>
    <?php if (!empty($message)): ?>
        <p class="<?= strpos($message, 'succès') !== false ? 'success' : 'error' ?>"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Formulaire pour ajouter une voiture -->
    <form method="POST" action="" enctype="multipart/form-data">
        <h2>Ajouter une Voiture</h2>
        <div>
            <label for="marque">Marque :</label>
            <input type="text" id="marque" name="marque" required>
        </div>
        <div>
            <label for="modele">Modèle :</label>
            <input type="text" id="modele" name="modele" required>
        </div>
        <div>
            <label for="annee">Année :</label>
            <input type="number" id="annee" name="annee" required>
        </div>
        <div>
            <label for="plaque">Plaque :</label>
            <input type="text" id="plaque" name="plaque" required>
        </div>
        <div>
            <label for="prix_par_jour">Prix par jour :</label>
            <input type="number" step="0.01" id="prix_par_jour" name="prix_par_jour" required>
        </div>
        <div>
            <label for="statut">Statut :</label>
            <select id="statut" name="statut">
                <option value="disponible">Disponible</option>
                <option value="non disponible">Non disponible</option>
            </select>
        </div>
        <div>
            <label for="photo">Photo (télécharger une image) :</label>
            <input type="file" id="photo" name="photo" accept="image/*">
        </div>
        <div>
            <label for="image_url">OU URL de l'image :</label>
            <input type="text" id="image_url" name="image_url" placeholder="Entrez l'URL de l'image">
        </div>
        <button type="submit" name="add_car">Ajouter</button>
    </form>

    <!-- Tableau des voitures -->
    <h2>Liste des Voitures</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Année</th>
                <th>Plaque</th>
                <th>Prix par Jour</th>
                <th>Statut</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cars as $car): ?>
                <tr>
                    <td><?= htmlspecialchars($car['car_id']); ?></td>
                    <td><?= htmlspecialchars($car['marque']); ?></td>
                    <td><?= htmlspecialchars($car['modele']); ?></td>
                    <td><?= htmlspecialchars($car['annee']); ?></td>
                    <td><?= htmlspecialchars($car['plaque']); ?></td>
                    <td><?= htmlspecialchars($car['prix_par_jour']); ?> MAD</td>
                    <td><?= htmlspecialchars($car['statut']); ?></td>
                    <td>
                        <?php if (!empty($car['image'])): ?>
                            <img src="<?= htmlspecialchars($car['image']); ?>" alt="Image de la voiture" style="width:100px; height:auto;">
                        <?php else: ?>
                            Pas d'image
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id']); ?>">
                            <button type="submit" name="delete_car" style="background-color:#dc3545;">Supprimer</button>
                        </form>
                        <form method="POST" action="update_cars.php" style="display:inline;">
                            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id']); ?>">
                            <button type="submit" name="update_car" style="background-color:#28a745;">Modifier</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
