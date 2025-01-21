<?php
// Inclure le fichier de connexion
include 'Connection.php';

$conn = new Connection();
$db = $conn->conn;

// Récupérer tous les clients dans la base de données
$query = "SELECT id, firstname, lastname, email, phone, address FROM Clients";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients</title>
    <style>
        /* Styles généraux */
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; padding: 20px; }
        h2 { text-align: center; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }

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
        <a href="ajouter_maintenance.php">Ajouter Client</a>
        <a href="admin_view_client.php"> Clients</a>
        <a href="admin_view_reservations.php">Réservations</a>
        <a href="voir_maintenances.php">Maintenances</a>
        <a href="logout.php">Log out</a>
        
    </div>

    <h2>Liste des Clients Inscrits</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Adresse</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucun client n'a été trouvé.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php
$conn->closeConnection();
?>
</body>
</html>
