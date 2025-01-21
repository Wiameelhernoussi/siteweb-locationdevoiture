<?php
// Inclure le fichier de connexion
include 'Connection.php';

$conn = new Connection();
$db = $conn->conn;

// Requête pour récupérer les réservations des clients avec les informations des voitures
$query = "
    SELECT 
        Clients.id AS client_id, 
        Clients.firstname, 
        Clients.lastname, 
        Clients.email, 
        Cars.marque, 
        Cars.modele, 
        Cars.plaque, 
        Locations.date_debut, 
        Locations.date_fin, 
        Locations.prix_total, 
        Locations.statut
    FROM 
        Locations
    INNER JOIN Clients ON Locations.client_id = Clients.id
    INNER JOIN Cars ON Locations.car_id = Cars.car_id
    ORDER BY Locations.date_debut DESC";

$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Réservations</title>
    <style>
        /* Styles généraux */
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; margin: 0; padding: 0; }
        h2 { text-align: center; margin-top: 20px; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 12px; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }

        /* Navbar */
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
        <a href="ajouter_maintenance.php">Ajouter maintenace</a>
        <a href="admin_view_client.php">Liste des Clients</a>
        <a href="admin_view_reservations.php">Réservations</a>
        <a href="voir_maintenances.php">Maintenances</a>
        <a href="logout.php">log out</a>
    </div>

    <h2>Liste des Réservations</h2>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Email</th>
                <th>Voiture</th>
                <th>Plaque</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Prix total</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['marque']) . " " . htmlspecialchars($row['modele']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['plaque']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_debut']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_fin']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prix_total']) . " €</td>";
                    echo "<td>" . htmlspecialchars($row['statut']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Aucune réservation n'a été trouvée.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php
$conn->closeConnection();
?>
</body>
</html>
