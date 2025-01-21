<?php
include 'Connection.php';

// Se connecter à la base de données
$connection = new Connection();
$conn = $connection->conn;

// Récupérer la liste des maintenances
$query = "
    SELECT m.maintenance_id, c.marque, c.modele, m.date_debut, m.date_fin, m.description
    FROM Maintenance m
    JOIN Cars c ON m.car_id = c.car_id
    ORDER BY m.date_debut DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Maintenances</title>
    <style>
        /* Styles généraux */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; padding: 20px; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        .navbar {
            background-color: #333;
            overflow: hidden;
            margin-bottom: 20px;
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
        <a href="logout.php">Log out</a>
    </div>

    <h2>Liste des Maintenances des Voitures</h2>
    <table>
        <thead>
            <tr>
                <th>ID Maintenance</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['maintenance_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['marque']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['modele']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_debut']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_fin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucune maintenance enregistrée.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
