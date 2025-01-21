<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['user_email'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connection.php';

// Initialisation de la connexion
$connection = new Connection();
$conn = $connection->conn;

$message = "";

// Vérifier si un ID de voiture est fourni
if (isset($_POST['car_id'])) {
    $car_id = (int)$_POST['car_id']; // Cast pour s'assurer qu'il s'agit d'un entier

    // Récupérer les informations de la voiture
    $stmt = $conn->prepare("SELECT * FROM Cars WHERE car_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $stmt->close();

    if (!$car) {
        $message = "Voiture introuvable.";
    }
} else {
    header("Location: cars.php");
    exit();
}

// Gestion de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_car'])) {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $plaque = $_POST['plaque'];
    $prix_par_jour = (float)$_POST['prix_par_jour']; // Cast pour s'assurer que c'est un float
    $statut = $_POST['statut'];
    $image = $_POST['current_image']; // Valeur par défaut

    // Gestion de l'image (téléchargée ou URL)
    if (!empty($_FILES['photo']['name'])) {
        $imageName = basename($_FILES['photo']['name']);
        $imagePath = 'uploads/' . $imageName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $imagePath)) {
            $image = $imagePath;
        } else {
            $message = "Erreur lors du téléchargement de l'image.";
        }
    } elseif (!empty($_POST['image_url'])) {
        $image = $_POST['image_url'];
    }

    // Mettre à jour les informations dans la base de données
    $stmt = $conn->prepare("UPDATE Cars SET marque = ?, modele = ?, annee = ?, plaque = ?, statut = ?, prix_par_jour = ?, image = ? WHERE car_id = ?");
    $stmt->bind_param("sssssdsi", $marque, $modele, $annee, $plaque, $statut, $prix_par_jour, $image, $car_id);

    if ($stmt->execute()) {
        $message = "Voiture mise à jour avec succès.";
        header("Location: cars.php");
        exit();
    } else {
        $message = "Erreur lors de la mise à jour : " . $stmt->error;
    }
    $stmt->close();
}

// Fermer la connexion
$connection->closeConnection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Voiture</title>
    <style>
        /* Styles généraux */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; padding: 20px; }
        h1 { text-align: center; color: #333; }
        form { margin: 20px auto; padding: 20px; background-color: #fff; border: 1px solid #ccc; border-radius: 5px; max-width: 500px; }
        form div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], select { 
            width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px; 
            box-sizing: border-box; 
        }
        button { 
            padding: 10px 20px; background-color: #007bff; color: white; 
            border: none; cursor: pointer; border-radius: 4px; 
            display: block; width: 100%; 
        }
        button:hover { background-color: #0056b3; }
        .error { color: red; }
        .success { color: green; }

        /* Styles pour la navbar */
        nav {
            background-color: #007bff;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px; /* Espace sous la navbar */
        }
        nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Ajout de la barre de navigation -->
    <nav>
        <a href="cars.php">Cars</a>
        <a href="ajouter_maintenance.php">Ajouter Maintenance</a>
        <a href="voir_maintenance.php"> Maintenance</a>
        <a href="admin_view_client.php">Clients</a>
        <a href="admin_view_reservation.php">Réservations</a>
    </nav>

    <h1>Modifier une Voiture</h1>
    
    <?php
    // Si un message d'erreur ou de succès est défini, affichez-le
    if (!empty($message)): ?>
        <p class="<?= strpos($message, 'succès') !== false ? 'success' : 'error' ?>"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['car_id'] ?? ''); ?>">

        <div>
            <label for="marque">Marque :</label>
            <input type="text" id="marque" name="marque" value="<?= htmlspecialchars($car['marque'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="modele">Modèle :</label>
            <input type="text" id="modele" name="modele" value="<?= htmlspecialchars($car['modele'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="annee">Année :</label>
            <input type="number" id="annee" name="annee" value="<?= htmlspecialchars($car['annee'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="plaque">Plaque :</label>
            <input type="text" id="plaque" name="plaque" value="<?= htmlspecialchars($car['plaque'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="prix_par_jour">Prix par jour :</label>
            <input type="number" step="0.01" id="prix_par_jour" name="prix_par_jour" value="<?= htmlspecialchars($car['prix_par_jour'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="statut">Statut :</label>
            <select id="statut" name="statut" required>
                <option value="disponible" <?= (isset($car['statut']) && $car['statut'] == 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                <option value="non disponible" <?= (isset($car['statut']) && $car['statut'] == 'non disponible') ? 'selected' : ''; ?>>Non disponible</option>
            </select>
        </div>
        <div>
            <label for="photo">Photo (télécharger une nouvelle image) :</label>
            <input type="file" id="photo" name="photo" accept="image/*">
        </div>
        <div>
            <label for="image_url">OU URL de l'image :</label>
            <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($car['image'] ?? ''); ?>" placeholder="Entrez l'URL de l'image">
        </div>
        <input type="hidden" name="current_image" value="<?= htmlspecialchars($car['image'] ?? ''); ?>">
        <button type="submit" name="edit_car">Mettre à Jour</button>
    </form>
</body>
</html>
