<?php
session_start();
include 'Connection.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Se connecter à la base de données
$connection = new Connection();
$conn = $connection->conn;

// Récupérer la liste des voitures disponibles pour la sélection
$queryCars = "SELECT car_id, marque, modele FROM Cars WHERE statut = 'disponible'";
$resultCars = $conn->query($queryCars);

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_id = $_POST['car_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $description = $_POST['description'];

    if ($date_debut >= $date_fin) {
        echo "<p class='error'>La date de fin doit être après la date de début.</p>";
    } else {
        $queryInsert = "
            INSERT INTO Maintenance (car_id, date_debut, date_fin, description)
            VALUES (?, ?, ?, ?)";

        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bind_param("isss", $car_id, $date_debut, $date_fin, $description);

        if ($stmtInsert->execute()) {
            echo "<p class='success'>Maintenance ajoutée avec succès.</p>";
        } else {
            echo "<p class='error'>Erreur lors de l'ajout de la maintenance.</p>";
        }

        $stmtInsert->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Maintenance</title>
    <style>
        /* Styles généraux */
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; margin: 0; }
        h2 { text-align: center; margin-top: 20px; }
        form { width: 50%; margin: 20px auto; padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 5px; }
        label { display: block; margin-top: 10px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { margin-top: 15px; background-color: #28a745; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .error { color: red; text-align: center; }
        .success { color: green; text-align: center; }

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
        <a href="ajouter_maintenance.php">Ajouter Maintenance</a>
        <a href="voir_maintenances.php">Maintenances</a>
        <a href="admin_view_client.php">Clients</a>
        <a href="admin_view_reservations.php">Réservations</a>
        <a href="log out.php">Log out</a>
    </div>

    <h2>Ajouter une Maintenance</h2>

    <form action="ajouter_maintenance.php" method="post">
        <label for="car_id">Sélectionner une voiture :</label>
        <select name="car_id" id="car_id" required>
            <?php while ($car = $resultCars->fetch_assoc()) : ?>
                <option value="<?= htmlspecialchars($car['car_id']) ?>">
                    <?= htmlspecialchars($car['marque']) ?> <?= htmlspecialchars($car['modele']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label for="date_debut">Date de début :</label>
        <input type="date" name="date_debut" id="date_debut" required>
        
        <label for="date_fin">Date de fin :</label>
        <input type="date" name="date_fin" id="date_fin" required>
        
        <label for="description">Description de la maintenance :</label>
        <textarea name="description" id="description" rows="4" required></textarea>
        
        <button type="submit">Ajouter Maintenance</button>
    </form>

</body>
</html>
